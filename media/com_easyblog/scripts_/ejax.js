/**
 * @package		EasyBlog
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyBlog is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

EasyBlog.module('ejax', function($) {

	var module = this;

ejax = {
	http:		false, //HTTP Object
	format: 	'text',
	callback:	function(data){},
	error:		false,
	getHTTPObject : function() {
		var http = false;

		//Use IE's ActiveX items to load the file.
		if ( typeof ActiveXObject != 'undefined' ) {
			try {
				http = new ActiveXObject("Msxml2.XMLHTTP");
			}
			catch (e) {
				try {
					http = new ActiveXObject("Microsoft.XMLHTTP");
				}
				catch (E) {
					http = false;
				}
			}
		//If ActiveX is not available, use the XMLHttpRequest of Firefox/Mozilla etc. to load the document.
		}
		else if ( XMLHttpRequest ) {
			try {http = new XMLHttpRequest();}
			catch (e) {http = false;}
		}
		return http;
	},

	/**
	 * Ajax function
	 */

	// ejax.call('controller','task', ['arg1', 'arg2'], function(){});
	// ejax.call('controller','task', ['arg1', 'arg2'], {
	//    success: function(){},
	//    error: function(){}
	// });
	call: function(view, method, params, callback)
	{
		var args = [{view: view, callback: callback}, method];
		args = args.concat(params);
		ejax.load.apply(this, args);
	},

	load : function ( view, method )
	{
		var callback = {
			success: function(){},
			error: function(){}
		};

		if (typeof view == "object")
		{
			callback = $.extend(callback, ($.isFunction(view.callback)) ? {success: view.callback} : view.callback);
			view = view.view;
		}

		// This will be the site we are trying to connect to.
		url	 = eblog_site;
		url	+= '&tmpl=component';
		url += '&no_html=1';
		url += '&format=ejax';

		//Kill the Cache problem in IE.
		url	+= "&uid=" + new Date().getTime();

		var parameters	= '&view=' + view + '&layout=' + method;

		// If there is more than 1 arguments, we want to accept it as parameters.
		if ( arguments.length > 2 )
		{
			for ( var i = 2; i < arguments.length; i++ )
			{
				var myArgument	= arguments[ i ];

				if($.isArray(myArgument))
				{
					for(var j = 0; j < myArgument.length; j++)
					{
					    var argument    = myArgument[j];
						if ( typeof( argument ) == 'string' )
						{
							// Encode value to proper html entities.
							parameters	+= '&value' + ( i - 2 ) + '[]=' + encodeURIComponent( argument );
						}
					}
				} else {
				    var argument    = myArgument;
					if ( typeof( argument ) == 'string' )
					{
						// Encode value to proper html entities.
						parameters	+= '&value' + ( i - 2 ) + '=' + encodeURIComponent( argument );
					}
				}
			}
		}

		var http = this.getHTTPObject(); //The XMLHttpRequest object is recreated at every call - to defeat Cache problem in IE

		if ( !http || !view || !method ) return;

// 		if ( this.http.overrideMimeType )
// 			this.http.overrideMimeType( 'text/xml' );

		//Closure
 		var ths = this;

		http.open( 'POST' , url , true );

		// Required because we are doing a post
		http.setRequestHeader( "Content-type", "application/x-www-form-urlencoded" );
		http.setRequestHeader( "Content-length", parameters.length );
		http.setRequestHeader( "Connection", "close" );

		http.onreadystatechange = function(){
			//Call a function when the state changes.

			if (http.readyState == 4)
			{
				//Ready State will be 4 when the document is loaded.
				if (http.status == 200)
				{
					var result = "";

					if (http.responseText)
					{
						result = http.responseText;
					}

					// Evaluate the result before processing the JSON text. New lines in JSON string,
					// when evaluated will create errors in IE.
					result	= result.replace(/[\n\r]/g,"");

					try {
						result	= eval( result );
					} catch(e) {
						if (callback.error) { callback.error('Invalid response.'); }
					}

					// Give the data to the callback function.
					ths.process( result, callback );
				}
				else
				{
					//An error occured
					if (ths.error)
					{
						ths.error( http.status );
						if (callback.error) { callback.error(http.status); }
					}
				}
			}
		}
		http.send( parameters );
	},

	/**
	 * Method to get translated string from server
	 *
	 * @param	string
	 */
	_string: [],

	string: function( str ) {

		if (ejax._string[str]!=undefined)
			return ejax._string[str];

		var url	 = eblog_site + '&tmpl=component&no_html=1&controller=easyblog&task=ajaxGetSystemString';

		var r1 = $.ajax({
		    type: "POST",
			url: url,
			data: "data=" + str,
			async: false,
			cache: true
		}).responseText;

		ejax._string[str] = r1;

		return r1;
	},

	/**
	 * Get form values
	 *
	 * @param	string	Form ID
	 */
	getFormVal : function( element ) {

	    var inputs  = [];
	    var val		= null;

		$( ':input', $( element ) ).each( function() {
			val = this.value.replace(/"/g, "&quot;");
			val = encodeURIComponent(val);

			if($(this).is(':checkbox') || $(this).is(':radio'))
		    {
				if($(this).prop('checked'))
				{
					inputs.push( this.name + '=' + escape( val ) );
				}
		    }
		    else
		    {
				inputs.push( this.name + '=' + escape( val ) );
			}
		});
		//var finalData = inputs.join('&&');
		//return finalData;
		return inputs;
	},

	process : function ( result, callback ){

		// Process response according to the key
		for(var i=0; i < result.length;i++)
		{
			var action	= result[ i ][ 0 ];

			switch( action )
			{
				case 'script':
					var data	= result[ i ][ 1 ];
					eval("EasyBlog(function($){" + data + "});");
					break;

				case 'after':
					var id		= result[ i ][ 1 ];
					var value	= result[ i ][ 2 ];


					$( '#' + id ).after( value );
					break;

				case 'append':
					var id		= result[ i ][ 1 ];
					var value	= result[ i ][ 2 ];

					$( '#' + id ).append( value );
					break;

				case 'assign':
					var id		= result[ i ][ 1 ];
					var value	= result[ i ][ 2 ];

					$( '#' + id ).html( value );
					break;

				case 'value':
					var id		= result[ i ][ 1 ];
					var value	= result[ i ][ 2 ];

					$( '#' + id ).val( value );
					break;
				case 'prepend':
					var id		= result[ i ][ 1 ];
					var value	= result[ i ][ 2 ];
					$( '#' + id ).prepend( value );
					break;
				case 'destroy':
					var id		= result[ i ][ 1 ];
					$( '#' + id ).remove();
					break;
				case 'dialog':
					ejax.dialog( result[ i ][ 1 ] );
					break;
				case 'alert':
					ejax.alert( result[ i ][ 1 ], result[ i ][ 2 ], result[ i ][ 3 ] , result[ i ][ 4 ] );
					break;
				case 'create':
					break;
				case 'error':
					var args = result[ i ].slice(1);
					callback.error.apply(this,args);
					break;
				case 'callback':
					var args = result[ i ].slice(1);
					callback.success.apply(this, args);
					break;
			}
		}
		delete result;
	},

	/**
	 * Dialog
	 */
	dialog: function( options ) {
		ejax._showPopup( options );
	},

	closedlg: function() {
		var dialog = $('#eblog-dialog');
		var dialogOverlay = $('#eblog-overlay');

		var options = dialog.data('options');

		dialogOverlay.hide();

		dialog
			.fadeOut(function()
			{
				options.afterClose.apply(dialog);
			});

		$(window).unbind('.dialog');

		$(document).unbind('keyup', ejax._attachPopupShortcuts);
	},

	_attachPopupShortcuts: function(e)
	{
		if (e.keyCode == 27) { ejax.closedlg(); }
	},

	/**
	 * Alert
	 */
	alert: function( content, title, width, height ) {

		var COM_EASYBLOG_OK = ejax.string('COM_EASYBLOG_OK');

		var dialogActions = '<div class="dialog-actions"><input type="button" value="' + COM_EASYBLOG_OK + '" class="button" id="edialog-cancel" name="edialog-cancel" onclick="ejax.closedlg();" /></div>';

 		var options = {
 			title: title,
			content: content + dialogActions,
			width: width,
			height: height
		}

		ejax._showPopup( options );
	},

	/**
	 * Private function
	 *
	 * Generate dialog and popup dialog
	 */
	_showPopup: function( options ){

		var defaultOptions = {
			width: '500',
			height: 'auto',
			type: 'dialog',
			beforeDisplay: function(){},
			afterDisplay: function(){},
			afterClose: function(){}
		}

		var options = $.extend({}, defaultOptions, options);

		var dialogOverlay = $('#eblog-overlay');

		if (dialogOverlay.length < 1)
		{
			dialogOverlay = '<div id="eblog-overlay"></div>';

			dialogOverlay = $(dialogOverlay).appendTo('body');

			dialogOverlay.click(function()
			{
				ejax.closedlg();
			});
		}

		var dialog = $('#eblog-dialog');

		if (dialog.length < 1)
		{
			dialogTemplate   = '<div id="eblog-dialog">';
			dialogTemplate	+= '	<div class="dialog">';
			dialogTemplate	+= '		<div class="dialog-wrap">';
			dialogTemplate	+= '			<div class="dialog-top">';
			dialogTemplate	+= '				<h3></h3>';
			dialogTemplate	+= '				<a href="javascript:void(0);" onclick="ejax.closedlg();" class="closeme">Close</a>';
			dialogTemplate	+= '			</div>';
			dialogTemplate	+= '			<div class="dialog-middle clearfix">';
			dialogTemplate	+= '				<div class="dialog-middle-content"></div>';
			dialogTemplate	+= '			</div>';
			dialogTemplate	+= '		</div>';
			dialogTemplate	+= '	</div>';
			dialogTemplate	+= '</div>';

			dialog = $(dialogTemplate).appendTo('body');
		}

		// Store dialog options
		dialog
			.data('options', options);

		var dialogTitle = dialog.find('.dialog-top h3');

		options.title	= options.title != null ? options.title : '&nbsp;';
		dialogTitle.html(unescape(options.title));

		var dialogContent = $('#eblog-dialog .dialog-middle-content');

		dialogContent
			.css({
				width : (options.width=='auto') ? 'auto' : parseInt(options.width),
				height: (options.height=='auto') ? 'auto' : parseInt(options.height)
			})
			.html(options.content);

		options.beforeDisplay.apply(dialog);


		var positionDialog = function()
		{
			dialog
				.css({ top: 0, left: 0 })
				.position({ my: 'center', at: 'center', of: window });

			dialogOverlay
				.css({
					width: $(document).width(),
					height: $(document).height()
				})
				.show();
		};

		dialog
			.show(0, function()
			{
				positionDialog();

				var positionDelay;
				$(window)
					.bind('resize.dialog scroll.dialog', function()
					{
						clearTimeout(positionDelay);
						positionDelay = setTimeout(positionDialog, 50);
					});
			});

		dialog.fadeOut(0, function() {
			dialog.fadeIn(function() {
				options.afterDisplay.apply(dialog);
			});
		});

		$('#edialog-cancel, #edialog-submit').live('mouseup', function() {
		 	ejax.closedlg();
		});

		$(document).bind('keyup', ejax._attachPopupShortcuts);
	}
}

// module: end
	module.resolve();
});
