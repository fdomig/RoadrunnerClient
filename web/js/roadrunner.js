
jQuery(function() {
	
	var regexString = /^[\s\S]*$/;
	var regexFloat = /^[-+]?[0-9]*\.?[0-9]+([eE][-+]?[0-9]+)?$/;
	var regexInt = /^-{0,1}\d+$/;
	
	
	// ############### adds item to delivery item-list ##################
	
	var cdialog = $('.add-item-form');
	var removePersistentItemCount = 0;
	cdialog.find('.create-item-button').click(function(e) {
		e.preventDefault();
		
		var inputName = $(this).parent().parent().find('.input-name');
		var inputMaxTemp = $(this).parent().parent().find('.input-max-temp'); 
		var inputMinTemp = $(this).parent().parent().find('.input-min-temp');
		
		var valid = validateItem(cdialog, inputName, inputMinTemp, inputMaxTemp);
		if (valid) {
			var newItem = $('<tr class="create">'+
								'<td><code>Not yet saved</code></td>' + 
								'<td>'+ inputName.val() + '</td>' + 
								'<td>&nbsp;</td>' + 
								'<td><a href="" class="remove">Remove</a></td>' + 
							'</tr>');
			newItem.data('item',{
				name: inputName.val(),
				mintemp: inputMinTemp.val(),
				maxtemp: inputMaxTemp.val()
			});
			newItem.find('a.remove').click(function(e){
				e.preventDefault();
				$(this).parent().parent().remove();
				return false;
			});
		
			cdialog.dialog("destroy");
			inputName.val("");
			inputMaxTemp.val("");
			inputMinTemp.val("");
	
			$('#item-list tbody').append(newItem);
		}
		return false;
	});
	
	var validateItem = function(context, name, mintemp, maxtemp) {
		var valid = true;
		if (!name.val().match(regexString) || name.val().length <= 0) {
			name.parent().addClass('error');
			valid = false;
		} else if (name.parent().hasClass('error')) {
			name.parent().removeClass('error');
		}
		if (!mintemp.val().match(regexFloat) || mintemp.val().length <= 0) {
			mintemp.parent().addClass('error');
			valid = false;
		} else if (mintemp.parent().hasClass('error')) {
			mintemp.parent().removeClass('error');
		}
		if (!maxtemp.val().match(regexFloat) || maxtemp.val().length <= 0) {
			maxtemp.parent().addClass('error');
			valid = false;
		} else if (maxtemp.parent().hasClass('error')) {
			maxtemp.parent().removeClass('error');
		}
		var mint = parseFloat(mintemp.val());
		var maxt = parseFloat(maxtemp.val());
		if (valid && mint > maxt) {
			context.append('<div class="error-minmax error">Minimum Temperature cannot be greater than maximum Temperature</div>');
			valid = false;
		} else if (context.find('.error-minmax').length > 0) {
			context.find('.error-minmax').remove();
		}
		return valid
	}
	
	$('.add-item-to-delivery').click(function(e) {
		e.preventDefault();
		RR_AKS_CONFIRM = true;
		cdialog.dialog({
			title: "Add a new Item to this Delivery.",
			height: 460,
			width: 300,
			modal: true,
			resizable: false
		});
		cdialog.find('.input-name').parent().removeClass('error');
		cdialog.find('.input-min-temp').parent().removeClass('error');
		cdialog.find('.input-max-temp').parent().removeClass('error');
		cdialog.find('.error-minmax').remove();
		return false;
	});
	
	$('.remove-item-from-delivery').click(function(e) {
		e.preventDefault();
		var item = $(this).parent().parent();
		if (item.hasClass('persistent')) {
			RR_AKS_CONFIRM = true;
			$('.form-create-delivery fieldset').append(
				'<input type="hidden" name="input-remove-item-' + removePersistentSensorCount + '" value="' + item.find('.item-id').text() + '" />'
			);
			item.remove();
			removePersistentItemCount++;
		}
		return false;
	});
	
	$('.create-delivery-button').click(function() {
		RR_AKS_CONFIRM = false;
		
		var list = $('#item-list').find('.create');
		var items = '';
		$.each(list, function(index){
			items += $(this).data('item').name + '|' + $(this).data('item').mintemp + '|' + $(this).data('item').maxtemp;
			if (index != list.length-1) {
				items += ',';
			}
		});
		if (list.length > 0) {
			$('.form-create-delivery').append('<input type="hidden" name="create-item-list" value="' + items + '"/>');
		}
		$('.form-create-delivery').append('<input type="hidden" name="nr-of-items-to-remove" value="' + removePersistentItemCount + '"/>');
	});
	
	
	// ################ add Sensors to Container ################
	
	var sensorDialog = $('.add-sensor-form');
	var removePersistentSensorCount = 0;
	sensorDialog.find('.create-sensor-button').click(function(e) {
		e.preventDefault();
		var inputUrl = $(this).parent().parent().find('.input-uri');
		if (inputUrl.val().length > 0) {
			var newSensor = $('<tr class="create"><td class="sensor-uri">'+ inputUrl.val()+ '</td><td><a href="" class="remove">Remove</a></td></tr>');
			$('#sensor-list tbody').append(newSensor);
			
			newSensor.find('a.remove').click(function(e){
				e.preventDefault();
				$(this).parent().parent().remove();
				return false;
			});
			
			RR_AKS_CONFIRM = true;
			sensorDialog.dialog("destroy");
			inputUrl.val("");
		} else {
			inputUrl.parent().addClass('error');
		}
		return false;
	});
	
	$('.add-sensor-to-container').click(function(e) {
		e.preventDefault();
		sensorDialog.dialog({
			title: "Add a new Sensor to this Unit.",
			height: 150,
			width: 300,
			modal: true,
			resizable: false
		});
		sensorDialog.find('.error').removeClass('error');
		return false;
	});
	
	$('.create-container-button').click(function() {
		RR_AKS_CONFIRM = false;

		var list = $('#sensor-list').find('.create > .sensor-uri');
		var sensors = '';
		$.each(list, function(index){
			sensors += $(this).text();
			if (index != list.length-1) {
				sensors += ',';
			}
		});
		if (list.length > 0) {
			$('.form-create-container').append('<input type="hidden" name="create-sensor-list" value="' + sensors + '"/>');
		}
		$('.form-create-container').append('<input type="hidden" name="nr-of-sensors-to-remove" value="' + removePersistentSensorCount + '"/>');
	});
	
	
	$('.remove-sensor-from-container').click(function(e) {
		e.preventDefault();
		var sensor = $(this).parent().parent();
		if (sensor.hasClass('persistent')) {
			RR_AKS_CONFIRM = true;
			$('.form-create-container fieldset').append(
				'<input type="hidden" name="input-remove-sensor-' + removePersistentSensorCount + '" value="' + sensor.find('.sensor-uri').text() + '" />'
			);
			sensor.remove();
			removePersistentSensorCount++;
		}
		return false;
	});
	
	/*
	 * ############   LOGS VIEW ############
	 * 
	 */
	
	$('.view-signature').click(function(e) {
		e.preventDefault();
		$.nyroModalManual({
			url: $(this).attr('href'),
			title: 'Signature',
			bgColor: '#ffffff'
		});
		return false;
	});
});