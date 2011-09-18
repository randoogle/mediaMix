<?php

  $html = "
<!DOCTYPE html> 
<html>
<head>
";
  include_once('includes/header.php');
  
  $html .= "
  </head>
  <body>";
  
  $html .= "<div data-role='page' data-title='All Items'>
  <div data-role='content'>
  ";
  
  $query = "select * from media_items left outer join (storage_locations,storage_slots,media_types,mediums) ".
  "on (
	media_items.storage_slot_id = storage_slots.storage_slot_id
  	and storage_slots.storage_location_id = storage_locations.storage_location_id
  	and media_types.media_type_id = media_items.media_type_id
  	and mediums.medium_id = media_items.medium_id
  	)";
  if(count($_GET))
  {
    foreach ($_GET as $key => $value)
  	{
  		$_GET[$key] = stripslashes($value);
  	}
  }
  switch ($_GET['table'])
  {
  	case 'media_items':
	  	$query .= "where ";
	  	$query .= "{$_GET['field']} = '{$_GET['value']}'";
  		break;
  	case 'media_genre':
	  	$query .= "where ";
	  	$query .= "media_items.id in (select media_genre.media_item_id from media_genre where media_genre.genre_id = '{$_GET['value']}')";
	  	break;
  	default:
  		break;
  }
  $query .=	"
  	order by media_items.title";
  $results = mysql_query($query) or $_SESSION['firephp']->error(mysql_error());
  
  $results_array = mysql2array($results);
  $_SESSION['firephp']->log($results_array,'results_array');
  if(count($results_array))
  {
  	$html .= "<ul data-filter='true' data-role='listview' data-theme='d'>";
  	$html .= "<li data-icon='plus'><a href='add_media_item.php'>Add New</a></li>";
  	  $current_letter = 'A';
	  foreach($results_array as $result_row)
	  {
	  	$query = "select genres.genre_title from genres left join media_genre on media_genre.genre_id = genres.genre_id
	  	where media_genre.media_item_id = {$result_row['id']}";
	  	$result = mysql_query($query) or die(mysql_error());
	  	$genre_array = mysql2array($result);
	  	if(strtoupper(substr($result_row['title'],0,1)) != $current_letter)
	  	{
	  		$current_letter = strtoupper(substr($result_row['title'],0,1));
	  		$_SESSION['firephp']->log($current_letter,'current letter');
	  		$html .= "<li data-role='list-divider'>$current_letter</li>";
	  	}
	  	$image = "";
	  	$medium_html = $result_row['medium_title'];
	  	if($result_row['medium_image_location'])
	  	{
  		  	if(preg_match('@^http://.*@', $result_row['medium_image_location']))
	  		{
	  			$medium_html = "<image src='{$result_row['medium_image_location']}' />";
	  		}
	  		else 
	  		{
	  			$medium_html = "<image src='images/mediums/{$result_row['medium_image_location']}' />";
	  		}	  		
	  	}
	  	if($result_row['image_location'])
	  	{
	  		if(preg_match('@^http://.*@', $result_row['image_location']))
	  		{
	  			$image = "<image class='media_item_image' src='{$result_row['image_location']}' />";
	  		}
	  		else 
	  		{
	  			if(file_exists("images/media_items/{$result_row['image_location']}") && !file_exists("images/media_items/thumbs/{$result_row['image_location']}"))
	  			{
	  				$_SESSION['firephp']->log("images/media_items/{$result_row['image_location']} exists");
	  				$_SESSION['firephp']->log("images/media_items/thumbs/{$result_row['image_location']} does not");
	  				try{
	  					make_thumb("images/media_items/{$result_row['image_location']}", "images/media_items/thumbs/{$result_row['image_location']}", 100);
	  				}
	  				catch(Exception $e)
	  				{
	  					$_SESSION['firephp']->error($e);
	  				}
	  			}
	  			$image = "<image class='media_item_image' src='images/media_items/{$result_row['image_location']}' />";
	  			$thumbnail = "<image class='media_item_image' src='images/media_items/thumbs/{$result_row['image_location']}' />";
	  		}
	  		
	  	}
	  	$rating = "<div>";
	  	for ($i = 0; $i < $result_row['rating']; $i++)
	  	{
	  		$rating .= "<img src='images/star.png' />";
	  	}
	  	$rating .= "</div>";
	  	$html .= "
	  		<li>$thumbnail<h3>{$result_row['title']}</h3>
	  			<ul>";
	  	if($image)
	  	{
	  		$html .= "
	  				<li><div>{$image}</div></li>	  		
	  		";
	  	}
		$html .= "
			  		<li>$medium_html<form style='float:right; z-index: 10;' method='POST' action='add_media_item.php'><input id='edit_media_item_button' type='submit' data-iconpos='notext' data-icon='gear' name='edit' value='true' title='edit'></input><input type='hidden' name='media_item_id' value='{$result_row['id']}' /></form><h3>{$result_row['media_type_desc']}</h3>
			  			$rating";
  		if($result_row['length'])
  		{
  			$html .= "<br />{$result_row['length']}";
  		}
  		if($result_row['notes'])
  		{
  			$html .= "<p>{$result_row['notes']}</p>";
  		}
  		if($result_row['barcode'])
  		{
  			$html .= "<br />Barcode: {$result_row['barcode']}";
  		}
  		if($result_row['isbn'])
  		{
  			$html .= "<br />ISBN: {$result_row['isbn']}";
  		}
  		if(count($genre_array))
  		{
  			$html .= "<br /><br />Tags: ";
  			foreach ($genre_array as $genre_row)
  			{
  				$html .= "<span class='media_item_genre_title'>{$genre_row['genre_title']}</span>";	
  			}
  		}
  		$html .= "
			  		</li>
			  		<li>{$result_row['storage_title']} [{$result_row['storage_slot_label']}]</li>
	  			</ul>
	  		</li>
	  		";
	  }
  	$html .= "</ul>";
  }
  $html .= "
  	</div>
  </div>
  </body>
  </html>";
  echo $html;
?>