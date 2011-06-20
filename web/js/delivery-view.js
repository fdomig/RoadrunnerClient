jQuery(function() {
	
	var map;
	var image;
	var markersArray = [];
	var directionDisplay;
	var infowindow = null;
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
			drawPositions($(this).attr('id'));
		});	
	}
	
	/**
	 * createMarkerImage
	 *  
	 * Every marker_image.png has to prefix with 'marker_'
	 * Add: must be stored as png file
	 * 
	 * @param path String
	 * @return google.maps.MarkerImage
	 */
	function createMarkerImage(path, width, height) {
		
		return new google.maps.MarkerImage(path,
			// This marker is 20 pixels wide by 32 pixels tall.
			new google.maps.Size(width, height),
			// The origin for this image is 0,0.
			new google.maps.Point(0,0),
	      	// The anchor for this image is the base of the flagpole at 0,32.
	      	new google.maps.Point(width/2, height));
	}

	function addMarker(location, title, icon) {
		var marker = new google.maps.Marker({
			position: location,
			map: map,
			title: title,
			icon: icon
		});
		google.maps.event.addListener(marker, 'click', function() {
			if (infowindow) {
				infowindow.close();
			}
			infowindow = new google.maps.InfoWindow({
				content: title
			});
			infowindow.open(map,marker);
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
				travelMode: google.maps.DirectionsTravelMode.DRIVING
			}
			directionsService.route(options, function(result, status) {
				if (status == google.maps.DirectionsStatus.OK) {
					directionsDisplay.setDirections(result);
				}
			});
		});
	}
	
	function drawPositions(id) {
		$.getJSON('/delivery/routes/' + id, function(routes) {
			
			for (var i = 0; i < routes.results.length; i++) {
				var route = routes.results[i];
				for (var j = 0; j < route.length; j++) {
					var cr = route[j];
					addMarker(
						new google.maps.LatLng(cr.pos.lat, cr.pos.lng),
						cr.info.msg + '<br>' + new Date(cr.info.time*1000) + '<br>Route: ' + cr.rid,
						createMarkerImage(cr.img.path, cr.img.width, cr.img.height)
					);
//					if (j+1 < route.length) {
//						drawLine(cr.pos, route[j+1].pos);
//					}
				}
			}
			setItemMarkers(routes.items);
		});
	}
	
	var setItemMarkers = function(items) {
		for (var k=0; k < items.length; k++) {
			$('#'+items[k].id + ' > .item-route').html('<img src="' + items[k].img +'" style="height: 20px;"/>');
		}
	}
	
	
//	function drawLine(a, b) {
//		line = [[parseFloat(a.lng), parseFloat(a.lat)],[parseFloat(b.lng), parseFloat(b.lat)]];
//		var geojson = {
//				"type": "LineString",
//				"coordinates": line
//			};
//		
//		console.log(geojson);
//
//			var options = {
//				strokeColor: "#FFFF00",
//				strokeWeight: 3,
//				strokeOpacity: 1.0,
//				map: map
//			};
//
//			var vector = new GeoJSON(geojson, options);
//			vector.setMap(map);
//	} 

	initialize();

});