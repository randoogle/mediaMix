<?php

  function mysql2array($query_result)
  {
    $i = 0;
    $array = array();
	    while($row = mysql_fetch_assoc($query_result))
	    {
	      foreach ($row as $key=>$value)
	      {
	      	$array[$i][$key] = $value;
	      }
	  
	      $i++;
	      
	    }
    return $array;
  }


?>
