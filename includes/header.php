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
  		make_thumb("images/media_items/$local_image_name.$local_image_ext","images/media_items/thumbs/$local_image_name.$local_image_ext",100);
  		return "$local_image_name.$local_image_ext";	
  	}
  }
	/**
	 * 
	 * Create a thumbnail from a source image
	 * @param string $src
	 * @param string $dest
	 * @param int $desired_width
	 */
	function make_thumb($src,$dest,$desired_width)
	{
	
		if(preg_match("/(.*?)\.([^\.]+)$/", $src, $matches))
		{
			$_SESSION['firephp']->log($matches,'make_thumb() matches');
			$source_image = null;
			
			//read the source image
			switch ($matches[2])
			{//determine image type
				case 'jpg':
					$source_image = imagecreatefromjpeg($src);
					break;
				case 'png':
					$source_image = imagecreatefrompng($src);
					break;
				case 'bmp':
					$source_image = imagecreatefromwbmp($src);
					break;
				case 'gif':
					$source_image = imagecreatefromgif($src);
					break;
				default:
					//unsupported image type
					$_SESSION['firephp']->error("images of type \"{$matches[2]}\" are not supported");
					return false;
					break;
			}
		
			
			
			$width = imagesx($source_image);
			$height = imagesy($source_image);
			
			//find the "desired height" of this thumbnail, relative to the desired width
			$desired_height = floor($height*($desired_width/$width));
			
			//create a new, "virtual" image
			$virtual_image = imagecreatetruecolor($desired_width,$desired_height);
			
			//copy source image at a resized size
			imagecopyresized($virtual_image,$source_image,0,0,0,0,$desired_width,$desired_height,$width,$height);
			
			//create the physical thumbnail image to its destination
			switch ($matches[2])
			{
				case 'jpg':
					imagejpeg($virtual_image,$dest,100);
					break;
				case 'png':
					imagepng($virtual_image,$dest,9);
					break;
				case 'bmp':
					image2wbmp($virtual_image,$dest);
					break;
				case 'gif':
					imagegif($virtual_image,$dest);
					break;
				default:
					//unsupported image type
					$_SESSION['firephp']->error("images of type \"{$matches[2]}\" are not supported");
					return false;
					break;
			}
		}
	}
  
  if(true)
  {
  	$html .= '
	<meta name="viewport" content="width=device-width, initial-scale=1">
  	<link rel="stylesheet" href="includes/css/jquery.mobile-1.0b2.min.css" />
  	<link rel="stylesheet" href="includes/css/mediaMix.css" />
  	<link rel="apple-touch-icon" href="images/iOS_icon.png" />
	<script type="text/javascript" src="includes/js/jquery-1.6.2.min.js"></script>
	<script type="text/javascript" src="includes/js/jquery.mobile-1.0b3.min.js"></script>
	<script type="text/javascript" src="includes/js/add_media_item.js"></script>
	';
  }
?>