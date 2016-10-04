// JavaScript Document
function obj(){
	td = navigator.appName;
	if(td == "Microsoft Internet Explorer"){
		dd = new ActiveXObject("Microsoft.XMLHTTP");	
	}else{
		dd = new XMLHttpRequest();	
	}
	return dd;
}
http = obj();

function loadXMLDoc(){
	document.getElementById("result").innerHTML = "Processing...";
	url = "result.php";
	http.open("get",url,true);
	http.onreadystatechange=process;
	http.send(null);	
}

function process(){
	if(http.readyState == 4 && http.status == 200){
		document.getElementById("result").innerHTML = http.responseText;	
	}
}