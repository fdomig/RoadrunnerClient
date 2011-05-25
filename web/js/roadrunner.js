
jQuery(function() {
	
	// ############### adds item to delivery item-list ##################
	
	var cdialog = $('.add-item-form');
	var itemCount = 0;
	var removePersistentItemCount = 0;
	cdialog.find('.create-item-button').click(function(e) {
		e.preventDefault();
		var inputName = $(this).parent().parent().find('.input-name');
		var inputMaxTemp = $(this).parent().parent().find('.input-max-temp'); 
		var inputMinTemp = $(this).parent().parent().find('.input-min-temp');
		
		var newItem = $('<tr class="create"><td>New Item</td><td>'+ inputName.val()+ '</td><td>&nbsp;</td><td><a href="" class="remove">Remove</a></td></tr>');
		
		newItem.find('a.remove').click(function(e){
			e.preventDefault();
			$(this).parent().parent().remove();
			return false;
		});
		
		$('#item-list tbody').append(newItem);
		$('.form-create-delivery fieldset').append(
			'<div>\
				<input type="hidden" name="input-name-hidden-' + itemCount + '" value="' + inputName.val() +'" /> \
				<input type="hidden" name="input-max-temp-hidden-' + itemCount + '" value="' + inputMaxTemp.val() +'" /> \
				<input type="hidden" name="input-min-temp-hidden-' + itemCount + '" value="' + inputMinTemp.val() +'" /> \
			</div>');
		
		itemCount++;
		cdialog.dialog("destroy");
		inputName.val("");
		inputMaxTemp.val("");
		inputMinTemp.val("");
		return false;
	});
	
	$('.add-item-to-delivery').click(function(e) {
		e.preventDefault();
		RR_AKS_CONFIRM = true;
		cdialog.dialog({
			title: "Add a new Item to this Delivery.",
			height: 370,
			width: 300,
			modal: true,
			resizable: false
		});
		return false;
	});
	
	$('.remove-item-from-delivery').click(function(e) {
		e.preventDefault();
		var item = $(this).parent().parent();
		if (item.hasClass('persistent')) {
			RR_AKS_CONFIRM = true;
			$('.form-create-delivery fieldset').append(
				'<input type="hidden" name="input-remove-item-' + removePersistentSensorCount + '" value="' + item.find('.item-id').text() + '" />;'
			);
			item.remove();
			removePersistentItemCount++;
		}
		return false;
	});
	
	$('.create-delivery-button').click(function() {
		RR_AKS_CONFIRM = false;
		$('.form-create-delivery').append(
			'<input type="hidden" name="nr-of-items-to-remove" value="' + removePersistentItemCount + '"/>' +	
			'<input type="hidden" name="nr-of-items" value="'+ itemCount +'"/>');
	});
	
	
	// ################ add Sensors to Container ################
	
	var sensorDialog = $('.add-sensor-form');
	var sensorCount = 0;
	var removePersistentSensorCount = 0;
	sensorDialog.find('.create-sensor-button').click(function(e) {
		e.preventDefault();
		var inputUrl = $(this).parent().parent().find('.input-uri');
		
		var newSensor = $('<tr class="create"><td>'+ inputUrl.val()+ '</td><td><a href="" class="remove">Remove</a></td></tr>');
		$('#sensor-list tbody').append(newSensor);
		
		newSensor.find('a.remove').click(function(e){
			e.preventDefault();
			$(this).parent().parent().remove();
			return false;
		});
		
		$('.form-create-container fieldset').append(
			'<div>\
				<input type="hidden" name="input-uri-hidden-' + sensorCount + '" value="' + inputUrl.val() +'" />\
			</div>');
		
		sensorCount++;
		RR_AKS_CONFIRM = true;
		sensorDialog.dialog("destroy");
		inputUrl.val("");
		return false;
	});
	
	$('.add-sensor-to-container').click(function(e) {
		e.preventDefault();
		sensorDialog.dialog({
			title: "Add a new Sensor to this Unit.",
			height: 120,
			width: 300,
			modal: true,
			resizable: false
		});
		return false;
	});
	
	$('.create-container-button').click(function() {
		RR_AKS_CONFIRM = false;
		$('.form-create-container').append(
			'<input type="hidden" name="nr-of-sensors-to-remove" value="' + removePersistentSensorCount + '"/>' +
			'<input type="hidden" name="nr-of-sensors" value="'+ sensorCount +'"/>"'
		);
	});
	
	$('.remove-sensor-from-container').click(function(e) {
		e.preventDefault();
		var sensor = $(this).parent().parent();
		if (sensor.hasClass('persistent')) {
			RR_AKS_CONFIRM = true;
			$('.form-create-container fieldset').append(
				'<input type="hidden" name="input-remove-sensor-' + removePersistentSensorCount + '" value="' + sensor.find('.sensor-uri').text() + '" />;'
			);
			sensor.remove();
			removePersistentSensorCount++;
		}
		return false;
	});
	
});