jQuery(function() {
	
	// highchart
	var chart;
	var tempdata = [];
	var itemCount = $('.item-status').length;
	
	$('.item-status').each(function() {
		// item status
		$.getJSON('/item/status/' + $(this).parent().attr('id'), function(data) {
			$('#' + data.id).find('.item-status').text(data.status);
		});
		// temp tracking
		$.getJSON('/item/templogs/' + $(this).parent().attr('id'), function(data) {
			tempdata.push(data);
			if (tempdata.length == itemCount) {
				drawChart();
			}
		});
	});
	
	function drawChart() {
		chart = new Highcharts.Chart({
			chart: {
				renderTo: 'delivery-temp-tracking',
				defaultSeriesType: 'line'
			},
			title: {
				text: 'Temperature Data'
			},
			xAxis: {
				categories: getCategories()
			},
			yAxis: {
				title: {
					text: 'Temperature in Celsius'
				}
			},
			series: getSeries()
		});
	}
	
	function getSeries() {
		series = [];
		$.each(tempdata, function(index) {
			series.push({
				name: tempdata[index].name,
				data: getData(tempdata[index].logs) 
			});
		});
		return series;
	}
	
	function getCategories() {
		categories = [];
		$.each(tempdata, function(index) {
			$.each(tempdata[index].logs, function(index2) {
				categories.push(formatTime(tempdata[index].logs[index2].timestamp));
			});
		});
		return categories.sort(function(a, b) { return a - b; });
	}
	
	function getData(logs) {
		data = [];
		$.each(logs, function(index) {
			data.push(logs[index].value)
		});
		return data;
	}
	
	formatTime = function(unixTimestamp) {
	    var dt = new Date(unixTimestamp * 1000);

	    var hours = dt.getHours();
	    var minutes = dt.getMinutes();

	    // the above dt.get...() functions return a single digit
	    // so I prepend the zero here when needed
	    if (hours < 10) 
	     hours = '0' + hours;

	    if (minutes < 10) 
	     minutes = '0' + minutes;

	    return hours + ":" + minutes;
	}
	
});


