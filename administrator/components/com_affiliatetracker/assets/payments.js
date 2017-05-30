
window.addEvent('domready', function(){
	
	$('button_search_invoice').addEvent("click", function(event) {
		search_invoices();
	});
	
	$('search_invoice').addEvent('keypress', function(event){
		//console.log(event);
		if(event.keyCode == 13) search_invoices();
	});
			 
});

function search_invoices(){
	
	var url = "index.php?option=com_affiliatetracker&controller=payment&task=search_invoice&searchword=" + $('search_invoice').value ;
	
	if(MooTools.version >= '1.2'){
		
		var x = new Request({
						url: url, 
						method: 'get', 
						onSuccess: function(responseText){
							$('log_invoices').innerHTML = responseText;
						}
					}).send();
		
		
	 }else{
	
		new Ajax(url, {
			method: 'get',
			update: 'log_invoices'
		}).request();
			
	 }
}

function obtain_invoice(id){
	
	var url = "index.php?option=com_affiliatetracker&controller=payment&task=obtain_invoice&id=" + id ;
	
	if(MooTools.version >= '1.2'){
		
		var x = new Request({
						url: url, 
						method: 'get', 
						onSuccess: function(responseText){
							load_invoice(responseText);
						}
					}).send();
		
		
	 }else{
	
		new Ajax(url, {
			method: 'get',
			onComplete: load_invoice
		}).request();
			
	 }
}

function load_invoice(resultat){

	var resultat = JSON.parse(resultat);
	
	for(var i = 0; i < resultat.length; i++){
		
		$(resultat[i].key).value = resultat[i].value ;
		
	}
	
	
}