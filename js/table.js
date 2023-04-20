//

var bUnsavedData = false;

//takes html input elements of type="date"
function getData( from, to ){
	
	if(!from.value || !to.value){
		return;
	}
	
	var xhr = new XMLHttpRequest();
	
	document.getElementById('btnSave').disabled = true;
	document.getElementById('tblData').classList.add('ajax-loading');
	
	xhr.addEventListener('load', function( evt ){
		
		if( evt.target.response ){
			var response = JSON.parse( evt.target.response );
			
			if( response.success ){
				
				buildTable( response.data );
				
				/*
				Pull the summary data
				*/
				(function() {
					
					var xhr = new XMLHttpRequest();
					
					xhr.addEventListener('load', function( evt ){
						document.getElementById('summary').innerHTML = evt.target.response;
					});
					
					xhr.open('GET', 'php/ajax/summary.php');
					xhr.send();
				})();
				
			} else if( response.errors ) {
				setErrorMessage( response.errors );
			}
		}
		
		document.getElementById('btnSave').disabled = false;
		document.getElementById('tblData').classList.remove('ajax-loading');
	});
	
	xhr.open('GET', 'php/ajax/table.php?fromDate=' + encodeURIComponent(from.value) + '&toDate=' + encodeURIComponent(to.value));
	xhr.send();
}

/*
	POST all the dirty rows
*/
function saveData(){
	if ( !bUnsavedData ) return;
	
	document.getElementById('btnSave').disabled = true;
	document.getElementById('tblData').classList.add('ajax-loading');
	
	var xhr = new XMLHttpRequest();
	
	xhr.addEventListener('load', function( evt ){
		var response = JSON.parse( evt.target.response );
		
		if( response.success ){
			setSuccessMessage( "Saved" );
			bUnsavedData = false;
			getData( document.getElementById('inpFromDate'), document.getElementById('inpToDate') );
		} else if( response.errors) {
			setErrorMessage( response.errors );
		}
		document.getElementById('btnSave').disabled = false;
		document.getElementById('tblData').classList.remove('ajax-loading');
		
	});
	
	var payload = [];
	
	document.querySelectorAll('.table-warning').forEach(function( el ){
		
		var dirtyRow = {
			date : el.querySelector('td:nth-Child(1)').getAttribute('data-date'),
			kilograms : el.querySelector('td:nth-Child(2) > input').value,
			note : el.querySelector('td:nth-Child(6) > input').value
		};
		
		payload.push( dirtyRow );
	});
	
	clearErrors();
	
	xhr.open('POST', 'php/ajax/table.php');
	xhr.setRequestHeader('Content-type','application/json; charset=utf-8');
	xhr.send( JSON.stringify(payload) );
}

