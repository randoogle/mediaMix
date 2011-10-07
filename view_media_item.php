<?php
function mediaItemHtml($media_item_id)
{
  $media_item_array = array();
  $html = "";
  if($media_item_id)
  {
  	$media_id = addslashes($media_item_id);//just in case of potential injections
  	//perform main media item query
	  $query = "select * from media_items left outer join (storage_locations,storage_slots,media_types,mediums) ".
	  "on (
		media_items.storage_slot_id = storage_slots.storage_slot_id
	  	and storage_slots.storage_location_id = storage_locations.storage_location_id
	  	and media_types.media_type_id = media_items.media_type_id
	  	and mediums.medium_id = media_items.medium_id
	  	)
	  	where media_items.id = '$media_id'
	  	";
	  $results = mysql_query($query) or $_SESSION['firephp']->error(mysql_error());
	  
	  $results_array = mysql2array($results);
	  $media_item_array = $results_array[0];
	  
  $html .= "
  <div data-role='page' data-title='View Item' data-add-back-btn='true'>
  	  <div data-role='header'><form style='float:right;' method='POST' action='add_media_item.php'><input id='edit_media_item_button' type='submit' data-icon='gear' data-iconpos='right' name='Edit' value='Edit' title='edit'></input><input type='hidden' name='Edit' value='true' title='edit'></input><input type='hidden' name='media_item_id' value='$media_id' /></form></div>
	  <div data-role='content'>
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
	  	$rating = "<div>";
	  	for ($i = 0; $i < $media_item_array['rating']; $i++)
	  	{
	  		$rating .= "<img src='images/star.png' />";
	  	}
	  	$rating .= "</div>";
	  	$html .= "
	  			<h3>{$media_item_array['title']}</h3>
	  			<ul data-role='listview' data-theme='d'>";
	  	if($image)
	  	{
	  		$html .= "
	  				<li><div>{$image}</div></li>	  		
	  		";
	  	}
		$html .= "
			  		<li>$medium_html<h3>{$media_item_array['media_type_desc']}</h3>
			  			$rating";
  		if($media_item_array['length'])
  		{
  			$html .= "<br />{$media_item_array['length']}";
  		}
  		if($media_item_array['notes'])
  		{
  			$html .= "<p>{$media_item_array['notes']}</p>";
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
  				$html .= "<span class='media_item_genre_title'>{$genre_row['genre_title']}</span>";	
  			}
  		}
  		if($media_item_array['storage_title'])
  		{
	  		$html .= "<li>{$media_item_array['storage_title']} [{$media_item_array['storage_slot_label']}]</li>";  			
  		}
	  $html .= "
		  	</ul>
		  </div>
	  </div>
	  ";
  }
  return $html;
}
?>