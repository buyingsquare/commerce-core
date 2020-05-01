<?php

/**
 * Common resource configuration file
 */

$_ENV = \Dotenv\Dotenv::create(__DIR__.'/../')->load();

return array(

	// default database configuration
	'db' => array(

		// database adapter (e.g 'mysql', 'pgsql', etc.)
		'adapter' => $_ENV['DB_CONNECTION'],

		// database server name, IP address or path to local (socket) file
		'host' => $_ENV['DB_HOST'],

		// database server port
		'port' => $_ENV['DB_PORT'],

		// name of the database to use
		'database' => $_ENV['DB_DATABASE'],

		// name of the database account used for connecting
		'username' => $_ENV['DB_USERNAME'],

		// secret password
		'password' => $_ENV['DB_PASSWORD'],

		// SQL statements executed immediately after connecting to the database server
		'stmt' => array( "SET SESSIOn sort_buffer_size=2097144; SET NAMES 'utf8'; SET SESSION sql_mode='ANSI'" ),

		// number of concurrent database connections while processing one request
		'limit' => 2,

		// use persistent connections and connection pooling
		'opt-persistent' => false,
	),
	// If using the order domain in an other database or database server
	// 'db-order' => array('adapter' => 'mysql', 'host' => 'localhost', ....)

	// default message queue configuration
	'fs' => array(

		// file system adapter
		'adapter' => 'Standard',

		// base directory for file system view
		'basedir' => '.',
	),

	// default message queue configuration
	'mq' => array(

		// message queue adapter
		'adapter' => 'Standard',

		// use database configuration from resource "db"
		'db' => 'db',
	)
);
