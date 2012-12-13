Unit Testing
============
The SwiftRiver web application uses the [PHPUnit testing framework](http://www.phpunit.de/manual/current/en/index.html).

System Requirements
====================
To run the application, the following software packages must be installed your production 
or development environment:

 * PHPUnit 3.7+
 * phpunit/DbUnit extension

Setting up your environment
===========================
To install PHPUnit and the required extensions, install PHP Pear on your system then run the following:

    $ pear config-set auto_discover 1
    $ pear install pear.phpunit.de/PHPUnit
    $ pear install phpunit/DbUnit

Running the tests
=================
* Create a unit test database and provide the connection details in your application/config/database.php file.
* Run the following after setting the paths to bootstrap.php and test.php on your system:

    $ phpunit --colors --bootstrap=/path/to/application/tests/bootstrap.php --exclude-group=kohana /path/to/modules/unittest/tests.php 

Known Issues
============
* [Issue 4507](https://github.com/kohana/unittest/pull/24) with the kohana/unittest module.