
/* any success message is green and just fades out */
function setSuccessMessage( msg ){
	var msgSuccess = document.getElementById('msgSuccess');
	msgSuccess.classList.remove('fade-text');
	msgSuccess.innerHTML = msg;
	msgSuccess.classList.add('fade-text');
	msgSuccess.style.animation = 'none';
	msgSuccess.offsetHeight; //trigger reflow by querying a property which restarts the animation
	msgSuccess.style.animation = null;
}

/* parameter here is an array */
function setErrorMessage( errors, errorPanel ){
	//'msgError'
	var msgError = document.getElementById(errorPanel);
	msgError.innerHTML = '';
	
	for(var err in errors){
		msgError.appendChild( document.createTextNode( errors[err] ) );
		msgError.appendChild( document.createElement('br') );
	}
}

function clearErrors(){	
	document.getElementById('msgError').innerHTML = "";
}
