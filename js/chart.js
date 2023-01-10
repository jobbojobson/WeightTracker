//

google.charts.load('current', {'packages':['line']});
google.charts.setOnLoadCallback(updateChart);

function updateChart( ){
	var xhr = new XMLHttpRequest();
	
	var fromDate = document.getElementById('inpFromDate').value;
	var toDate = document.getElementById('inpToDate').value;
	
	var slider = document.getElementById('inpChartRange');
	
	xhr.addEventListener('load', function( evt ){
		var response = JSON.parse( evt.target.response );
		
		if( ! response.success ) return; //TODO
		
		//turn the ISO8601 strings into JS Dates
		for( var i = 0; i < response.data.length; i++ ){
			response.data[i][0] = new Date(response.data[i][0]);
		}
				
		var chartData = new google.visualization.DataTable();
		chartData.addColumn('date', 'Date');
		chartData.addColumn('number', '7 Day Rolling Average');
		chartData.addRows( response.data );
		
		var options = {
			backgroundColor: '#282828',
			chartArea: {
				backgroundColor: '#282828'
			},
			
			curvetype:'function',
			legend: { 
				position:'none'
			},
			
			
			trendlines: {
				0: {
					type:'exponential',
					opacity:0.2
				}
			},
			hAxis: { 
				textStyle: { color: '#c8c8c8' }, 
				title: 'Date',
				format: 'dd MMM yyyy',
				gridlines: {
					count:4
				},
				minorGridlines: { 
					count: 4,
					interval:1
				}
			},
			vAxis: { 
				textStyle: { color: '#c8c8c8' }, 
				title: 'KG' 
			},
			width:'800',
			height:'600'
		}
		
		var formatter = new google.visualization.NumberFormat({pattern:'0.00'});
		formatter.format(chartData, 1);
		
		var chart = new google.charts.Line(document.getElementById('lineChart'));
		chart.draw( chartData, google.charts.Line.convertOptions(options) );
	});
	
	xhr.open('GET', 'php/ajax/chart.php?fromDate=' + encodeURIComponent(fromDate) + '&toDate=' + encodeURIComponent(toDate) );
	xhr.send();
}

/*
	move "from date" 90 days in the past
*/
var fromDate = document.getElementById('inpFromDate');
fromDate.value = (new Date(fromDate.valueAsDate.getTime() - (90 * 24 * 60 * 60 * 1000)).toISOString().substr(0, 10));
var toDate = document.getElementById('inpToDate');

document.getElementById('btnFetch').addEventListener('click', updateChart);








