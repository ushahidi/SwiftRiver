SwiftRiver
==========
SwiftRiver is a tool that helps people curate and make sense of large amounts of
data in a short amount of time. The data originates from various channels such as
RSS feeds, Email, Twitter, Facebook, SMS etc

System Requirements
====================
To run the application, the following software packages must be installed your production 
or development environment:

 * PHP 5.2.3 or greater
 * Apache HTTP Server 2.x
 * MySQL Database Server 5.1 or greater
 * Gearman Server (http://gearman.org) - For managing background execution of tasks

Required Extensions
===================
In addition to the software packages outlined above, the following PHP libraries *MUST* be installed and
enabled in your PHP configuration file:
 
  * cURL (http://php.net/curl) for accessing remote sites
  * OpenSSL (http://php.net/openssl) secure comms
  * IMAP (http://php.net/imap) for accessing local and remote mailboxes
  * MySQL (http://php.net/mysql) for database access
  * mcrypt (http://php.net/mcrypt) for cryptography services
  * PCNTL (http://php.net/pcntl) for process control
  * Gearman (http://php.net/gearman) - For communicating with the Gearman server


Installation of Required Extensions
===================================
This section covers the installation of the required extension on OS X and Linux distributions only.

OS X Installation via MacPorts
------------------------------
Run the following commands from the terminal:

        $ sudo port install php5-curl
        $ sudo port install php5-mcrypt
        $ sudo port install php5-mysql
        $ sudo port install php5-pcntl
        $ sudo port install php5-gearman
        $ sudo port install php5-openssl
    
Alternatively, you can specify all the extensions to be installed on one line as follows:
    
        $ sudo port install php5-curl php5-mcrypt php5-mysql php5-pcntl php5-gearman php5-openssl
        
Restart Apache:
    
        sudo <path-to-apachectl> restart

Debian/Ubuntu/Linux Mint Installation
-------------------------------------

        $ sudo apt-get install php5-curl php5-mcrypt php5-mysql php5-pcntl php5-gearman php5-openssl

Restart Apache:

        $ sudo /etc/init.d/apache2 restart

Fedora/CentOS Installation
--------------------------

        $ sudo yum install php5-curl php5-mcrypt php5-mysql php5-pcntl php5-gearman php5-openssl

Restart Apache:

        $ sudo service httpd restart
        