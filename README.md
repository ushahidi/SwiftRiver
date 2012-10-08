SwiftRiver
==========
SwiftRiver is a free and open source platform for helping people to make sense of large amounts of information in a short amount of time. It is a mission to democratize access to the tools used to make sense of data - discover information that is authentic, accurate and above all, relevant - by providing the following capabilities:

* Gathering and filtering of information from a variety of channels e.g. RSS, Email, SMS, Twitter etc
* Drawing insights from the collected information
* Allowing people to create buckets of information using their own expectations of authority and accuracy as opposed to popularity.

System Requirements
====================
To run the application, the following software packages must be installed your production 
or development environment:

 * PHP 5.3 or greater
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


Get the latest code from GitHub
===============================
* Get the latest (stable) code from the `master` branch on our [GitHub](https://github.com/ushahidi/SwiftRiver) repository. To do this, Git has to be installed on your system.

		git clone --recursive git://github.com/ushahidi/SwiftRiver.git

	This will clone the code into a newly created `SwiftRiver` directory. Copy this directory to your web server's document root 
	or `public_html` directory.

* Create the following directories and ensure they are writable:

		application/cache
		application/logs

* Create the configuration files
	Create a `.php` for each of the `.php.template` files in your `application/config/` directory.
	This can be done via the command line as follows:

		cp application/config/site.php.template application/config/site.php
		cp application/config/database.php.template application/config/database.php
		cp application/config/cache.php.template application/config/cache.php
		cp application/config/auth.php.template application/config/auth.php
		cp application/config/cookie.php.template application/config/cookie.php

	Alternatively, you can run the following one-liner from the command line:

		for config in application/config/*.php.template; do cp $config application/`basename ${config} .php.template`.php; done

* Create the database
	Log in to your MySQL server:
	
		mysql -u <username> -p

	MySQL will prompt you for the password associated with `<username>`. Once logged in, run the following command at the MySQL
	prompt to create the database that shall host the data for your SwiftRiver installation.

		create database <swiftriver-database>;

	Where `<swiftriver-databasename>` is the name of your SwiftRiver database.
	__NOTE:__ `<username>` should be an account that has privileges to create a database on your MySQL server.

	Next, run the following command (also at the MySQL prompt):

		GRANT CREATE ROUTINE, CREATE VIEW, ALTER, SHOW VIEW, CREATE, ALTER ROUTINE, EVENT, INSERT, SELECT, DELETE,
		TRIGGER, GRANT OPTION, REFERENCES, UPDATE, DROP, EXECUTE, LOCK TABLES, CREATE TEMPORARY TABLES, 
		INDEX ON `<swiftriver-database>`.* TO `<swiftriver-user>`@'localhost' IDENTIFIED BY `<swiftriver-user-password>`;

	Where:
	- `<swiftriver-database>` is the name of your SwiftRiver database
	- `<swiftriver-user>` is the username to use when connecting to your SwiftRiver database
	- `<swiftriver-user-password>` is the password associated with the user account to be used for connecting to your SwiftRiver database

* Run the schema setup script
	The schema setup script is located at `/path/to/SwiftRiver/install/sql/swiftriver.sql`.

		mysql <swiftriver-database> -u <swiftriver-user> -p < /path/to/SwiftRiver/install/sql/swiftriver.sql

	__NOTE__: If you get the following error:

		ERROR 1418 (HY000) at line 801: This function has none of DETERMINISTIC, NO SQL, or READS SQL DATA in its declaration 
		and binary logging is enabled (you might want to use the less safe log_bin_trust_function_creators variable)

	it's because MySQL gets paranoid when you attempt to create a function that is not deterministic and/or attempts to modify data.
	See http://dev.mysql.com/doc/refman/5.5/en/stored-programs-logging.html for a more detailed explanation. 
	To circumvent this restriction, set the `log_bin_trust_function_creators` system variable to 1. You can do this from the MySQL prompt as follows:

		SET GLOBAL log_bin_trust_function_creators = 1;

* Update the database configuration
	Update your database configuration file (`application/config/database.php`) with the __values__ you used for the 
	`swiftriver-` parameters in the preceding steps. The updated database configuration should read as follows:

		return array
		(
			'default' => array
			(
				'type'       => 'mysql',
				'connection' => array(
					'hostname'   => 'localhost',
					'database'   => '<swiftriver-database>',
					'username'   => '<swiftriver-user>',
					'password'   => '<swiftriver-user-password>',
					'persistent' => FALSE,
				),
				'table_prefix' => '',
				'charset'      => 'utf8',
				'caching'      => TRUE,
				'profiling'    => TRUE,
			)
		);

Point your browser to the URL of your SwiftRiver installation. At the login prompt, use `admin` and `password` for
the username and password respectively. 

__NOTE:__ Change the default password after the initial login



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


For additional information, see the [Installing SwiftRiver](https://wiki.ushahidi.com/display/WIKI/Installing+SwiftRiver) section on the Ushahidi wiki.