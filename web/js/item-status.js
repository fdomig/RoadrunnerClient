jQuery(function() {
	$('.item-status').each(function() {
		$.getJSON('/item/status/' + $(this).parent().attr('id'), function(data) {
			$('#' + data.id).find('.item-status').text(data.status);
		});
	});
});