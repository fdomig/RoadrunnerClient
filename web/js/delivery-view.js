jQuery(function() {
	
	var map;
	var markersArray = [];

	function initialize() {
		var home = new google.maps.LatLng(47.413417, 9.744417);
		var mapOptions = {
			zoom: 12,
			center: home,
			mapTypeId: google.maps.MapTypeId.TERRAIN
		};
		map = new google.maps.Map(document.getElementById("map_canvas"),
			mapOptions
		);

		$('.delivery-entry').each(function() {
			getDirections($(this).attr('id'));
		});

		$('.item-entry').each(function() {
			getRoute($(this).attr('id'));
		});
		
	}

	function addMarker(location) {
		marker = new google.maps.Marker({
			position: location,
			map: map
		});
		markersArray.push(marker);
	}

	// Removes the overlays from the map, but keeps them in the array
	function clearOverlays() {
		if (markersArray) {
			for (i in markersArray) {
				markersArray[i].setMap(null);
			}
		}
	}

	// Shows any overlays currently in the array
	function showOverlays() {
		if (markersArray) {
			for (i in markersArray) {
				markersArray[i].setMap(map);
			}
		}
	}

	// Deletes all markers in the array by removing references to them
	function deleteOverlays() {
		if (markersArray) {
			for (i in markersArray) {
				markersArray[i].setMap(null);
			}
			markersArray.length = 0;
		}
	}

	function getRoute(id) {
		$.getJSON('/item/route/' + id, function(data) {
			// TODO
		});
	}

	function getDirections(id) {
		$.getJSON('/delivery/directions/' + id, function(data) {
			// TODO
		});
	}

	initialize();

});