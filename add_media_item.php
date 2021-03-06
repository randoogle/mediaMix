<?php
  $html = "
<!DOCTYPE html> 
<html>
	<head>";
  include_once('includes/header.php');
  
  $html .= "
  </head>
  <body>";
  
  $html .= "<div data-role='page' data-title='All Items' data-add-back-btn='true'>
  <div data-role='content'>
  	
  ";
  if($_POST['Save']
  	&& $_POST['media_item_title_input']
  )
  {
  	$_SESSION['firephp']->log($_POST,"\$_POST");
  	$query = "";
  	//cleanup post data
  	foreach ($_POST as $key => $value)
  	{
  		if(is_array($value))
  		{
  			foreach($_POST[$key] as $k => $v)
  			{
  				$_POST[$key][$k] = addslashes($v);
  			}
  		}
  		else
  		{
  			$_POST[$key] = addslashes($value);
  		}
  		
  	}
  	$media_id = "";
  	if($_POST['media_item_id_input'])
  	{//UPDATE EXISTING RECORD
  		$media_id = $_POST['media_item_id_input'];
  		$query = "update media_items
  			set 
  				title='{$_POST['media_item_title_input']}',
  				media_type_id='{$_POST['media_item_type_input']}',
  		";
 		//length
  		if($_POST['media_item_length_hours_input'] || $_POST['media_item_length_minutes_input'])
	  	{
	  		$query .= "
			    length='{$_POST['media_item_length_hours_input']}:{$_POST['media_item_length_minutes_input']}:00',
	  		";	
	  	}
	  	
	  	//capture image from website if the image location is a URL
  		if(preg_match('@^http://.*@',$_POST['media_item_image_location_input']))
	  	{
	  		$filename = save_image($_POST['media_item_image_location_input'],'images/media_items/');
	  		if(!$filename)
	  		{
	  			$_SESSION['firephp']->error("there was a problem saving {$_POST['media_item_image_location_input']}");
	  		}
	  		else 
	  		{
	  			$query .= "image_location='$filename',";
	  		}
	  	}
	  	else 
	  	{
	  		$query .= "image_location='{$_POST['media_item_image_location_input']}',";
	  	}
	  	$query .= "
	  		rating='{$_POST['media_item_rating_input']}',
	  		notes='{$_POST['media_item_notes_input']}',
	  		medium_id='{$_POST['media_item_medium_input']}',
	  		barcode='{$_POST['media_item_barcode_input']}',
	  		isbn='{$_POST['media_item_isbn_input']}',
	  		storage_slot_id='{$_POST['storage_slot_id_input']}'
	  	where id = '{$_POST['media_item_id_input']}'
	  	";
	  	
	  	//update record
	  	mysql_query($query) or $_SESSION['firephp']->error(mysql_error());
	  	
	  	//genre query
	  	$genre_query = "";
  	  	if(is_array($_POST['media_item_genre_input']))
	  	{
	  		//delete all current genres for this media item
	  		$genre_query_delete = "delete from media_genre where media_item_id = '{$_POST['media_item_id_input']}'";
	  		mysql_query($genre_query_delete) or $_SESSION['firephp']->error(mysql_error());
	  		
	  		//now insert all the currently selected genres
	  		foreach($_POST['media_item_genre_input'] as $genre_id)
	  		{
	  			$genre_query = "insert into media_genre (genre_id,media_item_id)
	  			values('$genre_id','{$_POST['media_item_id_input']}')
	  			";
	  			mysql_query($genre_query) or $_SESSION['firephp']->error(mysql_error());
	  		}
	  	}
  	}
  	else 
  	{//INSERT NEW RECORD
	  	$query = "insert into media_items (id,title,media_type_id,length,image_location,rating,notes,medium_id,barcode,isbn,storage_slot_id)
	  	values(''
	  		,'{$_POST['media_item_title_input']}'
	  		,'{$_POST['media_item_type_input']}'";
	  	if($_POST['media_item_length_hours_input'] || $_POST['media_item_length_minutes_input'])
	  	{
	  		//TODO: turns out this code doesn't work, as the interface itself is enforcing the 59 minute rule
	  		//needs to be fixed at the interface level
	  		$these_minutes = 0;
	  		$these_hours = 0;
	  		if($_POST['media_item_length_minutes_input'] >= 60)
	  		{//allow for input of minutes converted to hours
	  			$_SESSION['firephp']->log('longer than 60 minutes');
	  			$these_hours = floor($_POST['media_item_length_minutes_input'] / 60);
	  			$these_minutes = $_POST['media_item_length_minutes_input'] - ($these_hours * 60);
	  		}
			else
			{
				$these_minutes = $_POST['media_item_length_minutes_input'];
			}
	  		//in case there were any hours input
	  		$these_hours += $_POST['media_item_length_hours_input'];
	  		$query .= "
			,'{$these_hours}:{$these_minutes}:00'
	  		";	
			$_SESSION['firephp']->log("'{$these_hours}:{$these_minutes}:00'");
	  	}
	  	else
	  	{
	  		$query .= ",null";
	  	}
	  	if(preg_match('@^http://.*@',$_POST['media_item_image_location_input']))
	  	{//TODO:watch for images like this [http://imagelocation/something.php?image.jpg&h=200&w=100]
	  		$filename = save_image($_POST['media_item_image_location_input'],'images/media_items/');
	  		if(!$filename)
	  		{
	  			$_SESSION['firephp']->error("there was a problem saving {$_POST['media_item_image_location_input']}");
	  		}
	  		else 
	  		{
	  			$query .= ",'$filename'";
	  		}
	  	}
	  	else 
	  	{
	  		$query .= ",'{$_POST['media_item_image_location_input']}'";
	  	}
	  	$query .= "
	  		,'{$_POST['media_item_rating_input']}'
	  		,'{$_POST['media_item_notes_input']}'
	  		,'{$_POST['media_item_medium_input']}'
	  		,'{$_POST['media_item_barcode_input']}'
	  		,'{$_POST['media_item_isbn_input']}'
	  		,'{$_POST['storage_slot_id_input']}'
	  	)
	  	";
	  	
	  	//insert record
	  	mysql_query($query) or $_SESSION['firephp']->error(mysql_error());
	  	
	  	$query = "select media_items.id from media_items where media_items.id = LAST_INSERT_ID()";
	  	$query_result = mysql_query($query) or $_SESSION['firephp']->error(mysql_error());
	  	$result_array = mysql2array($query_result);
	  	$media_id = $result_array[0]['id'];
	  	
	  	//genre query
	  	$genre_query = "";
  	  	if(is_array($_POST['media_item_genre_input']))
	  	{ 		
	  		foreach($_POST['media_item_genre_input'] as $genre_id)
	  		{
	  			$genre_query = "insert into media_genre (genre_id,media_item_id)
	  			values('$genre_id',LAST_INSERT_ID())
	  			";
	  			mysql_query($genre_query) or $_SESSION['firephp']->error(mysql_error());
	  		}
	  	}
	  	
  	}
  	

  	$html .= "
  		<div data-role='header'>
  			<form style='float:right; margin-top: 5px;' method='POST' action='add_media_item.php'><input id='edit_media_item_button' type='submit' data-icon='gear' data-iconpos='right' name='Edit' value='Edit' title='edit'></input><input type='hidden' name='edit' value='true' title='edit'></input><input type='hidden' name='media_item_id' value='$media_id' /></form>
  			<a href='index.php' data-icon='home'>Home</a>
  			<h3>View Item</h3>
  		</div>
  	";
  	$html .= mediaItemHtml($media_id);
  	
  }
  else {//DISPLAY FORM
  	$html .= "
  	<div data-role='header' data-position='fixed'>
	  	<a href='index.php' data-rel='back' data-icon='delete'>Cancel</a>
	  	<h1>Add Media</h1>
	  	<a data-icon='check' data-theme='a'>Save</a>
  	</div>
  	";
  	$media_item_array = array();
  	$edit_media_item_hidden_input = "";
  	
  	if($_POST['edit'] && $_POST['media_item_id'])
  	{
  		$_POST['media_item_id'] = addslashes($_POST['media_item_id']);
  		$_SESSION['firephp']->warn('edit item');
	    $query = "select * from media_items
		left outer join storage_slots on media_items.storage_slot_id = storage_slots.storage_slot_id 
		left outer join storage_locations on storage_slots.storage_location_id = storage_locations.storage_location_id
		left outer join media_types on media_types.media_type_id = media_items.media_type_id
		left outer join mediums on mediums.medium_id = media_items.medium_id
	    where media_items.id = '{$_POST['media_item_id']}'
	  ";
	  
		$result = mysql_query($query) or $_SESSION['firephp']->error(mysql_error());
		$media_item_array = mysql2array($result);
		$media_item_array = $media_item_array[0];
	  	$_SESSION['firephp']->log($media_item_array,'media_item_array');
	  	
	  	$query = "select * from genres left join media_genre on media_genre.genre_id = genres.genre_id
	  	where media_genre.media_item_id = '{$_POST['media_item_id']}'";
	  	$result = mysql_query($query) or $_SESSION['firephp']->error(mysql_error());
	  	$temp_genre_array = mysql2array($result);
	  	$genre_array = array();
	  	foreach($temp_genre_array as $genre_row)
	  	{
	  		$genre_array[$genre_row['genre_id']] = true;
	  	}
	  	$_SESSION['firephp']->log($genre_array,'genre_array');
	  	$edit_media_item_hidden_input = "<input type='hidden' name='media_item_id_input' value='{$media_item_array['id']}'></input>";
  	}
	$html .= "<form action='add_media_item.php' method='post'>
	$edit_media_item_hidden_input
	<label for='media_item_title_input'>Title</label>
	<input name='media_item_title_input' id='media_item_title_input' type='text' value='". preg_replace("/'/","&#39;",$media_item_array['title']) . "'></input>
	<div data-role='fieldcontain'>	
		<label for='media_item_type_input'>Media Type</label>
		<select name='media_item_type_input' id='media_item_type_input'>
		<option data-placeholder='true'></option>
		";
		$query = "select * from media_types order by media_type_desc";
		$query_result = mysql_query($query) or $_SESSION['firephp']->error(mysql_error());
		$media_types = mysql2array($query_result);
		foreach ($media_types as $media_type_row)
		{
			$selected = "";
			if($media_item_array['media_type_id'] == $media_type_row['media_type_id'])
			{
				$selected = "selected='selected'";
			}
			$html .= "<option $selected value='{$media_type_row['media_type_id']}'>{$media_type_row['media_type_desc']}</option>";
		}
		preg_match('/(\d{2}):(\d{2}):\d{2}/',$media_item_array['length'],$matches);
		$_SESSION['firephp']->log($matches,'matches');
		$media_item_length_hours = $matches[1];
		$media_item_length_minutes = $matches[2];
		$html .= "</select><br />
	
	<label for='media_item_medium_input'>Medium</label>
	<select name='media_item_medium_input' id='media_item_medium_input'>
	<option data-placeholder='true'></option>
	";
	$query = "select * from mediums order by medium_title";
	$query_result = mysql_query($query) or $_SESSION['firephp']->error(mysql_error());
	$mediums = mysql2array($query_result);
	foreach ($mediums as $medium_row)
	{
		$selected = "";
		if($media_item_array['medium_id'] == $medium_row['medium_id'])
		{
			$selected = "selected='selected'";
		}
		$html .= "<option $selected value='{$medium_row['medium_id']}'>{$medium_row['medium_title']}</option>";
	}
	$html .= "</select>
	
		<div id='media_item_length_input_div' style='display: none;'>
		<label for='media_item_length_hours_input'>Length (hours:minutes)</label><br />
		<input name='media_item_length_hours_input' id='media_item_length_hours_input' type='range' max='20' min='0' value='$media_item_length_hours'></input><br />
		<input name='media_item_length_minutes_input' id='media_item_length_minutes_input' type='range' max='59' min='0' value='$media_item_length_minutes'></input>
		</div>
	
	</div>

			
	<label for='media_item_rating_input'>Rating</label>
	<input name='media_item_rating_input' id='media_item_rating_input' type='range' max='5' min='1' value='{$media_item_array['rating']}'></input>
	
	<label for='media_item_image_location_input'>Thumbnail Location (relative or URL)</label>
	<input name='media_item_image_location_input' id='media_item_image_location_input' type='text' value='{$media_item_array['image_location']}'></input>

	<label for='media_item_barcode_input'>Barcode</label>
	<input name='media_item_barcode_input' id='media_item_barcode_input' type='text' value='{$media_item_array['barcode']}'></input>

	<label for='media_item_isbn_input'>ISBN</label>
	<input name='media_item_isbn_input' id='media_item_isbn_input' type='text' value='{$media_item_array['isbn']}'></input>";
	$storage_slot = "";
	if($media_item_array['storage_slot_id'])
	{
		$storage_slot = "storage_slot='{$media_item_array['storage_slot_id']}'";
	}
	$html .= "
	<label for='storage_location_input'>Storage Location</label><a id='edit_storage_location_button' data-role='button' style='float:right; z-index: 10;' href='edit_storage_location.php' data-iconpos='notext' data-icon='gear'>Edit</a>
	<select name='storage_location_input' id='storage_location_input' $storage_slot>
	<option>None</option>
	";
	$query = "select * from storage_locations order by storage_title";
	$query_result = mysql_query($query) or $_SESSION['firephp']->error(mysql_error());
	$storage_locations = mysql2array($query_result);
	foreach ($storage_locations as $storage_location_row)
	{
		$selected = "";
		if($media_item_array['storage_location_id'] == $storage_location_row['storage_location_id'])
		{
			$selected = "selected='selected'";
		}
		$html .= "<option $selected value='{$storage_location_row['storage_location_id']}'>{$storage_location_row['storage_title']}</option>";
	}
	$html .= "</select>
	<div data-role='fieldcontain' id='storage_slot_input_div'>
	<label for='storage_slot_id_input'>Storage Slot</label>
	<select id='storage_slot_id_input' name='storage_slot_id_input'>
	</select>
	</div>
	
	<div data-role='fieldcontain'>
		<fieldset data-role='controlgroup' data-type='horizontal'>
		<legend>Genre(s)</legend>";
		$query = "select * from genres order by genres.genre_title";
		$query_result = mysql_query($query) or die(mysql_error());
		$genres = mysql2array($query_result);
		foreach($genres as $genre_row)
		{
			$checked = "";
			if($genre_array[$genre_row['genre_id']])
			{
				$checked = "checked='checked'";
			}
			$html .= "<input $checked type='checkbox' name='media_item_genre_input[]' id='media_item_genre_{$genre_row['genre_id']}' value='{$genre_row['genre_id']}'></input>
			<label for='media_item_genre_{$genre_row['genre_id']}'>{$genre_row['genre_title']}</label>";
		}
	$html .= "
		</fieldset>
	</div>
	
	<label for='media_item_notes_input'>Notes</label>
	<textarea name='media_item_notes_input' id='media_item_notes_input'>{$media_item_array['notes']}</textarea>
	
	<div data-role='fieldcontain'>
		<a href='all_media.php' data-direction='reverse' data-role='button' data-inline='true'>Cancel</a>
		<input type='submit' data-role='button' data-inline='true' data-theme='b' name='Save' value='Save'></submit>
	</div>
	
	</form>";  
  }//end if Save
  
  $html .= "
  	</div>
  </div>
  </body>
  </html>";
  
  echo $html;
?>