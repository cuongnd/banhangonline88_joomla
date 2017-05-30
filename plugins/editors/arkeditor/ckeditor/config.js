/**
 * @license Copyright (c) 2003-2016, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
};
CKEDITOR.dtd.$removeEmpty['i'] = false; //AW This needs to be done properly so this option can be mofified in Joomla as a paramater
CKEDITOR.dtd.$removeEmpty['b'] = false; //AW This needs to be done properly so this option can be mofified in Joomla as a paramater	
CKEDITOR.dtd.$removeEmpty['span'] = false; //AW This needs to be done properly so this option can be mofified in Joomla as a paramater	
