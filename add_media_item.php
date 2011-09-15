<?php
  $html = "
<!DOCTYPE html> 
<html>
	<head>";
  include_once('includes/header.php');
  
  $html .= "
  </head>
  <body>";
  
  $html .= "<div data-role='page' data-title='All Items'>
  <div data-role='content'>
  	<div data-role='header'>Add Media Item</div>
  ";
  if($_POST['Save']
  	&& $_POST['media_item_title_input']
  )
  {
  	//cleanup post data
  	foreach ($_POST as $key => $value)
  	{
  		$_POST[$key] = stripcslashes($value);
  	}
  	$query = "insert into media_items (id,title,media_type_id,length,image_location,rating,notes,medium_id,barcode,isbn,storage_slot_id)
  	values(''
  		,'{$_POST['media_item_title_input']}'
  		,'{$_POST['media_item_type_input']}'";
  	if($_POST['media_item_length_hours_input'] || $_POST['media_item_length_minutes_input'])
  	{
  		$query .= "
		,'{$_POST['media_item_length_hours_input']}:{$_POST['media_item_length_minutes_input']}:00'
  		";	
  	}
  	else
  	{
  		$query .= ",null";
  	}
  	if(preg_match('@^http://.*@',$_POST['media_item_image_location_input']))
  	{
  		$remote_image = file_get_contents($_POST['media_item_image_location_input']);
  		preg_match('@(.*?)\.([^\.]+)$@',basename($_POST['media_item_image_location_input']),$matches);
  		$_SESSION['firephp']->log($matches);
  		$local_image_name = urldecode($matches[1]);
  		$local_image_ext = $matches[2];
  		$counter = 2;
  		while(file_exists('images/media_items/' . $local_image_name))
  		{
  			$local_image_name = $local_image_name . "($counter)";
  			$counter++;
  		}
  		$fp = fopen("images/media_items/$local_image_name.$local_image_ext");
  		fwrite($fp,$remote_image);
  		$query .= ",'$local_image_name.$local_image_ext'";
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
  	$_SESSION['firephp']->log($query,'query');
  	mysql_query($query) or $_SESSION['firephp']->error(mysql_error());
  	if(is_array($_POST['media_item_genre_input']))
  	{
  		foreach($_POST['media_item_genre_input'] as $genre_id)
  		{
  			$query = "insert into media genre (genre_id,media_item_id)
  			values('$genre_id',LAST_INSERT_ID())
  			";
  			$_SESSION['firephp']->log($query);
  			mysql_query($query) or $_SESSION['firephp']->error(mysql_error());
  		}
  	}
  	
  	
//  	mysql_query($query) or $_SESSION['firephp']->error(mysql_error());
//  	media_item_genre_input
  	
  }
  else {
	$html .= "<form action='add_media_item.php' method='post'>
	
	<label for='media_item_title_input'>Title</label>
	<input name='media_item_title_input' id='media_item_title_input' type='text'></input>
	<div data-role='fieldcontain'>	
		<label for='media_item_type_input'>Media Type</label>
		<select name='media_item_type_input' id='media_item_type_input'>
		<option data-placeholder='true'></option>
		";
		$query = "select * from media_types order by media_type_desc";
		$query_result = mysql_query($query) or die(mysql_error());
		$media_types = mysql2array($query_result);
		foreach ($media_types as $media_type_row)
		{
			$html .= "<option value='{$media_type_row['media_type_id']}'>{$media_type_row['media_type_desc']}</option>";
		}
		$html .= "</select><br />
		<div id='media_item_length_input_div' style='display: none;'>
		<label for='media_item_length_hours_input'>Length (hours:minutes)</label><br />
		<input name='media_item_length_hours_input' id='media_item_length_hours_input' type='range' max='20' min='0'></input><br />
		<input name='media_item_length_minutes_input' id='media_item_length_minutes_input' type='range' max='59' min='0'></input>
		</div>
	
	
	<label for='media_item_medium_input'>Medium</label>
	<select name='media_item_medium_input' id='media_item_medium_input'>
	<option data-placeholder='true'></option>
	";
	$query = "select * from mediums order by medium_title";
	$query_result = mysql_query($query) or die(mysql_error());
	$mediums = mysql2array($query_result);
	foreach ($mediums as $medium_row)
	{
		$html .= "<option value='{$medium_row['medium_id']}'>{$medium_row['medium_title']}</option>";
	}
	$html .= "</select>
	</div>
			
	<label for='media_item_rating_input'>Rating</label>
	<input name='media_item_rating_input' id='media_item_rating_input' type='range' max='5' min='1'></input>
	
	<label for='media_item_image_location_input'>Thumbnail Location (relative)</label>
	<input name='media_item_image_location_input' id='media_item_image_location_input' type='text'></input>

	<label for='media_item_barcode_input'>Barcode</label>
	<input name='media_item_barcode_input' id='media_item_barcode_input' type='text'></input>

	<label for='media_item_isbn_input'>ISBN</label>
	<input name='media_item_isbn_input' id='media_item_isbn_input' type='text'></input>
	
	<label for='storage_location_input'>Storage Location</label><a id='edit_storage_location_button' data-role='button' style='float:right; z-index: 10;' href='edit_storage_location.php' data-iconpos='notext' data-icon='gear'>Edit</a>
	<select name='storage_location_input' id='storage_location_input'>
	";
	$query = "select * from storage_locations order by storage_title";
	$query_result = mysql_query($query) or die(mysql_error());
	$storage_locations = mysql2array($query_result);
	foreach ($storage_locations as $storage_location_row)
	{
		$html .= "<option value='{$storage_location_row['storage_location_id']}'>{$storage_location_row['storage_title']}</option>";
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
			$html .= "<input type='checkbox' name='media_item_genre_input' id='media_item_genre_{$genre_row['genre_id']}' value='{$genre_row['genre_id']}'></input>
			<label for='media_item_genre_{$genre_row['genre_id']}'>{$genre_row['genre_title']}</label>";
		}
	$html .= "
		</fieldset>
	</div>
	
	<label for='media_item_notes_input'>Notes</label>
	<textarea name='media_item_notes_input' id='media_item_notes_input'></textarea>
	
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