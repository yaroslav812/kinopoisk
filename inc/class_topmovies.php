<?php
if (!defined('__DIR__')) define( '__DIR__', dirname(__FILE__) );

$parse_options = array
(
	'START_PARSE_LABEL'  => '<tr  id="top250_place_1">',
	'FINISH_PARSE_LABEL' => '</table>',
	'SPLIT_PARSE_LABEL'  => '</tr>',

	/* Patterns for getTextBetweenTags()  */
	'PATTERN_NAME_YEAR' => "/<a ?.* class=\"all\">(.*)<\/a>/",		// <a href="/film/368936/" class="all">Movie Name (2013)</a>
	'PATTERN_POSITION'  => "/<\/a>(.*).<\/td>/",					// </a>17.</td>
	'PATTERN_RATING'    => "/<a ?.* class=\"continue\">(.*)<\/a>/", // <a href="/film/1902/votes/" class="continue">8.092</a>
	'PATTERN_VOTES'     => "/<span ?.* #777\">(.*)<\/span>/",		// <span style="color: #777">(33&nbsp;477)</span> 
);


class TopMovies{
	
	private $db;				   // MySQL connection link
	private $_curlError	   = null; // Curl connection error message
	private $_dbSaveError  = null; // Database error message
	private $_options	   = null; // Parser options
	private $_today_date;		   // Today date date('Y-m-d') for same value in different functions
	public $c_added_movies = 0;	   // Counter for added movies into movies table
	public $c_added_rating = 0;    // Counter for added ratings into rating table
	
	//### FUNCTIONS ###

    public function __construct($db)
	{
        $this->db = $db;
		$this->_today_date = date('Y-m-d');
    }
	
	public function __destruct()
	{
		$this->db->disconnect();
	}
	
