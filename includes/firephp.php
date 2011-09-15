<?php
  session_start();
  include_once('includes/firephp/FirePHP.class.php');
  $firephp = FirePHP::getInstance(true);
  $_SESSION['firephp'] = $firephp;
?>