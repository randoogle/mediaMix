<?php
  error_reporting(E_ERROR);
  include_once('includes/firephp.php');//allow for advanced debugging  
  include_once('includes/dbcons.php');//allow for db_connect()
  include_once('includes/array2htmlTable.php');//allow for array2html()
  include_once('includes/mysql2array.php');//allow for mysql2array()
  include_once('includes/tv.com2table.php');//allow for addSeason(url,showID)
  include_once('includes/showStats.php');//allow for showStats(showID)

//  include_once('viewEps.php');
  
  //define baseurl
  $filename = basename($_SERVER['REQUEST_URI']);
  $baseurl = "http://" . $_SERVER['HTTP_HOST'] . preg_replace("/(.*)$filename/i", '$1', $_SERVER['REQUEST_URI']);
//   print $baseurl;//debugging

  $link = db_connect();
  function getQueryArray($query)
  {
  	
  	try {
  	  	$query_result = mysql_query($query) or $_SESSION['firephp']->error(mysql_error());
  	  	if(is_resource($query_result))
  	  	{
  	  		return mysql2array($query_result);	
  	  	}
  	} catch (Exception $e) {
  		$_SESSION['firephp']->error($e);
  	}
  }
  function save_image($image_url,$storage_folder)
  {
	$remote_image = file_get_contents($image_url);
	  		
	preg_match('@(.*?)\.([^\.]+)$@',basename($image_url),$matches);
 	
  	$local_image_name = urldecode($matches[1]);
  	$local_image_ext = $matches[2];
  	$counter = 2;
  	while(file_exists('images/media_items/' . $local_image_name . $local_image_ext))
  	{
  		$local_image_name = $local_image_name . "($counter)";
  		$counter++;
  	}
  	$fp = fopen("{$storage_folder}$local_image_name.$local_image_ext",'c');
  	$success = fwrite($fp,$remote_image);
  	
  	if(!$success)
  	{
  		return false;
  	}
  	else 
  	{
  		return "$local_image_name.$local_image_ext";	
  	}
  }
?>