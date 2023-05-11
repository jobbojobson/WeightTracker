var bUnsavedData = false;

//takes html input elements of type="date"
async function getData( from, to ){
	
	if(!from.value || !to.value){
		return;
	}
	
	document.getElementById('btnSave').disabled = true;
	document.getElementById('tblData').classList.add('ajax-loading');
	
	let r = await fetch('php/ajax/data.php?fromDate=' + encodeURIComponent(from.value) + '&toDate=' + encodeURIComponent(to.value));
	let d = await r.json();
	
	document.getElementById('btnSave').disabled = false;
	document.getElementById('tblData').classList.remove('ajax-loading');
	
	if(d.success){
		buildTable(d.data);
	} else if(d.errors) {
		setErrorMessage(d.errors);
	}
}

/*
	Save button event handler
*/
document.getElementById('btnSave').addEventListener('click', async e => {
	if ( !bUnsavedData ) return;
	
	clearErrors();
	
	let payload = [];
	
	document.querySelectorAll('.table-warning').forEach( el => {
		payload.push({
			date : el.querySelector('td:nth-Child(1)').getAttribute('data-date'),
			kilograms : el.querySelector('td:nth-Child(2) > input').value,
			note : el.querySelector('td:nth-Child(6) > input').value
		});
	});
	
	let r = await fetch('php/ajax/data.php', { 
		method:'POST', 
		body:JSON.stringify(payload),
		headers: { 'Content-type':'application/json; charset=utf-8' }
	});
	
	let d = await r.json();
	
	if(d.success){
		setSuccessMessage( "Saved" );
		bUnsavedData = false;
		getData( document.getElementById('inpFromDate'), document.getElementById('inpToDate') );
	}else if(d.errors){
		setErrorMessage( response.errors );
	}
});



/*
	The table will have a row for every date between fromDate and toDate, regardless of whether "data" has an object for that row.
*/
function buildTable( data ){
	
	var tbody = document.querySelector('#tblData tbody');
	tbody.innerHTML = "";
	
	let fromDate = document.getElementById('inpFromDate').valueAsDate;
	let toDate = document.getElementById('inpToDate').valueAsDate;
	
	const ONE_DAY = (86400 * 1000); //in milliseconds
	
	var getImageButton = function( image_exists ){
		if(!image_exists || image_exists === null){
			return '';
		} else if( image_exists === '0' ){
			return `<i class="bi bi-plus"></i>`;
		} else if( image_exists === '1' ) {
			return `<i class="bi bi-image"></i>`;
		}
	}
	
	var getImageHandler = function( date, image_exists ){
		if(!image_exists || image_exists === null){
			return '';
		} else if(image_exists === '0'){
			return `uploadImage('${date}')`;
		} else {
			return `viewImage('${date}')`;
		}
	}
	
	for( var date = fromDate; date.getTime() <= toDate.getTime(); date.setTime(date.getTime() + ONE_DAY) ){
		
		let day = date.toISOString().substring(0, 10);
		
		let row = data.find( r => {
			return r.date === day;
		});
		
		let tr = "";
		
		tr += `<tr>
					<td class="date-col${((date.getDay() == 6 || date.getDay() == 0) ? ' bg-secondary" ' : '" ')}
						data-date="${day}">${date.toLocaleDateString()}</td>`
		if( ! row ){
			tr += `
					<td class="num-col-small"><input class="form-control" type="number" step=".1" value=""/></td>
					<td class="num-col-small"></td>
					<td class="num-col-small"></td>
					<td class="num-col-small"></td>
					<td class="text-col-wide"><input class="form-control" type="text" value=""/></td>
					<td class="button-col-small">${getImageButton(null)}</td>`
		} else {
			tr += `
					<td class="num-col-small"><input class="form-control" type="number" step=".1" value="${Number(row.kilograms).toFixed(1)}"/></td>
					<td class="num-col-small">${Number(row.last_week_average).toFixed(2)}</td>
					<td class="num-col-small">${Number(row.pounds).toFixed(2)}</td>
					<td class="num-col-small">${row.stone}</td>
					<td class="text-col-wide"><input class="form-control" type="text" value="${row.note == null ? '' : row.note}"/></td>
					<td class="button-col-small" onclick="${getImageHandler(day, row.image_exists)}">${getImageButton(row.image_exists)}</td>`
		}
		
		tr += `</tr>`
		
		tbody.innerHTML += tr;
	}
	
	document.querySelectorAll('#tblData input').forEach( el => {
		el.addEventListener('change', e => {
			e.target.parentElement.parentElement.classList.add('table-warning');
			bUnsavedData = true;
		});
	});
	
	
	let scrl = document.getElementById('scrAllTable');
	scrl.scrollTop = scrl.scrollHeight;
}

