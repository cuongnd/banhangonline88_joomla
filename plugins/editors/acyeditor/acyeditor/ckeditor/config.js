/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here.
	// For the complete reference:
	// http://docs.ckeditor.com/#!/api/CKEDITOR.config

	// The toolbar groups arrangement, optimized for two toolbar rows.
	config.toolbarGroups = [
				{ name: 'tools' },
				{ name: 'mode' },
				{ name: 'undo' },
				{ name: 'links' },
				{ name: 'insert', items: [ 'acymediabrowser', 'addtag' ] },,
				{ name: 'basicstyles',   groups: [ 'basicstyles', 'cleanup' ] },
				{ name: 'colors' },
				{ name: 'paragraph',   groups: [ 'list', 'indent', 'blocks' ] },
				{ name: 'align' },
				{ name: 'styles' }
	];

	config.removeButtons = 'Cut,Copy,Paste,SpecialChar';
	config.removePlugins = 'contextmenu,liststyle,tabletools,image,forms';
	config.startupFocus = false;
	config.fillEmptyBlocks = false;
	config.enterMode = CKEDITOR.ENTER_BR;
	config.filebrowserBrowseUrl = '';
	config.filebrowserImageBrowseUrl = '';
	config.filebrowserFlashBrowseUrl = '';
	config.filebrowserUploadUrl = '';
	config.filebrowserImageUploadUrl = '';
	config.filebrowserFlashUploadUrl = '';

};
