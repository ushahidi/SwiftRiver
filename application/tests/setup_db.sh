#!/bin/sh

SQL_FILE="$1"
MYSQL_HOST="$2"
MYSQL_DB="$3"
MYSQL_USER="$4"
MYSQL_PASS="$5"
 
if [ $# -lt 4 ]
then
	echo "Usage: $0 {Install-Script} {MySQL-Host-Name} {MySQL-Database-Name} {MySQL-User-Name} {MySQL-User-Password}"
	echo "Drops all tables from a MySQL"
	exit 1
fi

MYSQL_OPTIONS="-h $MYSQL_HOST -u $MYSQL_USER $MYSQL_DB"

if [ $MYSQL_PASS ]; then
	MYSQL_OPTIONS="$MYSQL_OPTIONS -p$MYSQL_PASS"
fi

TABLES=$(mysql $MYSQL_OPTIONS -e 'show tables' | awk '{ print $1}' | grep -v '^Tables' )

for t in $TABLES
do
	echo "Dropping $t table from $MDB database..."
	mysql $MYSQL_OPTIONS -e "drop table $t"
done

if [ -f $SQL_FILE ]; then
mysql $MYSQL_OPTIONS  < $SQL_FILE
fi
