<?php
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
?>