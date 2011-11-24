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
In addition to the software packages outlined above, the following PHP libraries must be installed and
enabled in your PHP configuration file:
 
  * cURL (http://php.net/curl) for accessing remote sites
  * imap (http://php.net/imap) for accessing local and remote mailboxes
  * MySQL (http://php.net/mysql) for database access
  * mcrypt (http://php.net/mcrypt) for cryptography services
  * Gearman (htto://php.net/gearman) - For communicating with the Gearman server
