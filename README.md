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
 * APC (http://php.net/apc) for in memory caching
 * GD (http://php.net/gd) for image processing


Installation of Required Extensions
===================================

This section covers the installation of the required extensions on OS X and Linux distributions only.

OS X Installation via MacPorts
------------------------------
Run the following commands from the terminal:

	$ sudo port install php5-curl
	$ sudo port install php5-mcrypt
	$ sudo port install php5-mysql
	$ sudo port install php5-pcntl
	$ sudo port install php5-openssl
	$ sudo port install php5-apc
	$ sudo port install php5-gd

Restart Apache:

	sudo <path-to-apachectl> restart

Debian/Ubuntu/Linux Mint Installation
-------------------------------------

Run the following command in Terminal:

	$ sudo apt-get install php5-curl php5-mcrypt php5-mysql php5-gd

Restart Apache:

	$ sudo service apache2 restart

Fedora/CentOS Installation
--------------------------

	$ sudo yum install php5-curl php5-mcrypt php5-mysql php5-pcntl php5-openssl php5-apc php5-gd

Restart Apache:

	$ sudo service httpd restart


Configuring the crawler
=======================

Add the following entries to your crontab to schedule crawling every 30 
minutes and post processing every 15 minutes respectively:

	0,30 * * * * cd <app home>; php5 index.php --uri=crawler >> application/logs/crawl.log 2>&1
	0,15,30,45 * * * * cd <app home>; php5 index.php --uri=process >> application/logs/process.log 2>&1


Configuring river maintenance
=============================

River maintenance involves checking which rivers have expired and are about to expire and sending 
out notifications to their owners. To schedule maintenance to run every day at midnight, add the
following entries to your crontab:

    * 0 * * * cd <app home>; php5 index.php --uri=maintenance >> application/logs/maintenance.log 2>&1
