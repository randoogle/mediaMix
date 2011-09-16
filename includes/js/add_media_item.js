$('div').live('pagecreate',function(){


	$('#media_item_type_input').change(function(){
		if(this.value == 2)
		{
			$('#media_item_length_input_div').show('slow');
		}
		else
		{
			$('#media_item_length_input_div').hide('slow');
		}
	});
	//change the storage slot select list to reflect the currently select storage location
	$('#storage_location_input').change(function(){
		$('#storage_slot_id_input').load('create_ajax_inputs.php',{input_id:'storage_location_input',value:this.value,selected:$(this).attr('storage_slot')}).trigger("change");
		$('#edit_storage_location_button').attr('href',"edit_storage_location.php?location=" + this.value);
	});
	//now trigger it once so it will be populated on load
	$('#storage_location_input').trigger('change');
	$('#media_item_type_input').trigger('change');
});