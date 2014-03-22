<?php

  $html = "
<!DOCTYPE html> 
<html>
<head>
";
  include_once('includes/header.php');
  if(preg_match('%#.*ui-page=(\d+)%',$_SERVER['REQUEST_URI'],$matches))
  {
  	$_SESSION['firephp']->log($matches,'matches');
  }
  $html .= "
  </head>
  <body>";
  
  $html .= "
  <div data-role='page' data-title='All Items' data-add-back-btn='true'>
  	<div data-role='header' data-position='fixed'>
	  	<a href='index.php' data-rel='back' data-icon='arrow-l'>Back</a>
	  	<h1>List Media</h1>
	  	<a href='add_media_item.php' data-icon='plus' data-theme='b'>Add New</a>
  	</div>
  	<div data-role='content'>
  ";
  
  $query = "select * from  media_items ";
//	left outer join storage_slots on media_items.storage_slot_id = storage_slots.storage_slot_id 
//	left outer join storage_locations on storage_slots.storage_location_id = storage_locations.storage_location_id
//	left outer join media_types on media_types.media_type_id = media_items.media_type_id
//	left outer join mediums on mediums.medium_id = media_items.medium_id ";
  if(count($_GET))
  {
    foreach ($_GET as $key => $value)
  	{
  		$_GET[$key] = addslashes($value);
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
  $_SESSION['firephp']->log($query);
  $results = mysql_query($query) or $_SESSION['firephp']->error(mysql_error());
  
  $results_array = mysql2array($results);
  $_SESSION['firephp']->log($results_array,'results_array');
  if(count($results_array))
  {
  	$html .= "<ul data-filter='true' data-role='listview' data-theme='d'>";
//  	$html .= "<li data-icon='plus'><a href='add_media_item.php'>Add New</a></li>";
  	  $current_letter = 'A';
	  foreach($results_array as $result_row)
	  {
	  	if(strtoupper(substr($result_row['title'],0,1)) != $current_letter)
	  	{
	  		$current_letter = strtoupper(substr($result_row['title'],0,1));
//	  		$_SESSION['firephp']->log($current_letter,'current letter');
	  		$html .= "<li data-role='list-divider'>$current_letter</li>";
	  	}
	  	$thumbnail = "";

	  	if($result_row['image_location'] && 
	  		!preg_match('@^http://.*@', $result_row['image_location']) &&
	  		file_exists("images/media_items/{$result_row['image_location']}") && 
	  		!file_exists("images/media_items/thumbs/{$result_row['image_location']}")
	  		)
	  		{
  				$_SESSION['firephp']->log("images/media_items/{$result_row['image_location']} exists");
  				$_SESSION['firephp']->log("images/media_items/thumbs/{$result_row['image_location']} does not");
  				try{
  					make_thumb("images/media_items/{$result_row['image_location']}", "images/media_items/thumbs/{$result_row['image_location']}", 50);
  				}
  				catch(Exception $e)
  				{
  					$_SESSION['firephp']->error($e);
  				}
	  		}
	  	$thumbnail = "<image class='media_item_image' src='images/media_items/thumbs/{$result_row['image_location']}' />";
//		$_SESSION['firephp']->log($result_row,'result_row');
	  	$html .= "
	  		<li>
	  		<a href='view_media_item.php?item={$result_row['id']}'>$thumbnail{$result_row['title']}</a>
	  		<!-- <form style='float: right; z-index: 100;' method='POST' action='view_media_item.php'><input type='submit' data-icon='arrow-r' data-iconpos='notext' /><input type='hidden' name='view' value='true' title='view item'></input><input type='hidden' name='media_item_id' value='{$result_row['id']}' /></form> -->
	  		";
//	  		<ul>
//	  		<div data-role='header'>
//	  			<form style='float:right; margin-top: 5px;' method='POST' action='add_media_item.php'><input id='edit_media_item_button' type='submit' data-icon='gear' data-iconpos='right' name='Edit' value='Edit' title='edit'></input><input type='hidden' name='edit' value='true' title='edit'></input><input type='hidden' name='media_item_id' value='{$result_row['id']}' /></form>
//	  			<a href='index.php' data-rel='back' data-icon='arrow-l'>Back</a>
//	  			<h3>View Item</h3>
//	  		</div>
//	  		";
//	  	$html .= mediaItemHtml($result_row['id']);
//	  	$html .= "
//	  		</ul>
//	  		";
		$html .= "
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