/*
	The table will have a row for every date between fromDate and toDate, regardless of whether "data" has an object for that row.
*/
function buildTable( data ){
	
	var tbody = document.querySelector('#tblData tbody');
	tbody.innerHTML = "";
	
	var fromDate = document.getElementById('inpFromDate').valueAsDate;
	var toDate = document.getElementById('inpToDate').valueAsDate;
	
	var getImageButton = function( image_exists ){
		if( (!image_exists || image_exists === null) || image_exists === '0' ){
			return ''
		} else if( image_exists === '1' ) {
			return '<i class="bi bi-image"></i>'
		}
	}
	
	for( var date = fromDate; date.getTime() <= toDate.getTime(); date.setTime(date.getTime() + (86400 * 1000))){
		
		var row = data.find(function(r){
			return r.date === date.toISOString().substring(0, 10);
		});
		
		//no row yet for this date, make an object for it
		if( ! row ){
			row = {
				kilograms:null,
				last_week_average:null,
				pounds:null,
				stone:null,
				note:null,
				image_exists:null
			}
		}
		
		tbody.innerHTML += 
			'<tr>' +
				'<td class="date-col' + ((date.getDay() == 6 || date.getDay() == 0) ? ' bg-secondary" ' : '" ') + 
					'data-date="'+ (date.toISOString().substring(0, 10)) + '">'+ date.toLocaleDateString() + '</td>' +
				'<td class="num-col-small"><input class="form-control" type="number" step=".1" value="'+ (row.kilograms ? Number(row.kilograms).toFixed(1) : '') +'"/></td>' +
				'<td class="num-col-small">'+ (row.last_week_average ? Number(row.last_week_average).toFixed(2) : '') + '</td>' +
				'<td class="num-col-small">'+ (row.pounds ? Number(row.pounds).toFixed(2) : '') + '</td>' +
				'<td class="num-col-small">'+ (row.stone ? row.stone : '') + '</td>' +
				'<td class="text-col-wide"><input class="form-control" type="text" value="'+ (row.note == null ? '' : row.note) + '" /></td>' +
				'<td class="button-col-small">'+ getImageButton(row.image_exists) +'</td>'
			'</tr>';
	}
	
	document.querySelectorAll('#tblData input').forEach(function( input ){
		input.addEventListener('change', function( evt ){
			evt.target.parentElement.parentElement.classList.add('table-warning');
			bUnsavedData = true;
		});
	});
	
	document.querySelectorAll('#tblData td:has(i.bi-image)').forEach(function(e){
		e.addEventListener('click', function( evt ){
			if( evt.target.localName == 'i' ){
				showImage(evt.target.parentElement.parentElement);
			} else {
				showImage(evt.target.parentElement);
			}
		});
	});
	
	var scrl = document.getElementById('scrAllTable');
	scrl.scrollTop = scrl.scrollHeight;
}

/*

*/
function showImage( tr ){
	var date = tr.querySelector('[data-date]').getAttribute('data-date');
	
	document.querySelector('#imagePanel img').setAttribute('src', 'php/ajax/image.php?date=' + encodeURIComponent(date));
	document.querySelector('#imagePanel h3').innerHTML = (new Date(date)).toLocaleDateString();
	new bootstrap.Modal(document.getElementById('imagePanel')).show();
}

/*
	Fetch button event handler
*/
document.getElementById('btnFetch').addEventListener('click', function( evt ){
	if( bUnsavedData ){
		if(!confirm('There is unsaved data on the page, continue?')) {
			return;
		} else {
			bUnsavedData = false;
		}
	} 
	
	clearErrors();
	
	getData( document.getElementById('inpFromDate'), document.getElementById('inpToDate') );
	
});

/*
	Save button event handler
*/
document.getElementById('btnSave').addEventListener('click', saveData);


/*
	Export button handler
*/
document.getElementById('btnExport').addEventListener('click', function(){
	
	window.location = 
		'php/ajax/table.php' +
		'?fromDate=' + encodeURIComponent(document.getElementById('inpFromDate').value) + 
		'&toDate=' + encodeURIComponent(document.getElementById('inpToDate').value) + 
		'&export';
});

/*
	Window handler for unsaved data
*/
window.addEventListener('beforeunload', function(e){
	if(bUnsavedData){
		(e || window.event).returnValue = "There is unsaved data on the page";
	}
});


/*
	Add functionality for "scroll to top" and "scroll to bottom" TODO: might be useless?
*/
(function(){
	var el = document.getElementById('scrollControls');
	var tbl = document.getElementById('scrAllTable');
	
	el.firstElementChild.addEventListener('click', function(){
		tbl.scrollTop = 0;
	});
	
	el.firstElementChild.nextElementSibling.addEventListener('click', function(){
		tbl.scrollTop = tbl.scrollHeight;
	});
	
})();

/*
	move "from date" 28 days in the past
*/
var fromDate = document.getElementById('inpFromDate');
fromDate.value = (new Date(fromDate.valueAsDate.getTime() - (28 * 24 * 60 * 60 * 1000)).toISOString().substr(0, 10));
var toDate = document.getElementById('inpToDate');

getData( fromDate, toDate );
