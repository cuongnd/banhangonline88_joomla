function jLoadVersion(id,editorName)
{
	var editor = CKEDITOR.instances[editorName];
	
	editor.focusManager.focus(); // focus the editor
	
	var editable = editor.editable();
	
	var type = editable.getCustomData('type'),
	context = editable.getCustomData('context');
	itemtype = editable.getCustomData('itemType');
	
	var url = 'index.php?option=com_ajax&plugin=inlinecontent&format=json&mode=version&context='+context+'&id='+id+'&itemtype='+itemtype;
	
	var response = CKEDITOR.ajax.load(url);	
		response = JSON.parse(response);
	
	var content = response.data[0].data;
	
	var data = {
		'versionId': id,
		'type': type,
		'context': context,
		'itemType': itemtype,
		'content': content
	}
	
	editor.fire('loadContentVersion',data, editor); // Fire loadContentVersion so that we can manipulate version content if need be before we load the content

}

function jSaveAllInstances()
{
		
	var editors = CKEDITOR.instances;
	
	CKEDITOR.showSaveAlertReloadMessage = true;
		
	if(CKEDITOR.currentInstance)
	{
		var instanceName = CKEDITOR.currentInstance.name;
		CKEDITOR.currentInstance.fire('blur');
		editors[instanceName].focusManager.blur();
	}
	
	for(var name in editors)
	{
		var editor = editors[name];
		if(editors[name]._snapshot && editors[name].editable().getCustomData('hasFirstFocus'))
		{
			var editable = editor.editable();
			editor.fire('focus');
		
			var itemid = editable.getCustomData('itemId'),
			type = editable.getCustomData('type'),
			context = editable.getCustomData('context'),
			itemtype = editable.getCustomData('itemType');
			
					
			var content = editor.getData();
			
			var data = {
				'itemId': itemid,
				'type': type,
				'context': context,
				'itemType': itemtype,
				'content': content
			}
			//Fire saveContent event so that we can manipulate editor's content if need be before we save article content in Joomla 
			editor.fire('saveContent',data, editor);
			editor.fire('blur');
			var element = document.querySelector("div.ark.inline.alert-success");
			element.style.display = 'block';
			window.scrollTo(0,0);
		}	
	}

}

function jDisableOrEnableAllInstances(disableLinks)
{
	var editors = CKEDITOR.instances;
	
	if(CKEDITOR.enableManualInline) 
	{
		
		for(var name in editors)
		{
			var editor = editors[name];
			editor.editable().removeStyle('position'); //Add code to remove position style
			editor.destroy(true);
		}	
		CKEDITOR.enableManualInline = false;
		CKEDITOR.removeNativeLinkClickListeners();
		CKEDITOR.toggleEditableContent();
	}
	else
	{
		CKEDITOR.enableManualInline = true;
		CKEDITOR.toggleEditableContent();
		if(disableLinks)
			CKEDITOR.disableAllNativeLinkClickListener();
	}
}



function beforeUnload(evt)
{
	var editors = CKEDITOR.instances;
	var loadPopUp = false;

	for(var name in editors)
	{
		var editor = editors[name];
		if(CKEDITOR.currentInstance)
		{
			if(editor.name == CKEDITOR.currentInstance.name)
			{
				editor.fire('blur');
			}	
		}	
		
		if(editor._snapshot && editor._.previousValue != editor._snapshot)
		{
			loadPopUp = true;
		}
	}
	
	if(loadPopUp)
	{
		var message = 'Some items have not been saved. Your work will be lost. Are you sure you want to navigate away?';
		evt.returnValue = message;
		return message;
	}
}

function beforePageHide(evt)
{
	var editors = CKEDITOR.instances;
	var loadPopUp = false;

		
	for(var name in editors)
	{
		var editor = editors[name];
		if(CKEDITOR.currentInstance)
		{
			if(editor.name == CKEDITOR.currentInstance.name)
			{
				editor.fire('blur');
			}	
		}	
		if(editor._snapshot && editor._.previousValue != editor._snapshot)
		{
			loadPopUp = true;
		}
	}
	if(loadPopUp)
	{
		if(confirm('Some items have not been saved. Your work will be lost. Would you like to save now?'))
		{
			jSaveAllInstances();
		}	
	}
}



function pageHide(evt)
{
	if(evt.persisted)
		beforePageHide.apply(arguments);
}


function ARKEditorUpdateSelectedImageOrLink(instanceName,text)
{
	var editor = CKEDITOR.instances[instanceName],
		element;
	
	if(!editor.hasBookMarks)
	{	
		editor.hasBookMarks = function() { return this._bookmarks};
	}
	
	if(!editor.resetBookMarks)
	{	
		editor.resetBookMarks = function() { this._bookmarks = null;};
	}
	
	if(CKEDITOR.env.ie)
	{
		var bookmarks = null;
		
		if( (bookmarks = editor.hasBookMarks()))
		{
			var sel = editor.getSelection();
			sel && sel.selectBookmarks( bookmarks );
			editor.resetBookMarks();
		}
	
	}
	
	if(text.match(/^<a[^>]+?href/i))
	{
		if ( ( element = CKEDITOR.plugins.link.getSelectedLink( editor ) ) && element.hasAttribute( 'href' ) )
		{
                var newElement =  CKEDITOR.dom.element.createFromHtml(text);
				var href = newElement.getAttribute('href').replace(/^(?!\/|[a-zA-Z0-9\-]+:|#|')(.*)$/i,editor.config.base+'$1');
				newElement.setAttribute('href',href); 
				newElement.data('cke-saved-href',href);
        	    newElement.copyAttributes(element);
			
                element.setHtml(newElement.getHtml());    
				editor.getSelection().selectElement(element); //content changes so reselect element
			    return true;
		}	
	}
	else if (text.match(/^<img/i))
	{
		var selection = editor.getSelection();
		if ( ( element = selection && selection.getSelectedElement()) && element.is( 'img' ) )
		{
				var newElement = CKEDITOR.dom.element.createFromHtml(text);
				var src = newElement.getAttribute('src').replace(/^(?!\/|[a-zA-Z0-9\-]+:|#|')(.*)$/i,editor.config.base+'$1');
				newElement.setAttribute('src',src); 
				newElement.data('cke-saved-src',src);
				newElement.copyAttributes(element);
				selection.selectElement(element); //content changes so reselect element
				return true;
		}	
		
	}
	
	return false;
}

function jInsertEditorText( text,editor) 
{
	if(!ARKEditorUpdateSelectedImageOrLink(editor,text))
		CKEDITOR.instances[editor].insertHtml( text );
}

function jModalClose() 
{
	SqueezeBox.close();
}