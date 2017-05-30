
window.addEvent('domready', function(){

	jQuery( "#button_search_user" ).click(function() {
		search_user();
	});

	jQuery( "#button_search_user_parent" ).click(function() {
		search_user_parents();
	});

	//Search if the selected word is available
	jQuery("#ref_word").bind("change paste keyup", function() {
		var searchword = jQuery(this).val();
		var aId = jQuery('[name=id]').val();

		var url = "index.php?option=com_affiliatetracker&controller=accounts&task=is_refWord_available";

		jQuery.ajax({
			url: url,
			data: {searchword: searchword, aId: aId},
			//dataType: "json",
			success: function(data)
			{
				if(data == "0") {
					jQuery("#ref_word_group").addClass("error");
					jQuery("#ref_word_error").show();
				} else {
					jQuery("#ref_word_group").removeClass("error");
					jQuery("#ref_word_error").hide();
				}
			}
		});

	});

	//If the selected word is not available, don't submit
	jQuery( "#adminForm" ).submit(function( event ) {
		if (jQuery("#ref_word_group").hasClass("error")) {
			event.preventDefault();
		}
	});

});

function search_user_parents() {
	var url = "index.php?option=com_affiliatetracker&controller=conversion&task=search_user&searchword=" + jQuery('#search_user_parent').val() + "&parents=1";

	jQuery.ajax({
		url: url,
		success: function(data)
		{
			jQuery('#log_user_parents').html(data);
		}
	});
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

function obtain_user_parents(id) {
	var url = "index.php?option=com_affiliatetracker&controller=conversion&task=obtain_parent&id=" + id ;

	jQuery.ajax({
		url: url,
		success: function(data)
		{
			load_user(data);
		}
	});
}

function load_user(resultat){
	
	var resultat = JSON.parse(resultat);
	
	for(var i = 0; i < resultat.length; i++){
		
		$(resultat[i].key).value = resultat[i].value ;
		
	}
	
	$('log_users').empty();
	
}