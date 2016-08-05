<?php
/**
 *	Dispatcher for the following calls
 *	- /services/index.php/SignInWithCredentials
 *	- /services/index.php/entitlements
 *	- /services/index.php/RenewAuthToken
 */

session_start();

require_once "../php/settings.php";
require_once "../php/utils.php";

$path_info = $_SERVER["PATH_INFO"];
$call = substr($path_info, 1);

if ($call == "SignInWithCredentials" || $call == "RenewAuthToken" || $call == "entitlements") {
	$mysqli = new mysqli($db_host, $db_user, $db_password, $db_name);
	switch ($call) {
		case "SignInWithCredentials":
			SignInWithCredentials($mysqli);
			break;
		case "RenewAuthToken":
			RenewAuthToken($mysqli);
			break;
		case "entitlements":
			entitlements($mysqli, $identity_provider);
			break;
	}
} else {
	handleDefaultCall();
}

function SignInWithCredentials($mysqli) {
	$requestBody = file_get_contents('php://input');
	$xml = simplexml_load_string($requestBody);
	$emailAddress = escapeURLData($xml->emailAddress);
	$password = escapeURLData($xml->password);
	// gets the appId either from same XML (used by custom IDP) or from request
	$appId = ($xml->appId) ? escapeURLData($xml->appId) : escapeURLData($_REQUEST["appId"]);

	// Check for a matching guid before proceeding.
	$stmt = $mysqli->prepare("SELECT guid FROM app_ids WHERE app_id = ?");
	$stmt->bind_param("s", $appId);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($guid);
	$stmt->fetch();

	if (!$guid) {
		returnErrorResponse();
		exit;
	}

	// Get the salt value for this guid and name.
	$stmt = $mysqli->prepare("SELECT salt, password FROM users WHERE guid = ? AND name = ?");
	$stmt->bind_param("ss", $guid, $emailAddress);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($salt, $hashedPassword);
	$stmt->fetch();

	if ($stmt->num_rows == 0) { // Invalid name so no salt.
		returnErrorResponse();
		exit;
	}

	if (generateHash($password, $salt) != $hashedPassword) { // password does not match the hashed password.
		returnErrorResponse();
		exit;
	} else {
		// Create and insert a new authToken.
		$authToken = createAuthToken($emailAddress . $appId);

		$stmt = $mysqli->prepare("UPDATE users SET auth_token = ? WHERE guid = ? AND name = ? ");
		$stmt->bind_param("sss", $authToken, $guid, $emailAddress);
		$stmt->execute();

		// Output the success xml.
		header("Content-Type: application/xml");
		$xml = simplexml_load_string("<result/>");
		$xml->addAttribute("httpResponseCode", '200');
		$xml->addChild("authToken", $authToken);
		echo $xml->asXML();
	}
}

function RenewAuthToken($mysqli) {
	$authToken = escapeURLData($_REQUEST["authToken"]);

	$stmt = $mysqli->prepare("SELECT id FROM users WHERE auth_token = ?");
	$stmt->bind_param("s", $authToken);
	$stmt->execute();
	$stmt->store_result();

	if ($stmt->num_rows > 0) { // authToken is valid so send back the same one.
		$xml = simplexml_load_string("<result />");
		$xml->addAttribute("httpResponseCode", "200");
		$xml->addChild("authToken", $authToken);
	} else {
		$xml = simplexml_load_string("<result />");
		$xml->addAttribute("httpResponseCode", "401");
	}

	header("Content-Type: application/xml");
	echo $xml->asXML();
}

function entitlements($mysqli, $identity_provider) {
	// retrieves the authToken from the request
	$authToken = escapeURLData($_REQUEST["authToken"]);

	// caches the authToken and userId pair,
	// this way you don't have to query for the userId every time
	if (isset($_SESSION[$authToken])) {
		$userId = $_SESSION[$authToken];
	} else {
		// gets the userToken depending on the identity provider setup
		switch ($identity_provider) {
			case 'facebook': // Facebook as identity provider
				$response = file_get_contents('https://graph.facebook.com/me?fields=email&access_token=' . urlencode($authToken));
				$facebookUser = json_decode($response, true);
				$userToken = $facebookUser['email'];
				$stmt = $mysqli->prepare("SELECT id FROM users WHERE name = ?");
				break;
			case 'google': // Google as identity provider
				$response = file_get_contents('https://www.googleapis.com/oauth2/v1/userinfo?access_token=' . urlencode($authToken));
				$googleUser = json_decode($response, true);
				$userToken = $googleUser['email'];
				$stmt = $mysqli->prepare("SELECT id FROM users WHERE name = ?");
				break;
			default: // default identity provider
				$userToken = $authToken;
				$stmt = $mysqli->prepare("SELECT id FROM users WHERE auth_token = ?");
				break;
		}

		$stmt->bind_param("s", $userToken);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($userId);
		$stmt->fetch();
	}

	if ($userId) {
		// caches the authToken and userId pair
		$_SESSION[$authToken] = $userId;
		session_write_close();

		// Create the XML.
		$xml = simplexml_load_string("<result/>");
		$xml->addAttribute("httpResponseCode", "200");
		$entitlements = $xml->addChild("entitlements");

		// Get the groups for this user.
		$stmt = $mysqli->prepare("SELECT group_id FROM groups_for_users WHERE user_id = ?");
		$stmt->bind_param("i", $userId);
		$stmt->execute();
		$stmt->bind_result($groupId);
		$stmt->store_result();

		if ($stmt->num_rows > 0) {
			// Construct the "in"
			$groupIds = "";
			while ($stmt->fetch()) {
				$groupIds .= "," . $groupId;
			}

			// Remove the leading comma
			$groupIds = ltrim($groupIds, ",");

			$select = "SELECT product_id FROM folios_for_groups WHERE group_id IN ($groupIds) UNION SELECT product_id FROM folios_for_users WHERE user_id = ?";
		} else {
			$select = "SELECT product_id FROM folios_for_users WHERE user_id = ?";
		}

		$stmt->close();

		// Get the entitlements for the group and user.
		$stmt = $mysqli->prepare($select);
		$stmt->bind_param("i", $userId);
		$stmt->execute();
		$stmt->bind_result($productId);
		$stmt->store_result();

		while($stmt->fetch()) {
			$entitlements->addChild("productId", $productId);
		}

		header("Content-Type: application/xml");
		echo $xml->asXML();
	} else {
		returnErrorResponse();
	}
}

function handleDefaultCall() {
	setXMLHeader();
	echo "<error>No such call</error>";
}

function setXMLHeader() {
	Header("Content-Type: application/xml; charset=utf-8");
}

?>
