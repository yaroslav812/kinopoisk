<?php
	define('USE_CACHE', FALSE);// IF TRUE => don't forget chmod 775 'cache' ($db->cache_dir)


	
	/* SET CACHE OPTIONS */
	if(USE_CACHE)
	{
		$db->cache_dir = __DIR__ . $db->cache_slash . 'cache';//Меняем только то, что в кавычках (cache_slash was added for local test on WinXP)
	// (1. You must create this dir. first!)
	// (2. Might need to do chmod 775)
		$db->use_disk_cache = true;
		$db->cache_queries = true;
		$db->cache_timeout = 24;
	}

?>