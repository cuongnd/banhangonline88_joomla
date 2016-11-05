// Add more functionality to method
var oldUpdateChildCategories = SB.Content.prototype.updateChildCategories;
SB.Content.prototype.updateChildCategories = function( category ) {
    oldUpdateChildCategories.apply(this, arguments);
    
    var level_reg = /level-(\d+)/;
	var level = level_reg.exec( $(category).get('class') );
	
	if ( (level != null) && (level[1] == 1) && $(category).get('disabled') ) {
		$(category).set('checked', '');
	}
}
