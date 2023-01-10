//
var bDirty = false;

document.getElementById('btnSave').addEventListener('click', function( evt ){
	evt.preventDefault();
	var xhr = new XMLHttpRequest();
	
	xhr.addEventListener('load', function( evt ){
		var response = JSON.parse( evt.target.response );
		if( response.success ){
			setSuccessMessage( 'Saved' );
			bDirty = false;
		} else {
			setErrorMessage( response.errors );
		}
	});
	
	xhr.open('POST', 'php/ajax/config.php');
	xhr.send( new FormData( document.getElementById( 'frmConfig' ) ) );
	
});

window.onload = function(){
	window.addEventListener('beforeunload', function(evt){
		if(bDirty){
			var msg = "Form is edited. Leave without saving?";
			(evt || window.event).returnValue = msg;
			return msg;
		}
	});
};

(function(){
	var formControls = document.querySelectorAll('input, select');
	
	formControls.forEach(function( i ){
		i.addEventListener('change', function(){ bDirty = true; });
	});
})();


(function(){
	
	var xhr = new XMLHttpRequest();
	
	xhr.addEventListener('load', function( evt ){
		var config = JSON.parse( evt.target.response );
		
		document.getElementById('selGender').value = config.gender;
		document.getElementById('inpDob').value = config.dob;
		document.getElementById('inpHeight').value = config.height;
		document.getElementById('selAct').value = config.activity_multiplier;
		document.getElementById('inpDeficit').value = config.deficit;
		document.getElementById('inpGoal').value = config.goal_kg;
	});
	
	xhr.open('GET', 'php/ajax/config.php');
	xhr.send();
})();