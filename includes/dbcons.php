<?php
  function db_connect()
  {
//     $hostname = $_SERVER['HTTP_HOST'];
    $hostname = "localhost";
    $database_host = $hostname;
//     print "hostname = $hostname<br/>";
    if($hostname == "rhunt.php.cs.dixie.edu")
    {
      $database_host = "mysql.cs.dixie.edu";#if it's the school server, then use this for dbpath
    }
  
    
    #$maindomain ="rhunt.php.cs.dixie.edu";
    $maindomain = $hostname;
    
    $username ="mediamix_user";
    
    $password ="whatever";
    
    $db ="mediamix";
    
    $dbpath="$database_host";
    #$dbpath="mysql.cs.dixie.edu";
    
    $resource_link = mysql_connect($dbpath, $username, $password) or die(mysql_error());
    
    mysql_select_db($db) or die(mysql_error());
    return $resource_link;
  }

?>
