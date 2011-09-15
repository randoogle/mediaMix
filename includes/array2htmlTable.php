<?php

function array2html($array,$headings = false)
{  /*
  ---------------------------------------------------------------------------
    BEGIN function array2html
    This function assumes this array structure:
    Array =>
    (
      [1]=> Array =>
            (
              ['key']=>'value';
              ['key2']=> 'value;
            )
      [2]=> Array =>
            (
              ['key']=>'value';
            )
      ...
    )
  ---------------------------------------------------------------------------
  */
  
  $content = '';
  
  if(count($array))
  {
    $content .= '<table border="1">';
  //if headings is true, then put headings in there
//   	print_r($array);//debugging
    if($headings)
    {
      $content .= '<tr style="background-color: rgb(102,153,204); color:rgb(255,255,255)">';
      foreach ($array[0] as $key=>$value)
      {
        $content .= '<th>' . $key . '</th>';
      }
      $content .= '</tr>';
    }
    $counter = 0;
    $style = "";
    
    foreach ($array as $subArray)
    {
		if(($counter % 2) == 1)
		{
			$style = 'style="background-color: rgb(204,204,255)" valign="top"';
		}
		else
		{
			$style = 'valign="top"';
		}

      $content .= "<tr $style>";
      foreach ($subArray as $key=>$value)
      {
      	$content .= '<td>' . $value . '</td>';
      }
      $content .= '</tr>';
      $counter ++;
    }
    $content .= '</table>';

  }
  
    
    return $content;
	
} // END function array2html
?>
