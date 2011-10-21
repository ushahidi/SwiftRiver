![Diagram](https://github.com/ushahidi/Swiftriver_v2_/raw/master/modules/sweeper_guide/media/guide/img/sweeper_logo.png)

# Swiftriver
Swiftriver is a tool that helps people discover, analyze and present information from disparate sources in meaningful ways - timelines, maps, graphs & charts etc
## Support

* [Google Groups](http://groups.google.com/group/swiftriver?hl=en)
* [SwiftRiver Skype Chat](skype:?chat&blob=MsCLduODseLjMKC9Fv5ktYWZsvzEt1ydNU4PTHjQSfBoQebEjMH-NHZKYzomXPVFUuwq4SXGIVqA2HS4PNgSvKkxknDzdKllVl9Cl45TKSlr5-TKl3ywAPAeUj4s2a7qUOe_eqEcIQiuB67LwgGyL7m6hcUzJHfIGmLnoJN3c0LMlgXACRqL7WGgIgzCBg)

## General Overview
Sweeper is broken down into projects, stories and items. The application has a feed builder that generates the item stream. Items can then be clustered together to form stories. Sweeper can extract the following entities from the content in items:

* Sources
* Tags
* Dates
* Places
* Links
* Attachments (jpg,gif,png,pdf,mov etc..)

![Diagram](https://github.com/ushahidi/Swiftriver_v2_/raw/master/modules/sweeper_guide/media/guide/img/sweeper_overview.png)

## Requirements

* Server with Unicode support
* Server with Unicode support
* PHP version >= 5.2.3 and the following extensions:
    - PCRE (http://php.net/pcre) must be compiled with –enable-utf8 and –enable-unicode-properties for UTF-8 functions to work properly.
    - iconv (http://php.net/iconv) is required for UTF-8 transliteration.
    - mcrypt (http://php.net/mcrypt) is required for encryption.
    - SPL (http://php.net/spl) is required for several core libraries
    - mbstring (http://php.net/mbstring) which speeds up Kohana's UTF-8 functions.
    - cURL (http://php.net/curl) which is used to access remote sites.

## Installation

* ####Download and extract Sweeper
    To unzip/extract the archive on a typical Unix/Linux command line:
    
        tar -xvf Swiftriver_v2_-xxxx.tar.gz
    
    or in the case of a zip file:

        unzip Sweeper-xxxx.zip
    
    This will create a new directory Sweeper-xxxx containing all the Sweeper platform files and directories - Move the contents of this directory
    into a directory within your webserver's document root or your public HTML directory.

* ####Ensure the following directories are writable (i.e. have their permission values set to 777)
    - application/config
    - application/cache
    - application/logs
    
    On Unix/Linux, you can change the permissions as follows:

        cd path-to-webserver-document-root-directory
        chmod -R 777 application/config
        chmod -R 777 application/cache
        chmod -R 777 application/logs
        
    #####NOTE: The process of configuring file permissions is different for various operating systems. Here are some helpful links about permissions for the Windows (http://support.microsoft.com/kb/308419) and Unix (http://www.washington.edu/computing/unix/permissions.html) operating systems.

* ####Create the Sweeper database
    Sweeper stores all its information in a database. You must therefore create this database in order to install Sweeper. This is done as follows:
    
        mysqladmin -u '[username]' -p create '[databasename]'
    
    MySQL will prompt for the password for the <username> database password and then create the initial database files. Next, you must log in and set the 
    database access rights:
    
        mysql -u 'username' -p
    
    Again, you will be prompted for the 'username' database password. At the MySQL prompt, enter the following command:
    
        GRANT SELECT, INSERT, DELETE, UPDATE, CREATE, DROP, ALTER, INDEX on 'databasename'.* 
        TO 'username'@'localhost' IDENFIFIED BY 'password';
    
    Where:
    - 'databasename' is the name of your database
    - 'username@localhost' is the name of your MySQL account
    - 'password' is the password required for that username

    #####NOTE: Your account must have all the privileges listed above in order to run Sweeper on your webserver.

* ####Sweeper doesn't yet have an installer. Until then, please import the required database tables with the following command:
    mysql -u '[username]' -p '[password]' '[database]' < install/sql/sweeper.sql

* ####Finally, to access Sweeper, the default username and password are sweeperadmin/password

## Credits
#### Contributors
* David Kobia
* Heather Ford
* Chris Blow
* Ed Bice
* Anas Qtiesh

#### Organizations
* [Ushahidi](http://www.ushahidi.com)
* [Meedan](http://news.meedan.net/index.php?page=static&action=about)