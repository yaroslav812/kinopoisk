<?php /* ECHO JSON DATA FOR myscript-ajax.js */
	include_once './inc/db_conf.php';         // Load MySQL class and set GLOBAL $db (Object)
	include_once './inc/class_topmovies.php';
	include_once './inc/cache_options.php';   // Set cache options
	
	/* Load GET values */
	$date = empty($_GET['date']) ? '' : $_GET['date'];
	
	//Defence MySQL injection
	if( !empty($date) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) ) die('0');

	if( !($array_top10 = TopMovies::LoadTop10( $db, $date )) )
	{
		echo '0';
	}
	else
	{
		echo json_encode( $array_top10 );
	}

?>