<?php
include_once('includes/header.php');
$html = "";
switch($_POST['input_id'])
{
	case 'storage_location_input':
		$query = "select * from storage_slots where storage_slots.storage_location_id = {$_POST['value']}";
		$slots_array = getQueryArray($query);
//		$_SESSION['firephp']->log($slots_array,'slots_array');
		foreach ($slots_array as $slot_array)
		{
			$selected = "";
			if($slot_array['storage_slot_id'] == $_POST['selected'])
			{
				$selected = "selected='selected'";
			}
			$html .= "<option $selected value='{$slot_array['storage_slot_id']}'>{$slot_array['storage_slot_label']}</option>";
		}

		break;
	default:
		break;
}
echo $html;
?>