/*
	Setup the content of the image viewing dialog
*/
function viewImage(date){
	document.querySelector('#imageViewPanel img').setAttribute('src', 'php/ajax/image.php?date=' + encodeURIComponent(date));
	document.querySelector('#imageViewPanel h3').innerHTML = (new Date(date)).toLocaleDateString();
	document.querySelector('#imageViewPanel h3').setAttribute('data-date', date);
	
	new bootstrap.Modal(document.getElementById('imageViewPanel')).show();
}

/*
	Setup the functionality of the delete button on the image viewing dialog
*/
document.getElementById('btnImageViewDelete').addEventListener('click', async evt => {
	evt.preventDefault();
	
	let date = document.querySelector('#imageViewPanel h3').getAttribute('data-date');
	let fd = new FormData();
	fd.append('date', date);
	fd.append('delete', 'true');
	
	let r = await fetch('php/ajax/image.php', {
		method:'POST',
		body:fd
	});
	
	document.getElementById('btnImageViewClose').click();
	getData( document.getElementById('inpFromDate'), document.getElementById('inpToDate') );
});

/*
	Setup the contents of the upload dialog and then open it
*/
function uploadImage(date){
	document.querySelector('#imageUploadPanel h3').innerHTML = 'Image Upload - ' + (new Date(date)).toLocaleDateString();
	document.querySelector('#imageUploadPanel input').value = null;
	document.querySelector('#imageUploadPanel h3').setAttribute('data-date', date);
	document.getElementById('msgImageUploadError').innerHTML = '';
	
	new bootstrap.Modal(document.getElementById('imageUploadPanel')).show();
}

/*
	setup the functionality of the save button on the image upload dialog
*/
document.getElementById('btnImageUploadSave').addEventListener('click', async evt => {
	evt.preventDefault();
	
	let date = document.querySelector('#imageUploadPanel h3').getAttribute('data-date');
	
	let msgErrors = document.getElementById('msgImageUploadError');
	msgErrors.innerHTML = '';
	
	let fd = new FormData( document.getElementById( 'frmImageUpload' ) );
	fd.append('date', date);
	
	let r = await fetch('php/ajax/image.php', {
		method:'POST',
		body:fd
	});
	
	let d = await r.json();
	
	if( d.success ){
		document.getElementById('btnImageUploadClose').click();
		getData( document.getElementById('inpFromDate'), document.getElementById('inpToDate') );
	} else {
		var e = '';
		for(var err in d.errors){
			e += d.errors[err] + '</br>';
		}
		msgErrors.innerHTML = e;
	}
});


/*
	Fetch button event handler
*/
document.getElementById('btnFetch').addEventListener('click', evt => {
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
	Export button handler
*/
document.getElementById('btnExport').addEventListener('click', function(){
	
	window.location = 
		'php/ajax/data.php' +
		'?fromDate=' + encodeURIComponent(document.getElementById('inpFromDate').value) + 
		'&toDate=' + encodeURIComponent(document.getElementById('inpToDate').value) + 
		'&export';
});

/*
	Window handler for unsaved data
*/
window.addEventListener('beforeunload', e => {
	if(bUnsavedData){
		(e || window.event).returnValue = "There is unsaved data on the page";
	}
});


/*
	Add functionality for "scroll to top" and "scroll to bottom" TODO: might be useless?
*/
(async () => {
	var el = document.getElementById('scrollControls');
	var tbl = document.getElementById('scrAllTable');
	
	el.firstElementChild.addEventListener('click', () => {
		tbl.scrollTop = 0;
	});
	
	el.firstElementChild.nextElementSibling.addEventListener('click', () => {
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
