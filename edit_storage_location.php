<?php 
  $html = "
<!DOCTYPE html> 
<html>
	<head>";
  include_once('includes/header.php');
  $html .= "
  </head>
  <body>
		<div data-role='content'>
		place holder for editing storage locations<br />
		Location: {$_GET['location']}
		</div>
  </body>";
echo $html;
?>