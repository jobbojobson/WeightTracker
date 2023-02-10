
var bUnsavedData = false;

(function(){
	var lbl = document.getElementById('spnDate');
	lbl.innerHTML = new Date(lbl.getAttribute('data-date')).toLocaleDateString();
	
	var xhr = new XMLHttpRequest();
	
	document.getElementById('btnSave').disabled = true;
	
	xhr.addEventListener('load', function(evt){
		var response = JSON.parse( evt.target.response );
		
		if( response.data && response.data.length > 0 ){
			document.getElementById('inpDayValue').value = response.data[0].kilograms.toFixed(1);
		}
		
		document.getElementById('btnSave').disabled = false;
	});
	
	var queryString = '?fromDate=' + encodeURIComponent(lbl.getAttribute('data-date')) + '&toDate=' + encodeURIComponent(lbl.getAttribute('data-date'));
	xhr.open('GET', 'php/ajax/table.php' + queryString);
	xhr.send();
})()



document.getElementById('inpDayValue').addEventListener('change', function(evt){
	bUnsavedData = true;
});

document.getElementById('btnSave').addEventListener('click', function(evt){
	if( !bUnsavedData ) return;
	
	document.getElementById('btnSave').disabled = true;
	
	var xhr = new XMLHttpRequest();
	
	var payload = [{
		date : document.getElementById('spnDate').getAttribute('data-date'),
		kilograms : document.getElementById('inpDayValue').value,
		note : null
	}];
	
	xhr.addEventListener('load', function( evt ){
		var response = JSON.parse( evt.target.response );
		
		if( response.success ){
			setSuccessMessage("Saved");
			bUnsavedData = false;
		} else if( response.errors ){
			setErrorMessage( response.errors );
		}
		document.getElementById('btnSave').disabled = false;
	});
	
	clearErrors();
	
	xhr.open('POST', 'php/ajax/table.php');
	xhr.setRequestHeader('Content-type', 'application/json; charset=utf-8');
	xhr.send( JSON.stringify(payload) );
});

window.addEventListener('beforeunload', function(e){
	if(bUnsavedData){
		(e || window.event).returnValue = "There is unsaved data on the page";
	}
});

