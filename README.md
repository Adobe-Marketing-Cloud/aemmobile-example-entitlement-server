AEM Mobile Example Entitlement Server
----

Using an entitlement service, Experience Manager Mobile apps can support user login and grant access (entitlement) to certain collections based on the sign-in credentials.

Please refer to the following help article for the full walkthrough: [Set up an entitlement service](https://helpx.adobe.com/digital-publishing-solution/help/entitlement-service.html);

__NOTE:__ The example implementation is to be provided as is, Adobe will not provide support on the code, the implementation, or the deployment process. If you have questions about the implementation, please use the AEM Mobile forum.

## Table of Contents

* [Requirement](#requirement)
* [Installation](#installation)
* [MySQL](#mysql)
* [Configuration](#configuration)
* [Website](#website)

## [Requirement](#table-of-contents)

* A server with MySQL and PHP installed.
* A __MySQL__ user account from the server.
* An __AEM Mobile__ user account
* _Prerequisite Knowledge:_ Basic understanding of MySQL and PHP

## [Installation](#table-of-contents)

#### [MySQL](#table-of-contents)

1. Navigate to the __phpMyAdmin__ page on the server with MySQL and PHP installed
2. If prompted to log in, enter the database username and password.
3. Navigate to the page to create new database in the MySQL database, by clicking either the `New` option on the left sidebar or the `database` tab on the top navigation bar.
4. Enter the database name __entitlement\_admin__ (preferably), or a name of your choice, into the `Database name` field on the page.
5. Generate the new database with name __entitlement\_admin__, or the name you entered in step 3, by clicking the `Create` button on the right.
6. Select the new database by clicking the new database name in the list.
7. Navigate to the import page to import the necessary database structure, by clicking the `Import` tab on the top navigation bar.
8. Open the browse window to select the existing database (.sql) by clicking the `Choose File` option.
9. Select the existing database file from the new window, __entitlement_admin.sql__, and click the `Choose` button to confirm.
10. Upload the selected database (.sql) by clicking on the `Go` button.
11. If the database was successfully created, there will be a message similar to the following: _Import has been successfully finished_.

#### [Configuration](#table-of-contents)

* Open the file `settings.php` with an text editor, located in the `Source-Code/php/settings.php`, and update the values for the following parameters:
    * __$db_host__, set this to the MySQL host name
    * __$db_user__, set this to the MySQL account user name, default is root
    * __$db_password__, set this to the MySQL account password, default is root
    * __$db_name__, set this to the MySQL database name, created in step 3 of [MySQL](#mysql) installation process.
    * __$admin_list__, set this with the list of administrative users with access to the entitlement server.

#### [Website](#table-of-contents)

* Upload all the source code to the root directory of a server with MySQL and PHP installed.
* Navigate to the index.html page from the server to see the login screen.
