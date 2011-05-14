jQuery(function() {

	// adds item to delivery item-list
	var cdialog = $('.add-item-form');
	
	cdialog.find('.create-item-button').click(function(e) {
		e.preventDefault();
		var inputName = $(this).parent().parent().find('.input-name');
		var inputMaxTemp = $(this).parent().parent().find('.input-max-temp'); 
		var inputMinTemp = $(this).parent().parent().find('.input-min-temp'); 
		$('#item-list tbody').append('<tr><td>undefined</td><td>'
				+ inputName.val()
				+ '</td></tr>');
		
		cdialog.dialog("destroy");
		inputName.val("");
		inputMaxTemp.val("");
		inputMinTemp.val("");
		return false;
	});
	
	$('.add-item-to-delivery').click(function(e) {
		e.preventDefault();
		cdialog.dialog({
			title: "Add a new Item to this Delivery.",
			height: 370,
			width: 300,
			modal: true,
			resizable: false
		});
		return false;
	});
	
});