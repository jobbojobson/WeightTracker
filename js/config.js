//
var bUnsavedData = false;

/*
	Save button click handler
*/
document.getElementById('btnSave').addEventListener('click', async evt => {
	evt.preventDefault();
	
	let r = await fetch('php/ajax/config.php', {
		method:'POST',
		body:new FormData( document.getElementById( 'frmConfig' ) )
	});
	
	let d = await r.json();
	
	if( d.success ){
		setSuccessMessage( 'Saved' );
		bUnsavedData = false;
	} else {
		setErrorMessage( d.errors );
	}
});

/*
	Check for unsaved data
*/
window.onload = function(){
	window.addEventListener('beforeunload', evt => {
		if(bUnsavedData){
			var msg = "Form is edited. Leave without saving?";
			(evt || window.event).returnValue = msg;
			return msg;
		}
	});
};

/*
	Add all change handlers
*/
(async () => {
	document.querySelectorAll('input, select').forEach( i => {
		i.addEventListener('change', () => { bDirty = true; });
	});
})();

/*
	Load the current data
*/
(async () => {
	let r = await fetch('php/ajax/config.php');
	let d = await r.json();
	
	document.getElementById('selGender').value = d.gender;
	document.getElementById('inpDob').value = d.dob;
	document.getElementById('inpHeight').value = d.height;
	document.getElementById('selAct').value = d.activity_multiplier;
	document.getElementById('inpDeficit').value = d.deficit;
	document.getElementById('inpGoal').value = d.goal_kg;
})();