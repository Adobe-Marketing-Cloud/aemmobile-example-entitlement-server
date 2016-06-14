<?php
require_once "settings.php";
require_once "utils.php";

$guid = escapeURLData($_POST["guid"]);
$pubName = escapeURLData($_POST["pubName"]);
$folioNumber = escapeURLData($_POST["folioNumber"]);
$productId = escapeURLData($_POST["productId"]);
$pubDate = escapeURLData($_POST["pubDate"]);
$csrfToken = escapeURLData($_POST["csrfToken"]);

if (empty($guid) || empty($pubName) || empty($folioNumber) || empty($productId) || empty($pubDate)) {
  echo '{"success":false,"description":"Sorry, guid, product label, product description, product ID and availability date are required fields."}';
} else {
  $mysqli = new mysqli($db_host, $db_user, $db_password, $db_name);

  if (!isValidCsrfToken($mysqli, $guid, $csrfToken)) {
    echo '{"success":false,"description":"Sorry, invalid token."}';
  } else {
    if ($mysqli->connect_errno) {
        echo '{"success":false,"description":"Sorry, unable to connect to the database."}';
    } else {
      if ($stmt = $mysqli->prepare("INSERT INTO folios (guid, pub_name, folio_number, product_id, pub_date) VALUES (?, ?, ?, ?, ?)")) {
        if ($stmt->bind_param("sssss", $guid, $pubName, $folioNumber, $productId, $pubDate)) {
          $stmt->execute();
          echo '{"success":true, "id":' . $stmt->insert_id . '}';
        } else {
          echo '{"success":false,"description":"addFolio - Binding insert parameters failed: (' . $mysqli->errno . ')' . $mysqli->error . '"}';
        }
        $stmt->close();
      } else {
        echo '{"success":false,"description":"addFolio - Prepare insert failed: (' . $mysqli->errno . ')' . $mysqli->error . '"}';
      }
    }
  }
}
?>