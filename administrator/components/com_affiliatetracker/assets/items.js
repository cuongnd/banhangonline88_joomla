// JavaScript Document

window.addEvent('domready', function(){
	
	$('button_search_account').addEvent("click", function(event) {
		search_account();
	});
	
	$('search_account').addEvent('keypress', function(event){
		//console.log(event);
		if(event.keyCode == 13) search_account();
	});
	
	$('button_search_user').addEvent("click", function(event) {
		search_user();
	});
	
	$('search_user').addEvent('keypress', function(event){
		//console.log(event);
		if(event.keyCode == 13) search_user();
	});
			 
});

function search_account(){
	
	var url = "index.php?option=com_affiliatetracker&controller=conversion&task=search_account&searchword=" + $('search_account').value ;
	
	if(MooTools.version >= '1.2'){
		
		var x = new Request({
						url: url, 
						method: 'get', 
						onSuccess: function(responseText){
							$('log_accounts').innerHTML = responseText;
						}
					}).send();
		
		
	 }else{
	
		new Ajax(url, {
			method: 'get',
			update: 'log_accounts'
		}).request();
			
	 }
}

function obtain_account(id){
	
	var url = "index.php?option=com_affiliatetracker&controller=conversion&task=obtain_account&id=" + id ;
	
	if(MooTools.version >= '1.2'){
		
		var x = new Request({
						url: url, 
						method: 'get', 
						onSuccess: function(responseText){
							load_account(responseText);
						}
					}).send();
		
		
	 }else{
	
		new Ajax(url, {
			method: 'get',
			onComplete: load_account
		}).request();
			
	 }
}

function load_account(resultat){
	
	var resultat = JSON.parse(resultat);
	
	for(var i = 0; i < resultat.length; i++){
		
		$(resultat[i].key).value = resultat[i].value ;
		
	}
	
	$('log_accounts').empty();
	
}

function search_user(){
	
	var url = "index.php?option=com_affiliatetracker&controller=conversion&task=search_user&searchword=" + $('search_user').value ;
	
	if(MooTools.version >= '1.2'){
		
		var x = new Request({
						url: url, 
						method: 'get', 
						onSuccess: function(responseText){
							$('log_users').innerHTML = responseText;
						}
					}).send();
		
		
	 }else{
	
		new Ajax(url, {
			method: 'get',
			update: 'log_users'
		}).request();
			
	 }
}

function obtain_user(id){
	
	var url = "index.php?option=com_affiliatetracker&controller=conversion&task=obtain_user&id=" + id ;
	
	if(MooTools.version >= '1.2'){
		
		var x = new Request({
						url: url, 
						method: 'get', 
						onSuccess: function(responseText){
							load_user(responseText);
						}
					}).send();
		
		
	 }else{
	
		new Ajax(url, {
			method: 'get',
			onComplete: load_user
		}).request();
			
	 }
}

function load_user(resultat){
	
	var resultat = JSON.parse(resultat);
	
	for(var i = 0; i < resultat.length; i++){
		
		$(resultat[i].key).value = resultat[i].value ;
		
	}
	
	$('log_users').empty();
	
}