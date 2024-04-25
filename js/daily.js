
let bUnsavedData = false;

(async () => {
	var lbl = document.getElementById('spnDate');
	lbl.innerHTML = new Date(lbl.getAttribute('data-date')).toLocaleDateString();
	
	document.getElementById('btnSave').disabled = true;
	
	let r = await fetch('php/ajax/data.php?fromDate=' + encodeURIComponent(lbl.getAttribute('data-date')) + '&toDate=' + encodeURIComponent(lbl.getAttribute('data-date')));
	let d = await r.json();
	
	if(d.data && d.data.length > 0){
		document.getElementById('inpDayValue').setAttribute('value', Number(d.data[0].kilograms).toFixed(1));
		document.getElementById('inpDayNote').setAttribute('value', d.data[0].note ? d.data[0].note : '');
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
		setErrorMessage( d.errors, 'msgError' );
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

function setSummaryHandlers(){
	
	var rotateUnits = function(){
		document.querySelectorAll('.infoPebble.multipleUnits div').forEach( el => {
			
			//get the index of the current pebbleDataSelected
			var pSelected = el.querySelector('.pebbleDataSelected');
			var i = Array.from(el.children).indexOf(pSelected) + 1;
			
			el.querySelectorAll('p').forEach( p => { p.classList.remove('pebbleDataSelected'); });
			switch(i){ 
				case 1: //KG
					el.querySelector('p:nth-child(2)').classList.add('pebbleDataSelected');
					break;
				case 2: //LBS
					el.querySelector('p:nth-child(3)').classList.add('pebbleDataSelected');
					break;
				case 3: //St
					el.querySelector('p:nth-child(1)').classList.add('pebbleDataSelected');
					break;
			}
			
		});
	}
	
	document.querySelectorAll('.infoPebble.multipleUnits').forEach( el => {
		el.addEventListener('click', rotateUnits);
	});
}

/*
Pull the summary data
*/
async function getSummary() {
	let r = await fetch('php/ajax/summary.php');
	let html = await r.text();
	let doc = new DOMParser().parseFromString(html, "text/html");
	let el = document.getElementById('summary');
	el.innerHTML = "";
	el.appendChild(doc.documentElement);
	getSummaryTrend();
	setSummaryHandlers();
}

async function getSummaryTrend(){
	
	var d = new Date(document.getElementById('spnDate').getAttribute('data-date'));
	var y = new Date(d.getTime() - (86400 * 1000));
	
	let r = await fetch('php/ajax/data.php?fromDate=' + encodeURIComponent(y.toISOString().substr(0, 10)) + '&toDate=' + encodeURIComponent(d.toISOString().substr(0, 10)));
	let j = await r.json();
	
	if(j.data.length === 2) {
		if(j.data[0].last_week_average <= j.data[1].last_week_average){
			document.getElementById("trend_flag").setAttribute('class', 'bi bi-graph-up-arrow');
		} else {
			document.getElementById("trend_flag").setAttribute('class', 'bi bi-graph-down-arrow');
		}
	}
}

getSummary();

