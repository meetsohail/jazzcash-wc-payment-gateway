// JavaScript Document
function show_MWALLET(){
	document.getElementById('MWALLET').style.display ='block';
	document.getElementById('OTC').style.display ='none';
	document.getElementById('MIGS').style.display ='none';
}
function show_OTC(){
	document.getElementById('MWALLET').style.display ='none';
	document.getElementById('OTC').style.display ='block';
	document.getElementById('MIGS').style.display ='none';
}
function show_MIGS(){
	document.getElementById('MWALLET').style.display ='none';
	document.getElementById('OTC').style.display ='none';
	document.getElementById('MIGS').style.display ='block';
}