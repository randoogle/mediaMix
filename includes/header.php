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
  	while(file_exists("images/media_items/$local_image_name.$local_image_ext"))
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
			$image_type = strtolower($matches[2]);
			
			//read the source image
			switch ($image_type)
			{//determine image type
				case 'jpg':
				case 'jpeg':
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
					if($source_image === false)
					{
						$_SESSION['firephp']->error("unable to read gif image from $src");
					}
					break;
				default:
					//unsupported image type
					$_SESSION['firephp']->error("images of type \"$image_type\" are not supported");
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
			switch ($image_type)
			{
				case 'jpg':
				case 'jpeg':
					imagejpeg($virtual_image,$dest,100);
					break;
				case 'png':
					imagepng($virtual_image,$dest,9);
					break;
				case 'bmp':
					if(!image2wbmp($virtual_image,$dest))
					{
						$_SESSION['firephp']->error("unable to create bmp image $src at $dest");
					}
					break;
				case 'gif':
					if(!imagegif($virtual_image,$dest))
					{
						$_SESSION['firephp']->error("unable to create gif image $src at $dest");
						if(!imagetypes() & IMG_GIF)
						{
							$_SESSION['firephp']->warn("gif image creation not supported on this system");	
						}
					}
					break;
				default:
					//unsupported image type
					$_SESSION['firephp']->error("images of type \"$image_type\" are not supported");
					return false;
					break;
			}
		}
	}
/**
 * 
 * returns html for viewing the specified media item
 * @param int $media_item_id
 */
function mediaItemHtml($media_item_id)
{
  $media_item_array = array();
  $html = "";
  if($media_item_id)
  {
  	$media_id = addslashes($media_item_id);//just in case of potential injections
  	//perform main media item query
	  $query = "select * from  media_items 
		left outer join storage_slots on media_items.storage_slot_id = storage_slots.storage_slot_id 
		left outer join storage_locations on storage_slots.storage_location_id = storage_locations.storage_location_id
		left outer join media_types on media_types.media_type_id = media_items.media_type_id
		left outer join mediums on mediums.medium_id = media_items.medium_id
	  	where media_items.id = '$media_id'
	  	";
	  $results = mysql_query($query) or $_SESSION['firephp']->error(mysql_error());
	  
	  $results_array = mysql2array($results);
	  $media_item_array = $results_array[0];
	  
  $html .= "
  


	";
	  	$query = "select genres.genre_title from genres left join media_genre on media_genre.genre_id = genres.genre_id
	  	where media_genre.media_item_id = $media_id";
	  	$result = mysql_query($query) or die(mysql_error());
	  	$genre_array = mysql2array($result);
		
	  	$image = "";
	  	$medium_html = $media_item_array['medium_title'];
	  	if($media_item_array['medium_image_location'])
	  	{
  		  	if(preg_match('@^http://.*@', $media_item_array['medium_image_location']))
	  		{
	  			$medium_html = "<image src='{$media_item_array['medium_image_location']}' />";
	  		}
	  		else 
	  		{
	  			$medium_html = "<image src='images/mediums/{$media_item_array['medium_image_location']}' />";
	  		}	  		
	  	}
	  	if($media_item_array['image_location'])
	  	{
	  		if(preg_match('@^http://.*@', $media_item_array['image_location']))
	  		{
	  			$image = "<image class='media_item_image' src='{$media_item_array['image_location']}' />";
	  		}
	  		else 
	  		{
	  			$image = "<image class='media_item_image' src='images/media_items/{$media_item_array['image_location']}' />";
	  		}
	  		
	  	}


	  	$html .= "
	  			<h3>{$media_item_array['title']}</h3>
	  			<ul data-role='listview' data-theme='d'>";
	  	
    	if($media_item_array['storage_title'])
  		{
	  		$html .= "<li>{$media_item_array['storage_title']} [{$media_item_array['storage_slot_label']}]</li>";  			
  		}
  	  	if($media_item_array['rating'] > 0)
	  	{
		  	$html .= "<li><div>";
		  	for ($i = 0; $i < $media_item_array['rating']; $i++)
		  	{
		  		$html .= "<img src='images/star.png' />";
		  	}
		  	$html .= "</div></li>";	  		
	  	}
	  	if($image)
	  	{
	  		$html .= "
	  				<li><div>{$image}</div></li>	  		
	  		";
	  	}
		$html .= "
			  		<li>$medium_html{$media_item_array['media_type_desc']}
			  			";
  		if($media_item_array['length'])
  		{
  			$html .= "<br />{$media_item_array['length']}";
  		}
  		if($media_item_array['barcode'])
  		{
  			$html .= "<br />Barcode: {$media_item_array['barcode']}";
  		}
  		if($media_item_array['isbn'])
  		{
  			$html .= "<br />ISBN: {$media_item_array['isbn']}";
  		}
  		if(count($genre_array))
  		{
  			$html .= "<br /><br />Tags: ";
  			foreach ($genre_array as $genre_row)
  			{
  				$html .= "<span class='media_item_genre_title'>{$genre_row['genre_title']}</span> ";	
  			}
//  			$html .= "</span>";
  		}
      	if($media_item_array['notes'])
  		{
  			$html .= "<li><p>" . preg_replace('/\n/', '<br />', $media_item_array['notes']) . "</p></li>";
  		}
	  $html .= "
	  			</li>
		  	</ul>
	  ";
  }
  return $html;
}
  if(true)
  {
  	$html .= '
	<meta name="viewport" content="width=device-width, initial-scale=1">
  	<meta charset="UTF-8">
  	<link rel="stylesheet" href="includes/css/jquery.mobile-1.2.0.min.css" />
  	<link rel="stylesheet" href="includes/css/mediaMix.css" />
  	<link rel="apple-touch-icon" href="images/iOS_icon.png" />
	<script type="text/javascript" src="includes/js/jquery-1.8.3.min.js"></script>
	<script type="text/javascript" src="includes/js/jquery.mobile-1.2.0.min.js"></script>
	<script type="text/javascript" src="includes/js/media_mix.js"></script>
	';
  }
?>