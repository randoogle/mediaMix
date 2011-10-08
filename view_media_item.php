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
  <body>
  	<div data-role='page'>
	  		<div data-role='header'>
	  			<form style='float:right; margin-top: 5px;' method='POST' action='add_media_item.php'><input id='edit_media_item_button' type='submit' data-icon='gear' data-iconpos='right' name='Edit' value='Edit' title='edit'></input><input type='hidden' name='edit' value='true' title='edit'></input><input type='hidden' name='media_item_id' value='{$_GET['item']}' /></form>
	  			<a href='index.php' data-rel='back' data-icon='arrow-l'>Back</a>
	  			<h3>View Item</h3>
	  		</div>
	";
  $_SESSION['firephp']->log($_GET,'get');
  $html .= mediaItemHtml($_GET['item']);
  $html .= "
  	</div>
  </body>
  </html>
  ";
  
  echo $html;

?>