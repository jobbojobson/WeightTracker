
var bDirty = false;


document.getElementById('btnSave').addEventListener('click', function( evt ){
	evt.preventDefault();
	
	clearErrors();
	
	var xhr = new XMLHttpRequest();
	
	xhr.addEventListener('load', function( evt ){
		var response = JSON.parse( evt.target.response );
		if( response.success ){
			setSuccessMessage( 'Saved' );
		} else {
			setErrorMessage( response.errors );
		}
		
		bDirty = false;
	});
	
	xhr.open('POST', 'php/ajax/image.php');
	xhr.send( new FormData( document.getElementById( 'frmImage' ) ) );
	
});



/*
	Check for unsaved data
*/
window.onload = function(){
	window.addEventListener('beforeunload', function(evt){
		if(bDirty){
			var msg = "Form is edited. Leave without saving?";
			(evt || window.event).returnValue = msg;
			return msg;
		}
	});
};

/*
	Add all change handlers
*/
(function(){
	var formControls = document.querySelectorAll('input, select');
	
	formControls.forEach(function( i ){
		i.addEventListener('change', function(){ bDirty = true; });
	});
})();



(function(){
	
	
})();