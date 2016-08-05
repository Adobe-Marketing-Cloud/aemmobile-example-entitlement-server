AEM Mobile Example Entitlement Server
----

Using an entitlement service, Experience Manager Mobile Runtimes can support user login and grant access (entitle) to certain collections based on the sign-in credentials.

This README provides a quick usage guide to the example entitlement server. For full walkthrough, please refer to the following help article: [Set up an entitlement service](https://helpx.adobe.com/digital-publishing-solution/help/entitlement-service.html).

This entitlement service now supports the use of Google or Facebook as the Identity Service provider. To do so, update the __$identity\_provider__ value in [Configuration](#configuration) to either Google or Facebook. In addition, there is the generic identity provider. To use the generic identity provider, leave the __$identity\_provider__ value as is. Please refer to the following help article on [use custom authentication in AEM Mobile apps](https://helpx.adobe.com/digital-publishing-solution/help/identity-providers.html).

__NOTE:__ The example implementation is to be provided as is, Adobe will not provide support on the code, the implementation, or the deployment process. If you have questions about the implementation, please refer to the [AEM Mobile forum](https://forums.adobe.com/community/experiencemanagermobile/).

## Table of Contents

* [Requirement](#requirement)
* [Installation](#installation)
* [MySQL](#mysql)
* [Configuration](#configuration)
* [Server](#server)

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
9. Select the existing database file from the new window, __entitlement\_admin.sql__, and click the `Choose` button to confirm.
10. Upload the selected database (.sql) by clicking on the `Go` button.
11. If the database was successfully created, there will be a message similar to the following: _Import has been successfully finished_.

#### [Configuration](#table-of-contents)

* Open the file `settings.php` with an text editor, located in the `<source-code>/php/settings.php`, and update the values for the following parameters:
    * __$db_host__, set this to the MySQL host name
    * __$db_user__, set this to the MySQL account user name, default is root
    * __$db_password__, set this to the MySQL account password, default is root
    * __$db_name__, set this to the MySQL database name, created in step 3 of [MySQL](#mysql) installation process.
    * __$admin_list__, set this with the list of administrative users with access to the entitlement server.
    * __$identity_provider__, set this with the identity provider name: default, google, or facebook.

#### [Server](#table-of-contents)

* Upload all the source code to the root directory of a server with MySQL and PHP installed.
* Navigate to the index.html page from the server to see the login screen.

****

## Identity Providers

This example entitlement server supports the usage of Google, Facebook, or Generic Identity Provider. When using Google or Facebook, the AEM Mobile Runtime will provide the `authToken` in the Entitlement V2 API: _/entitlements_. When using Generic Identity Provider, the Runtime will redirect users to the provided custom sign in UI.

#### Using Google or Facebook as the Identity Provider

Since there is not a clear way (100%) of telling apart if the authentication token is from Google or Facebook, you will need to set the __$identity_provider__ to either "google" or "facebook", respectively. This value can be found in `<source-code>/php/settings.php`.

#### Using Generic Identity Provider

The generic identity provider can be found in `<source-code>/idp` directory. From the domain that this example entitlement server will be hosted in, the authentication URL would be as follows:

```
http://<domain>/<path-to-source-code>/idp/index.html
```
