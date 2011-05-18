jQuery(function() {
	$('.item-status').each(function() {
		$.getJSON('/item/status/' + $(this).attr('id'), function(data) {
			$('#' + data.id).text(data.status);
		});
	});
});