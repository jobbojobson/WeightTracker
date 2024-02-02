//

google.charts.load('current', {'packages':['line']});
google.charts.setOnLoadCallback(updateChartData);

var data;

async function updateChartData(){
	let fromDate = document.getElementById('inpFromDate').value;
	let toDate = document.getElementById('inpToDate').value;
	
	let r = await fetch( 'php/ajax/data.php?fromDate=' + encodeURIComponent(fromDate) + '&toDate=' + encodeURIComponent(toDate) );
	let d = await r.json();
	
	if( ! d.success ) return;
	
	var c = [];
	
	d.data.forEach( r => {
		c.push([ new Date(r.date), Number(r.last_week_average) ]);
	});
	
	let dt = new google.visualization.DataTable();
	dt.addColumn('date', 'Date');
	dt.addColumn('number', '7 Day Rolling Average');
	dt.addRows( c );
	
	data = dt;
	drawChart();
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


var fromDate = document.getElementById('inpFromDate');
var toDate = document.getElementById('inpToDate');

if(sessionStorage.getItem('chartFromDate')){
	fromDate.value = new Date(sessionStorage.getItem('chartFromDate')).toISOString().substr(0, 10);
} else {
	/*
		move "from date" 90 days in the past
	*/
	fromDate.value = 
		(new Date(fromDate.valueAsDate.getTime() - (90 * 24 * 60 * 60 * 1000)).toISOString().substr(0, 10));
}

if(sessionStorage.getItem('chartToDate')){
	toDate.value = new Date(sessionStorage.getItem('chartToDate')).toISOString().substr(0, 10);
}

document.getElementById('btnFetch').addEventListener('click', () => {
	
	sessionStorage.setItem('chartFromDate', document.getElementById('inpFromDate').value);
	sessionStorage.setItem('chartToDate', document.getElementById('inpToDate').value);
	
	updateChartData();
});

window.addEventListener("resize", drawChart);
