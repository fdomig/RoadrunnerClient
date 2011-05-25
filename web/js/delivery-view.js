jQuery(function() {
	
	var map;
	var markersArray = [];
	var directionDisplay;
	var directionsService = new google.maps.DirectionsService();

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
		directionsDisplay = new google.maps.DirectionsRenderer();
		directionsDisplay.setMap(map);

		$('.delivery-entry').each(function() {
			drawDirections($(this).attr('id'));
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

	function drawDirections(id) {
		$.getJSON('/delivery/directions/' + id, function(route) {
			options = {
				origin: route.origin,
				destination: route.destination,
				travelMode: google.maps.DirectionsTravelMode.DRIVING,
			}
			directionsService.route(options, function(result, status) {
				if (status == google.maps.DirectionsStatus.OK) {
					directionsDisplay.setDirections(result);
				}
			});
		});
	}

	initialize();

});