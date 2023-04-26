//

google.charts.load('current', {'packages':['line']});
google.charts.setOnLoadCallback(updateChartData);

var data;

function updateChartData( ){
	
	var xhr = new XMLHttpRequest();
	
	var fromDate = document.getElementById('inpFromDate').value;
	var toDate = document.getElementById('inpToDate').value;
	
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
		
		data = chartData;
		drawChart();
	});
	
	xhr.open('GET', 'php/ajax/chart.php?fromDate=' + encodeURIComponent(fromDate) + '&toDate=' + encodeURIComponent(toDate) );
	xhr.send();
}


function drawChart( ){
	
	var width = document.getElementById('lineChart').clientWidth;
	var height = (width / 5) * 3;
	
	var options = {
		backgroundColor: '#343a40',
		chartArea: {
			backgroundColor: '#343a40'
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
			format: 'MMMM YYYY',
			title:'',
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
			title: 'KG',
			gridLines: {
				count:4
			},
			minorGridLines: {
				count:4
			}
		},
		width:width,
		height:height
	}
	
	var formatter = new google.visualization.NumberFormat({pattern:'0.00'});
	formatter.format(data, 1);
	
	var chart = new google.charts.Line(document.getElementById('lineChart'));
	chart.draw( data, google.charts.Line.convertOptions(options) );
}


/*
	move "from date" 90 days in the past
*/
var fromDate = document.getElementById('inpFromDate');
fromDate.value = (new Date(fromDate.valueAsDate.getTime() - (90 * 24 * 60 * 60 * 1000)).toISOString().substr(0, 10));
var toDate = document.getElementById('inpToDate');

document.getElementById('btnFetch').addEventListener('click', updateChartData);

window.addEventListener("resize", drawChart);