	/* Set parser options from GLOBAL $parse_options array */
	public function setParseOptions(array $opt)
	{
		$this->_options = $opt;
	}
	
/**
 * Get cURL Error
 * @return string
 */
	public function curl_get_error()
	{
		return $this->_curlError;
	}
/**
 * Get Database Save Error
 * @return string
 */
	public function dbsave_get_error()
	{
		return $this->_dbSaveError;
	}

/**
 * Load HTML using cURL
 * @param string $url to request
 * @return string
 */
	public function curl_LoadURL( $url, array $options = array() )
	{
		/* Check cURL library installation */
		if (!function_exists('curl_init')) {
			$this->_curlError = 'Curl is not installed!';
			return false;
		}
		/* Set default options */
		if(empty($options)) $options = array
		(
			CURLOPT_URL => $url,
			CURLOPT_HEADER => 0,
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_TIMEOUT => 5
		);
		/* Load html page */
		$ch = curl_init();
		curl_setopt_array($ch, $options);
		if( ! $result = curl_exec($ch))
		{
			$this->_curlError = 'Can not load URL: ' . $url;
		}
		curl_close($ch);
		return $result;
	}

/**
 * HTML Parser - Load Movie position, name, year, rating, votes in associative array
 * @param string $html for parse
 * @return array[key]['value']
 */
	public function Parse_URL(&$html)
	{
		if($this->_options == null) die('Use setParseOptions() before ' . __FUNCTION__ );
		
		$pos_start_parse = strpos($html, $this->_options['START_PARSE_LABEL'] );
		$pos_end_parse   = strpos($html, $this->_options['FINISH_PARSE_LABEL'] , $pos_start_parse);
		
		/* Get <table> with movies */
		$html = substr($html, $pos_start_parse, $pos_end_parse - $pos_start_parse );

		/* Get <tr> array */
		$array_tr = explode($this->_options['SPLIT_PARSE_LABEL'], $html);

		$movies_array = array();
		$n=0;
		foreach($array_tr as $k=>$v)
		{
			if( $n++ >= NUM_LOAD_MOVIES ) break;
			$v = trim($v);
			if( empty($v) ) continue;
	
			/* Load Name & Year */
			$nameYear         = $this->getTextBetweenTags($v, $this->_options['PATTERN_NAME_YEAR'] );
			$pos_last_bracket = strrpos($nameYear, '(');
			$name             = trim( substr($nameYear, 0, $pos_last_bracket) );
			$name             = str_replace('&nbsp;', ' ', $name);
			
			$year = substr($nameYear, $pos_last_bracket);
			$year = (int) trim( str_replace( array('(',')'), '', $year) );
	
			/* Load Position */
			$position = (int) $this->getTextBetweenTags($v, $this->_options['PATTERN_POSITION'] );
	
			/* Load Rating */
			$rating = $this->getTextBetweenTags($v, $this->_options['PATTERN_RATING'] );
			$rating = (float) $rating;
	
			/* Load Votes */
			$votes = $this->getTextBetweenTags($v, $this->_options['PATTERN_VOTES'] );
			$votes = (int) trim( str_replace( array('(',')','&nbsp;'), '', $votes) );

		    $movies_array[$k]['name']	  = $name;
			$movies_array[$k]['year']     = $year;
			$movies_array[$k]['position'] = $position;
			$movies_array[$k]['rating']   = $rating;
			$movies_array[$k]['votes']    = $votes;
		}
		return $movies_array;
	}

/**
 * Get innerHTML between tags specified in $pattern
 * @param string $string as part of HTML code
 * @param string $pattern for preg_match function
 * @return string
 */
	private function getTextBetweenTags($string, $pattern)
	{
		preg_match($pattern, $string, $matches);
		return $matches[1];
	}

/**
 * Get time of next update
 * @return string
 */
	public function nextUpdate()
	{
		$time1 = new DateTime('23:59:59');
		$time2 = new DateTime(date('H:i:s'));
		$interval = $time1->diff($time2);
		return $interval->format('%h hour(s) %i minute(s) %s second(s)');
	}

/**
 * Get today record from `dates` table
 * @return boolean
 */
	public function isTodayLoaded()
	{
		$result = (int) $this->db->get_var("SELECT COUNT(*) FROM `dates` WHERE `date` = '{$this->_today_date}'");
		if($result > 0) return true;
		else			return false;
	}

/**
 * Save daily movie statistic in database
 * @param array $movies_array with all movies info
 * @return boolean
 */
	public function dbSaveMovieList(array $movies_array)
	{
		/* IF today already loaded => return */
		if( $this->isTodayLoaded() )
		{
			$this->_dbSaveError = "Movies on {$this->_today_date} already loaded.";
			return false;
		}
		/* ELSE insert new Date into `dates` table*/
		else
		{
			$this->db->query("INSERT INTO `dates` (`date`,`time`) VALUES ('{$this->_today_date}', '".date('H:i:s')."')");/* CURTIME() заменил на date() т.к. время PHP и MySQL может отличаться  */
			if( $this->db->rows_affected > 0 )
			{
				$iddate = $this->db->insert_id;
			}
			else
			{
				$this->_dbSaveError = "DB Error: can not insert new date.";
				return false;
			}
		}
		
		foreach($movies_array as $movie)
		{
			/* Check existing Movie_Name & Year in table `movies` */
			$result = $this->db->get_var("SELECT `idmovie` FROM `movies` WHERE `name` = '{$this->db->escape($movie['name'])}' AND `year` = {$movie['year']}");
			/* Found:  */
			if( $this->db->num_rows > 0 )
			{
				$idmovie = (int) $result;
			}
			/* Not found: => add new movies in `movies` table */
			else
			{
				$conv_name = iconv(mb_detect_encoding($movie['name'], mb_detect_order(), true), "UTF-8", $movie['name']);// во избежании кракозябр в MySQL
				$this->db->query("INSERT INTO `movies` (`name`, `year`) VALUES ('{$this->db->escape($conv_name)}', {$movie['year']} )");
				if( $this->db->rows_affected > 0 )
				{
					$idmovie = $this->db->insert_id;
					$this->c_added_movies++;
				}
				else
				{
					continue;
				}
			}

			/* Insert new Rating info in rating` table */
			$this->db->query("INSERT INTO `rating` (`idmovie`, 
													`position`,
													`rating`, 
													`votes`, 
													`iddates`
													) 
											VALUES ( $idmovie,
													 {$movie['position']},
													 {$movie['rating']},
													 {$movie['votes']},
													 $iddate )");
			if( $this->db->rows_affected > 0 ) $this->c_added_rating++;
		}//end foreach
		return true;
	}
/**
 * Load top 10 movies from database on selected date
 * @param array $movies_array with all movies info
 * @return array or false
 */
	public static function LoadTop10(ezSQL_mysql $db, $date)
	{
		if(empty( $date ))
		{
			return false;
		}
		$result = $db->get_results("SELECT  `r`.`position`, `r`.`rating`, `r`.`votes`, `m`.`name`, `m`.`year`
									FROM `rating` AS `r`
									INNER JOIN `movies` AS `m`  USING (`idmovie`)
									WHERE `r`.`iddates` = (
															SELECT `dates`.`iddates` 
															FROM   `dates` 
															WHERE  `dates`.`date`='{$db->escape($date)}'
														  )
									ORDER BY `r`.`position`
									LIMIT 10");
		if( $db->num_rows == 0)
		{
			return false;
		}
		$array_top10 = array();
		foreach ( $result as $k => $v )
		{
			$array_top10[$k]['position'] = (int) $v->position;
            $array_top10[$k]['rating']	 = (float) sprintf("%.3f", $v->rating);
			$array_top10[$k]['votes']    = (int) $v->votes;
			$array_top10[$k]['name']	 = $v->name;
			$array_top10[$k]['year']	 = (int) $v->year;
		}
		return $array_top10;
	}
}
?>