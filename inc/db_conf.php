<?php

	define('DB_HOST', 'localhost');
	define('DB_NAME', 'top10kino');
	define('DB_USER', 'root');
	define('DB_PASS', '');

	/* Standard ezSQL Libs (github.com/jv2222/ezSQL) */
	include_once "ez_sql/ez_sql_core.php";
	include_once "ez_sql/ez_sql_mysql.php";
	
	$db = new ezSQL_mysql( DB_USER, DB_PASS, DB_NAME, DB_HOST );
	
	$db->query("SET NAMES utf8");
	$db->query('SET CHARACTER SET utf8');
	$db->query("SET CHARACTER_SET_CONNECTION = utf8");
	$db->query("SET COLLATION_CONNECTION = utf8_general_ci");
	$db->query("SET SQL_MODE = ''");

?>