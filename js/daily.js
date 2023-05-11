
let bUnsavedData = false;

(async () => {
	var lbl = document.getElementById('spnDate');
	lbl.innerHTML = new Date(lbl.getAttribute('data-date')).toLocaleDateString();
	
	document.getElementById('btnSave').disabled = true;
	
	let r = await fetch('php/ajax/data.php?fromDate=' + encodeURIComponent(lbl.getAttribute('data-date')) + '&toDate=' + encodeURIComponent(lbl.getAttribute('data-date')));
	let d = await r.json();
	
	if(d.data && d.data.length > 0){
		let doc = new DOMParser().parseFromString(d.data[0].note, "text/html");
		let note = doc.documentElement.textContent;
		
		document.getElementById('inpDayValue').value = Number(d.data[0].kilograms).toFixed(1);
		document.getElementById('inpDayNote').value = note;
	}
	
	document.getElementById('btnSave').disabled = false;
})();

document.querySelectorAll('input').forEach( el => {
	el.addEventListener('change', evt => {
		bUnsavedData = true;
	});	
});

document.getElementById('btnSave').addEventListener('click', async evt => {
	if( !bUnsavedData ) return;
	
	document.getElementById('btnSave').disabled = true;
	clearErrors();	
	
	let r = await fetch('php/ajax/data.php', {
		method:'POST',
		body:JSON.stringify([{
			date : document.getElementById('spnDate').getAttribute('data-date'),
			kilograms : document.getElementById('inpDayValue').value,
			note : (document.getElementById('inpDayNote').value.length ? document.getElementById('inpDayNote').value : null)
		}])
	});
	
	let d = await r.json();
	
	if(d.success){
		setSuccessMessage("Saved");
		bUnsavedData = false;
		getSummary();
	} else if (d.errors) {
		setErrorMessage( d.errors );
	}
	
	document.getElementById('btnSave').disabled = false;
});


document.getElementById('inpDayValue').addEventListener('keyup', evt => {
	if(evt.keyCode === 13){ //enter
		let e = document.getElementById('btnSave');
		if(typeof e.click == 'function')
			e.click();
	}
});

window.addEventListener('beforeunload', e => {
	if(bUnsavedData){
		(e || window.event).returnValue = "There is unsaved data on the page";
	}
});

/*
Pull the summary data
*/
async function getSummary() {
	let r = await fetch('php/ajax/summary.php');
	let html = await r.text();
	let doc = new DOMParser().parseFromString(html, "text/html");
	document.getElementById('summary').appendChild(doc.documentElement);
}

getSummary();

