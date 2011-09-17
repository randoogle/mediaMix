<?php
/*
TO DO:
  -import season
  -change epTitle on viewEps to be an <a href..>epTitle</a>
  -modify episode
  -show image
  add show
  modify show
  add episode
  view stats for all shows
  sort

*/
  
  $html = '
  
<!DOCTYPE html> 
<html> 
	<head>
	<title>MediaMix Database</title> 
	
	<meta name="viewport" content="width=device-width, initial-scale=1">
  	<link rel="stylesheet" href="includes/css/jquery.mobile-1.0b2.min.css" />
  	<link rel="stylesheet" href="includes/css/mediaMix.css" />
  	<link rel="apple-touch-icon" href="images/iOS_icon.png" />
	<script type="text/javascript" src="includes/js/jquery-1.6.2.min.js"></script>
	<script type="text/javascript" src="includes/js/jquery.mobile-1.0b3.min.js"></script>
	<script type="text/javascript" src="includes/js/add_media_item.js"></script>
	</head>
	<body>	
';
  include_once('includes/header.php');
  $html .= "<div data-role='page' data-title='MediaMix Database'>
  	<div data-role='header'>MediaMix Database</div>
  	<div data-role='content'>
  	<ul data-role='listview' data-theme='g'>
  	<li><form><input id='search_titles' type='search'></input></form></li>
  	<li><a href='all_media.php'>All Items</a></li>
  	<li>Genre
  		<ul data-role='listview'>
  	";
  $query = "select * from genres where genres.genre_id in (select media_genre.genre_id from media_genre) order by genres.genre_title";
  $query_result = mysql_query($query) or die(mysql_error());
  $genres = mysql2array($query_result);
  foreach($genres as $genre_row)
  {
  	$html .= "<li><a class='query_link' href='all_media.php?table=media_genre&value={$genre_row['genre_id']}'>{$genre_row['genre_title']}</a></li>";
  }
  $html .= "
  		</ul>
  	</li>
  	<li>Media Type
  		<ul>";
  $query = "select * from media_types order by media_type_desc";
  $query_result = mysql_query($query) or $_SESSION['firephp']->error(mysql_error());
  $media_types = mysql2array($query_result);
  foreach($media_types as $media_type_row)
  {
  	$html .= "<li><a class='query_link' href='all_media.php?table=media_items&field=media_types.media_type_id&value={$media_type_row['media_type_id']}'>{$media_type_row['media_type_desc']}</a></li>";
  }
  $html .="
  		</ul>
  	</li>
  	</ul>
  	
  	</div>
  </div>
  </body>
  </html>
  ";
  echo $html;
?>
