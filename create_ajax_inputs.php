<?php
include_once('includes/header.php');
$html = "";
switch($_POST['input_id'])
{
	case 'storage_location_input':
		$query = "select * from storage_slots where storage_slots.storage_location_id = {$_POST['value']}";
		$slots_array = getQueryArray($query);
//		$_SESSION['firephp']->log($slots_array,'slots_array');
//		$html .= "<label for='storage_slot_id_input'>Storage Slot</label>";
//		$html .= "<select id='storage_slot_id_input' name='storage_slot_id_input'>";
		foreach ($slots_array as $slot_array)
		{
			$html .= "<option value='{$slot_array['storage_slot_id']}'>{$slot_array['storage_slot_label']}</option>";
		}
//		$html .= "</select>";
		break;
	default:
		break;
}
echo $html;
?>