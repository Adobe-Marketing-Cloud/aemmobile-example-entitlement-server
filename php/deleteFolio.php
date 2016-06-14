<?php
require_once "settings.php";
require_once "utils.php";

// ini_set('display_errors', 1);

$guid = escapeURLData($_POST["guid"]);
$productId = escapeURLData($_POST["productId"]);
$csrfToken = escapeURLData($_POST["csrfToken"]);

$mysqli = new mysqli($db_host, $db_user, $db_password, $db_name);

if (!isValidCsrfToken($mysqli, $guid, $csrfToken)) {
  echo '{"success":false,"description":"Sorry, invalid token."}';
} else if (empty($guid) || empty($productId)) {
  echo '{"success":false,"description":"Sorry, guid and productId are required fields."}';
} else {
  if ($mysqli->connect_errno) {
    echo '{"success":false,"description":"Sorry, unable to connect to the database."}';
  } else {
    // Delete from folios
    if ($stmt = $mysqli->prepare("DELETE FROM folios WHERE guid = ? AND product_id = ?")) {
      if ($stmt->bind_param("ss", $guid, $productId)) {
        $stmt->execute();
        $stmt->close();

        // Delete from folios_for_groups
        $stmt = $mysqli->prepare("DELETE FROM folios_for_groups WHERE product_id = ? AND guid = ?");
        $stmt->bind_param("ss", $productId, $guid);
        $stmt->execute();

        // Delete from folios_for_users
        $stmt = $mysqli->prepare("DELETE FROM folios_for_users WHERE product_id = ? AND guid = ?");
        $stmt->bind_param("ss", $productId, $guid);
        $stmt->execute();

        echo '{"success":true}';
      } else {
        echo '{"success":false,"description":"deleteFolio - Binding folios parameters failed: (' . $mysqli->errno . ')' . $mysqli->error . '"}';
      }

      $stmt->close();
    } else {
      echo '{"success":false,"description":"deleteFolio - Prepare folios failed: (' . $mysqli->errno . ')' . $mysqli->error . '"}';
    }
  }
}

?>