<?php
require_once "settings.php";
require_once "utils.php";

//ini_set('display_errors', 1);

$info    = null;
$guid    = null;
$token   = null;
$success = false;

$adobeId  = escapeURLData($_POST['adobeId']);
$password = escapeURLData($_POST['password']);

if (empty($adobeId)) $info = "You must provide an Adobe ID";
else
if (empty($password)) $info = "You must provide a password";
else
{
	$isUserValid = false;
	$md5Password = md5($password);

	// verify that the user credential is valid with the existing list of admin users
	foreach ($admin_list as $admin) {
		if ($admin['username'] === $adobeId && $admin['password'] === $md5Password) {
			$isUserValid = true;
			break;
		}
	}

	if ($isUserValid) {
		$url = "https://edge.adobe-dcfs.com/ddp/issueServer/signInWithCredentials?emailAddress=".urlencode($adobeId)."&password=".urlencode($password);

		$guid = md5($adobeId . $password);

		// Check to see if there is a csrf token for this guid.
		// If there is then reuse it. It is an md5 hash of adobeId and guid.
		$mysqli = new mysqli($db_host, $db_user, $db_password, $db_name);

		if ($stmt = $mysqli->prepare("SELECT token FROM csrf_tokens WHERE guid = ?")) {
			$stmt->bind_param("s", $guid);
			$stmt->execute();
			$stmt->bind_result($token);
			$stmt->fetch();

			if (!$token) {
				// create a new csrf token. This isn't stored in the session since multiple servers will be used.
				$token = md5($adobeId . $guid);

				$stmt = $mysqli->prepare("INSERT INTO csrf_tokens (guid, token) VALUES (?, ?)");
				$stmt->bind_param("ss", $guid, $token);
				$stmt->execute();
			}

			$success = true;
		} else {
			$info = "The MySQL database is missing, please set that up first.";
		}
	} else {
		$info = "The user credentials are invalid, please verify and try again.";
	}
}

header('Access-Control-Allow-Origin: *');
echo "{\"success\": " . var_export($success, true) . ", \"guid\": \"$guid\", \"info\": \"$info\", \"csrfToken\":\"" . $token . "\"}";
?>
