EasySocial.require()
.script(
	'admin/api/tabs',
	'admin/vendors/uniform',
	'admin/grid/grid',
	'shared/responsive',
	'shared/elements',
	'shared/popdown',
	'shared/privacy'
)
.library(
	'dialog'
).done(function($){

	// Once uniform.js is implemented, we want to apply uniform to the elements.
	$(".uniform, input:file[data-uniform], .usergroups :checkbox").uniform();

	$('[data-sidebar-menu-toggle]').on('click' , function() {
		var parent = $(this).parent('li');
		var child = parent.find('ul');
		var isActive = $(this).parent('li').hasClass('active');

		if (isActive) {
			parent.removeClass( 'active' );
			child.removeClass( 'in' );
		} else {
			parent.addClass( 'active' );
			child.addClass( 'in' );
		}
	});

	EasySocial.compareVersion = function(version1, version2) {
		var nRes = 0;
		var parts1 = version1.split('.');
		var parts2 = version2.split('.');
		var nLen = Math.max(parts1.length, parts2.length);

		for (var i = 0; i < nLen; i++) {
			var nP1 = (i < parts1.length) ? parseInt(parts1[i], 10) : 0;
			var nP2 = (i < parts2.length) ? parseInt(parts2[i], 10) : 0;

			if (isNaN(nP1)) { 
				nP1 = 0; 
			}
			
			if (isNaN(nP2)) { 
				nP2 = 0; 
			}

			if (nP1 != nP2) {
				nRes = (nP1 > nP2) ? 1 : -1;
				break;
			}
		}

		return nRes;
	}

	// Implement grid
	var table = $('[data-table-grid]');

	if (table.length > 0) {
		table.implement(EasySocial.Controller.Grid);
	}
});