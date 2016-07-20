<?php
/**
 * @param {String} $db_host - The mysql host name.
 * @example $db_host = "localhost";
 * Please uncomment below and input actual value.
 */
// $db_host = "localhost";

/**
 * @param {String} $db_user - The mysql user name.
 * @example $db_user = "root";
 * Please uncomment below and input actual value.
 */
// $db_user = "root";

/**
 * @param {String} $db_password - The mysql password.
 * @example $db_password = "root";
 * Please uncomment below and input actual value.
 */
// $db_password = "root";

/**
 * @param {String} $db_name - The mysql database name.
 * @example $db_name = "entitlement_admin";
 * Only change below if the database name was different than 'entitlement_admin'.
 */
$db_name = "entitlement_admin";

/**
 * @param {Array} $admin_list - The list of admin user credentials
 * @example $admin_list = array(array('username' => 'username', 'password' => 'password'));
 * This is a list of administrators that will be allowed to sign into the entitlement service.
 * Please DO NOT input plain user password into the array, always MD5 hash it.
 * i.e. "123" after MD5 hash is "202cb962ac59075b964b07152d234b70"
 * For more info, please see http://php.net/manual/en/function.md5.php
 * Please uncomment below and input actual value.
 */
// $admin_list = array(
// 	array('username' => 'admin1', 'password' => '202cb962ac59075b964b07152d234b70'), // Admin user 1
// 	array('username' => 'admin2', 'password' => '202cb962ac59075b964b07152d234b70'), // Admin user 2
// );

/**
 * @param {String} $identity_provider_type - The type of identity provider.
 * @example $identity_provider = 'default'; // set default identity provider
 * @example $identity_provider = 'google'; // set Google as the identity provider
 * @example $identity_provider = 'facebook'; // set Facebook as the identity provider
 * Only change below if using Google or Facebook as the identity provider.
 */
$identity_provider = 'default';

?>