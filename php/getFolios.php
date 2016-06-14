<?php
header('Content-Type: text/xml');
header('Access-Control-Allow-Origin: *');

require_once "settings.php";
require_once "utils.php";

$default = '<results><issues/></results>';
$simpleXml = new SimpleXMLElement($default);
$mysqli = new mysqli($db_host, $db_user, $db_password, $db_name);
$guid = isset($_GET['accountId']) ? $_GET['accountId'] : false;

// attempts to query the database for the list of possible product info
if ($guid && $stmt = $mysqli->prepare("SELECT pub_name, folio_number, product_id, pub_date FROM folios WHERE guid = ? ORDER BY pub_name")) {
  if ($stmt->bind_param("s", $guid)) {
    $stmt->execute();
    $stmt->bind_result($pub_name, $folio_number, $product_id, $pub_date);
    $stmt->store_result();
    // if the list of product info is available
    if ($stmt->num_rows > 0) {
      // compiles the list of product info
      while($stmt->fetch()) {
        $folio = $simpleXml->issues->addChild('issue');
        $folio->addAttribute('productId', $product_id);
        $folio->addChild('magazineTitle', $pub_name);
        $folio->addChild('issueNumber', $folio_number);
        $folio->addChild('publicationDate', $pub_date);
      }
    }
  }
  $stmt->close();
}

// returns the list of product info if available
echo $simpleXml->asXML();

?>