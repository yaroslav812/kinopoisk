<?php
define( 'URL_TOP_10', 'http://www.kinopoisk.ru/top/' );
define( 'NUM_LOAD_MOVIES' , 100); /* Кол-во загружаемых топ фильмов для записи (1 - 250) */

include_once './inc/db_conf.php'; //Load MySQL class and set GLOBAL $db (Object)
include_once './inc/class_topmovies.php';

error_reporting(E_ALL);

//--- main code												// При проблемах с кодировками попробовать включить/отключить
//header('Content-Type: text/html; charset=utf-8');			// Но при записи в MySQL кодировка имен фильмов может слететь
//header("Content-type: text/html; charset=windows-1251");	// Настраивается индивидуально для каждого сервера  (окончательно оттестировать не успел)
//															// + class_topmovies.php строка 225 (iconv)
//															// + db_conf.php         строка  14 $db->query('SET CHARACTER SET utf8');

	$movies = new TopMovies($db);

	if( $movies->isTodayLoaded() )
	{ 
		echo 'Today top movies already loaded.<br>',
			 'This server time: ' . date('H:i:s d-M-Y') . '<br>',
			 'Next update can be after: ' . $movies->nextUpdate();
		exit;
	}

	//Load HTML code
	if( !($html = $movies->curl_LoadURL(URL_TOP_10)) )
	{
		die( $movies->curl_get_error() );
	}
	
	//Set Parsing Options
	$movies->setParseOptions($parse_options); //$parse_options - GLOBAL array in class_topmovies.php

	//Load movies-info array
	$top_movies_array = $movies->Parse_URL($html); // var_dump($top_movies_array);exit;
	unset($html);

	//Save movies info in database
	if( !($movies->dbSaveMovieList( $top_movies_array )) )
	{
		die( $movies->dbsave_get_error() );
	}

echo "Loaded & saved new info on " . date('j-M-Y') . "<br>",
	 "New movies added in `movies` table: {$movies->c_added_movies}<br>",
	 "New ratings added in `rating` table: {$movies->c_added_rating}";
?>