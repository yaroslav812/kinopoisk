<?php
	include_once './inc/db_conf.php';         // Load MySQL class and set GLOBAL $db (Object)
	include_once './inc/class_topmovies.php';
	include_once './inc/cache_options.php';   // Set cache options

	/* Load GET values */
	$date = empty($_GET['date']) ? '' : $_GET['date'];
	
	//Defence MySQL injection
	if( !empty($date) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) ) die('Invalid date format');

	/* Load top 10 movies array */
	$array_top10 = NULL;
	if(!empty($date))
	{
		$array_top10 = TopMovies::LoadTop10( $db, $date );
	}

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>10 лучших фильмов c КиноПоиска</title>
<link href="css/jquery-ui-1.10.3.custom.css" rel="stylesheet" type="text/css">
<link href="css/styles.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.10.3.custom.min.js"></script>
<script type="text/javascript" src="js/myscript.js"></script>
</head>
<body>
<p><a href="front-end.php">front-end.php</a> <a href="front-end-ajax.htm">front-end-ajax.htm</a> <a href="cron.php">cron.php</a></p>
<form id="myform" name="form1" action="" method="post">
	<fieldset>
    	<legend>Выберете дату:</legend>
        <p>Date: <input type="text" id="datepicker" value="<?php echo $date; ?>" /></p>
    </fieldset>
</form>
<div id="result" class="result">
<?php /*##### Out Error #####*/ ?>
<?php if( !empty($date) && $array_top10 === false  ): ?>
	<div class="ui-widget">
		<div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">
			<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
			<strong>Ошибка:</strong> статистики на выбранную дату в базе данных не обнаружено.</p>
		</div>
	</div>
<?php /*##### Out top 10 movies #####*/ ?>
<?php elseif(!empty($array_top10)): ?>
	<table id="table10" width="550" border="0" align="center" cellpadding="5" cellspacing="2">
	<tr>
		<th width="20">&nbsp;</th>
		<th>Фильм</th>
		<th class="left15" width="150">Рейтинг</th>
	</tr>
	<?php foreach($array_top10 as $movie){  ?>
		<tr>
			<td class="number"><?php echo $movie['position']; ?>.</td>
			<td class="movie" id="table10"><?php echo $movie['name']." ({$movie['year']})"; ?></td>
			<td class="rating"><?php echo $movie['rating']; ?> <span class="votes">(<?php echo $movie['votes']; ?>)</span></td>
		</tr>
	<?php } ?>
</table>
<?php endif; ?>
</div>
</body>
</html>