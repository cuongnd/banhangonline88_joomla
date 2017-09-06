function checkAvailable(el){
	var inputstr = el.value;
	var name = el.name;
	if(inputstr.length > 0){
		var myRequest = new Request({
			url: 'index.php?option=com_jblance&task=guest.checkuser&'+JoomBriToken,
            method: 'post',
			data: {'inputstr':inputstr, 'name':name},
			onRequest: function(){ $('status_'+name).empty().removeProperty('class'); $('status_'+name).addClass('jbloading dis-inl-blk'); },
			onSuccess: function(response) {
				if(response == 'OK'){
					$('status_'+name).removeClass('jbloading').addClass('successbg');
					$('status_'+name).set('html', Joomla.JText._('COM_JBLANCE_AVAILABLE'));
				} 
				else {
					$('status_'+name).removeClass('jbloading').addClass('failurebg');
					$('status_'+name).set('html', response);
				}
           }
		});
		myRequest.send();
	}
}

function createUploadButton(userid, task){
	var uploader = document.getElementById('photoupload');
	upclick({
		element: uploader,
		action: 'index.php?option=com_jblance&task='+task+'&'+JoomBriToken,
		dataname: 'photo', 
		action_params: {'userid': userid},
		onstart: 
			function(filename){ $('ajax-container').empty().removeProperty('class').addClass('jbloading'); },
		oncomplete:
		function(response){ //alert(response);
			var resp = JSON.decode(response);
			if(resp['result'] == 'OK'){
				//document.location.href = resp['return'];
				//set the picture
				target = $('divpicture');
				target.set('html', '<img src='+resp['image']+'>');
				//set the thumb
				target = $('divthumb');
				target.set('html', '<img src='+resp['thumb']+'>');
				//set the crop image
				if($('cropframe')){
					$('cropframe').setStyle('background-image', 'url('+resp['image']+')');
					$('imglayer').setStyles({
						'background-image': 'url('+resp['image']+')',
					    width: resp['width'],
					    height: resp['height']
					});
					$('imgname').set('value', resp['imgname']);
					$('tmbname').set('value', resp['tmbname']);
				}
				$('ajax-container').removeProperty('class').addClass('successbg');
				$('ajax-container').set('html', resp['msg']);
			}
			else if(resp['result'] == 'NO'){
				$('ajax-container').removeProperty('class').addClass('failurebg');
				$('ajax-container').set('html', resp['msg']);
			}
		}
	});
}

function attachFile(elementID, task){
	var uploader = document.getElementById(elementID);
	
	upclick({
		element: uploader,
		action: 'index.php?option=com_jblance&task='+task+'&'+JoomBriToken,
		dataname: elementID, 
		action_params: {'elementID': elementID},
		onstart: function(filename){ $('ajax-container-'+elementID).empty().removeProperty('class'); $('ajax-container-'+elementID).addClass('jbloading'); },
		oncomplete:
			function(response){ //alert(response);
			var resp = JSON.decode(response);
			var elementID = resp['elementID'];
			if(resp['result'] == 'OK'){
				$(elementID).setStyle('display', 'none');
				$('ajax-container-'+elementID).removeClass('jbloading').addClass('successbg');
				$('ajax-container-'+elementID).set('html', resp['msg']);
				var html = "<input type='checkbox' name='chk-"+elementID+"' checked value='1' />"+resp['attachname']+"<input type='hidden' name='attached-file-"+elementID+"' value='"+resp['attachvalue']+"'>";
				$('file-attached-'+elementID).set('html', html);
			}
			else if(resp['result'] == 'NO'){
				$('ajax-container-'+elementID).removeClass('jbloading').addClass('failurebg');
				$('ajax-container-'+elementID).set('html', resp['msg']);
			}
		}
	});
}

function removePicture(userid, task){
	var myRequest = new Request({
		url: 'index.php?option=com_jblance&task='+task+'&'+JoomBriToken,
		method: 'post',
		data: {'userid': userid },
		onRequest: function(){  $('ajax-container').empty().removeProperty('class');$('ajax-container').addClass('jbloading'); },
		onSuccess: function(responseText, responseXML){
			 var resp = JSON.decode(responseText);
		       
	          if(resp['result'] == 'OK'){
	          	  $('ajax-container').removeClass('jbloading').addClass('successbg');
	          	  $('ajax-container').set('html', resp['msg']);
	          }
	          else if(resp['result'] == 'NO'){
	          	  $('ajax-container').removeClass('jbloading').addClass('failurebg');
	          	  $('ajax-container').set('html', resp['msg']);
	          }
		}
	});
	myRequest.send();
}

function updateThumbnail(task){

	$('editthumb').setStyle('display', '');
	
	var ch = new CwCrop({
   	    minsize: {x: 64, y: 64},
   	    maxratio: {x: 2, y: 1},
   	    fixedratio: false,
   	 	onCrop: function(values){

   			var myRequest = new Request({
   			url: 'index.php?option=com_jblance&task='+task+'&'+JoomBriToken,
   			method: 'post',
   			data: {'cropW': values.w, 'cropH': values.h, 'cropX': values.x, 'cropY': values.y, 'imgLoc': $('imgname').get('value'), 'tmbLoc': $('tmbname').get('value')},
   			onRequest: function(){  $('tmb-container').empty().removeProperty('class');$('tmb-container').addClass('jbloading'); },
   			onSuccess: function(response){
   				var resp = JSON.decode(response);
   			    
				if(resp['result'] == 'OK'){
					//document.location.href = resp['return'];
   		          	$('tmb-container').removeClass('jbloading').addClass('successbg');
   		          	$('tmb-container').set('html', resp['msg']);
   		          }
   		          else if(resp['result'] == 'NO'){
   		          	  $('tmb-container').removeClass('jbloading').addClass('failurebg');
   		          	  $('tmb-container').set('html', resp['msg']);
   		          }
   			}
   		});
   		myRequest.send();
   	    }
   	});
}

function checkUsername(el){
	var inputstr = el.value;
	var name = el.name;
	if(inputstr.length > 0){
		var myRequest = new Request({
			url: 'index.php?option=com_jblance&task=membership.checkuser&'+JoomBriToken,
            method: 'post',
			data: {'inputstr':inputstr, 'name':name},
			onRequest: function(){ $('status_'+name).empty().removeProperty('class'); $('status_'+name).addClass('jbloading dis-inl-blk'); },
			onSuccess: function(response) {
				var resp = JSON.decode(response);
				if(resp['result'] == 'OK'){
					$('status_'+name).removeClass('jbloading').addClass('successbg');
					$('status_'+name).set('html', resp['msg']);
				} 
				else {
					$('status_'+name).removeClass('jbloading').addClass('failurebg');
					$('status_'+name).set('html', resp['msg']);
				}
           }
		});
		myRequest.send();
	}
}

function fillProjectInfo(){
	var project_id = $('project_id').value;
	var myRequest = new Request({
		url: 'index.php?option=com_jblance&task=membership.fillprojectinfo&'+JoomBriToken,
		method: 'post',
		data: {'project_id': project_id },
		onSuccess: function(responseText, responseXML){
			 var resp = JSON.decode(responseText);
	          if(resp['result'] == 'OK'){
	          	  $('recipient').set('value', resp['assignedto']);
	          	  $('proj_balance_div').set('html', resp['proj_balance_html']);
	          	  //if full payment is checked, set amount to bid amount. if payment is partial, set amount to balance amount
	          	  if($('full_payment_option').checked){
	          		  $('amount').set('value', resp['bidamount']);
	          	  }
	          	  else if($('partial_payment_option').checked){
	          		$('amount').set('value', resp['proj_balance']);
	          	  }
	          	$('proj_balance').set('value', resp['proj_balance']);
	          	// display pay for field only for hourly projects
	          	 if(resp['project_type'] == 'COM_JBLANCE_HOURLY'){
	          		$('div_pay_for').show();
	          		$('amount').set('value', '');
	          		$('bid_amount').set('value', resp['bidamount']);
	          		
	          		//$('amount').setProperty('readonly', true);
	          		$('pay_for').addClass('required').setProperty('required','required');
	          	 }
	          	 else if(resp['project_type'] == 'COM_JBLANCE_FIXED'){
	          		 $('div_pay_for').hide();
	          		 $('pay_for').removeClass('required').removeProperty('required');
	          	 }
	          	 
	          }
	          else if(resp['result'] == 'NO'){
	          	  //$('ajax-container').removeClass('jbloading').addClass('failurebg');
	          	 // $('ajax-container').set('html', resp['msg']);
	          }
		}
	});
	myRequest.send();
	
}

function processFeed(userid, activityid, type){
	var myRequest = new Request({
		url: 'index.php?option=com_jblance&task=user.processfeed&'+JoomBriToken,
		method: 'post',
		data: {'userid':userid, 'activityid':activityid, 'type':type}, 
		onRequest: function(){ $('feed_hide_'+activityid).empty().addClass('jbloading'); },
		onComplete: function(response){
			if(response == 'OK'){
				$('jbl_feed_item_'+activityid).hide();
			}
			else {
				alert(':(');
			}
		}
	});
	myRequest.send();
}

function processForum(forumid, task){
	var myRequest = new Request({
		url: 'index.php?option=com_jblance&task='+task+'&'+JoomBriToken,
		method: 'post',
		data: {'forumid':forumid}, 
		//onRequest: function(){ $('tr_forum_'+forumid).empty().addClass('jbloading'); },
		onComplete: function(response){
			if(response == 'OK'){
				$('tr_forum_'+forumid).dispose();
			}
			else {
				alert(':(');	
			}
		}
	});
	myRequest.send();
}

function processMessage(msgid, task){
	var myRequest = new Request({
		url: 'index.php?option=com_jblance&task='+task+'&'+JoomBriToken,
		method: 'post',
		data: {'msgid':msgid}, 
		onRequest: function(){ $('feed_hide_'+msgid).empty().addClass('jbloading'); },
		onComplete: function(response){
			if(response == 'OK'){
				$('jbl_feed_item_'+msgid).hide();
			} 
			else
				alert(':(');
		}
	});
	myRequest.send();
}

function manageMessage(msgid, type){
	var text = '';
	var removeAttach = '';
	if(type == 'message'){
		text = $('message_'+msgid).get('value');
	}
	else if (type == 'subject'){
		text = $('txt_subject_'+msgid).get('value');
	}
	
	if($('chk_attachment_'+msgid) && $('chk_attachment_'+msgid).checked){
		removeAttach = $('chk_attachment_'+msgid).get('value');
	}
		
	var myRequest = new Request({
		url: 'index.php?option=com_jblance&task=admproject.manageMessage&'+JoomBriToken,
        method: 'post',
		data: {'msgid':msgid, 'text':text , 'type': type, 'attachment' : removeAttach},
		//onRequest: function(){ $('status_'+name).empty().removeProperty('class'); $('status_'+name).addClass('jbloading dis-inl-blk'); },
		onSuccess: function(response) {//alert(response);
			var resp = JSON.decode(response);
			if(resp['result'] == 'OK'){
				//alert("Saved");
				if(type == 'message'){
					$('span_message_'+msgid).setStyle('display','inline');
					$('span_message_'+msgid).set('html', $('message_'+msgid).get('value'));
					$('message_'+msgid).setStyle('display','none');
					$('btn_save_message_'+msgid).setStyle('display','none');
					$('btn_edit_message_'+msgid).setStyle('display','inline');
					$('span_message_'+msgid).get("tween").options.duration = 1000;
					$('span_message_'+msgid).highlight('#98FB98');
					
					if(resp['attachRemoved'] == 1){
						$('div_attach_'+msgid).setStyle('display', 'none');
					}
				}
				else if(type == 'subject'){
					$('span_subject_'+msgid).set('html', $('txt_subject_'+msgid).get('value'));
					$('txt_subject_'+msgid).setStyle('display','none');
					$('btn_save_subject_'+msgid).setStyle('display','none');
				}
			} 
			else {
				alert(":(");
			}
       }
	});
	myRequest.send();
}

function approveMessage(msgid, task){
	var myRequest = new Request({
		url: 'index.php?option=com_jblance&task=admproject.approvemessage&'+JoomBriToken,
		method: 'post',
		data: {'msgid':msgid}, 
		//onRequest: function(){ $('feed_hide_'+msgid).empty().addClass('jbloading'); },
		onComplete: function(response){
			if(response == 'OK'){
				$('feed_hide_approve_'+msgid).hide();
			} 
			else {
				alert(":(");
			}
		}
	});
	myRequest.send();
}

function removeTransaction(transid){
	var myRequest = new Request({
		url: 'index.php?option=com_jblance&task=admproject.removetransaction&'+JoomBriToken,
		method: 'post',
		data: {'transid':transid}, 
		//onRequest: function(){ $('tr_forum_'+forumid).empty().addClass('jbloading'); },
		onComplete: function(response){//alert(response);
			if(response == 'OK'){
				$('tr_trans_'+transid).dispose();
			}
			else {
				alert(":(");
			}
		}
	});
	myRequest.send();
}

function processBid(bidid){
	var myRequest = new Request({
		url: 'index.php?option=com_jblance&task=admproject.processbid&'+JoomBriToken,
		method: 'post',
		data: {'bidid':bidid}, 
		//onRequest: function(){ $('feed_hide_'+msgid).empty().addClass('jbloading'); },
		onComplete: function(response){
			if(response == 'OK'){
				$('tr_r1_bid_'+bidid).hide();
				$('tr_r2_bid_'+bidid).hide();
			} 
			else
				alert(':(');
		}
	});
	myRequest.send();
}

function favourite(targetId, action, type){
	var requestURL = '';
	if(type == 'profile')
		requestURL = 'index.php?option=com_jblance&task=user.favourite&'+JoomBriToken;
	
	var myRequest = new Request({
		url: requestURL,
		method: 'post',
		data: {'targetId':targetId, 'action':action}, 
		//onRequest: function(){ $('feed_hide_'+msgid).empty().addClass('jbloading'); },
		onComplete: function(response){
			var resp = JSON.decode(response);
			if(resp['result'] == 'OK'){
				$('fav-msg-'+targetId).set('html', '<small>'+resp['msg']+'</small>');
				//$('tr_r1_bid_'+bidid).hide();
				//$('tr_r2_bid_'+bidid).hide();
			} 
			else
				alert(':(');
		}
	});
	myRequest.send();
}

function createDropzone(elementId, mockFile, controller){
	//Get the template HTML and remove it from the document template HTML and remove it from the doument
	var previewNode = document.querySelector("#template");
	previewNode.id = "";
	var previewTemplate = previewNode.parentNode.innerHTML;
	previewNode.parentNode.removeChild(previewNode);
	 
	var myDropzone = new Dropzone(elementId, { // Make the whole body a dropzone
	  url: 'index.php?option=com_jblance&task='+controller+'.serviceuploadfile&'+JoomBriToken, // Set the url
	  paramName: 'serviceFile',
	  //autoDiscover : false,
	  //myAwesomeDropzone : false,
	  thumbnailWidth: 80,
	  thumbnailHeight: 80,
	  parallelUploads: 1,
	  previewTemplate: previewTemplate,
	  autoQueue: false, // Make sure the files aren't queued until manually added
	  previewsContainer: "#previews", // Define the container to display the previews
	  clickable: ".fileinput-button", // Define the element that should be used as click trigger to select files.
	  maxFilesize: 2, // MB
	  maxFiles: 5,	//limited to 5 files only
	  acceptedFiles: 'image/*',
	  init: function(){
		// if images are uploaded successfully, add hidden fields. This function is called by emit function upon load and after file is successfully uploaded 
		 this.on('success', function(file, response){
			    if(typeof response !== "undefined" && response !== null){
					var resp = JSON.decode(response);
					if(resp['result'] == 'OK'){
						var hiddenvalue = resp['attachvalue'];
						}
			    }
			    else {
			    	var hiddenvalue = file.name + ';' + file.servername + ';' + file.size;
				}
			    var hiddenFileInput = new Element('input', {
					'type': 'hidden',
					'name': 'serviceFiles[]',
					'value' : hiddenvalue
				});
				hiddenFileInput.inject(file.previewTemplate);
			}); 
	  }
	});

	var existingFileCount = 0;
	var data = JSON.decode(mockFile);
	Object.each(data, function(value, key){
	    var mockFile = { name: value.name, servername: value.servername, size: value.size, status: Dropzone.SUCCESS };
	    myDropzone.emit("addedfile", mockFile);
	    myDropzone.emit("thumbnail", mockFile, value.thumb);
	    myDropzone.emit("success", mockFile);
	    myDropzone.files.push(mockFile);  // added this line so the files array is the correct length.
	    existingFileCount = existingFileCount + 1;
	});
	
	myDropzone.options.maxFiles = myDropzone.options.maxFiles - existingFileCount;
	
	myDropzone.on("addedfile", function(file) {
	  // Hookup the start button
		file.previewElement.querySelector(".start").onclick = function() { myDropzone.enqueueFile(file); };

		// Add default option box for each preview.
	    //var defaultRadioButton = Dropzone.createElement('<div class="default_pic_container"><input type="radio" name="default_pic" value="'+file.name+'" /> Default</div>');
	    //file.previewElement.appendChild(defaultRadioButton);
	});
	 
	// Update the total progress bar
	myDropzone.on("totaluploadprogress", function(progress) {
	  document.querySelector("#total-progress .bar").style.width = progress + "%";
	});
	 
	myDropzone.on("sending", function(file) {
	  // Show the total progress bar when upload starts
	  document.querySelector("#total-progress").style.opacity = "1";
	  // And disable the start button
	  file.previewElement.querySelector(".start").setAttribute("disabled", "disabled");
	});
	 
	// Hide the total progress bar when nothing's uploading anymore
	myDropzone.on("queuecomplete", function(progress) {
	  document.querySelector("#total-progress").style.opacity = "0";
	});

	myDropzone.on('removedfile', function(file){
		
		//if(file.xhr){
		//	var url = JSON.parse(file.xhr.response);
		//	var attachvalue = url.attachvalue;
		//}
		//if the input hidden node exist
		if(file.previewTemplate.children[4]){
			attachvalue = file.previewTemplate.children[4].value;
			
			var myRequest = new Request({
				url: 'index.php?option=com_jblance&task='+controller+'.removeServiceFile&'+JoomBriToken,
				method: 'post',
				data: {'attachvalue': attachvalue },
				onSuccess: function(responseText, responseXML){
					//alert(responseText);
				}
			});
			myRequest.send();
			myDropzone.options.maxFiles = myDropzone.options.maxFiles + 1;
		}
	});
	 
	// Setup the buttons for all transfers
	// The "add files" button doesn't need to be setup because the config `clickable` has already been specified.
	document.querySelector("#actions .start").onclick = function() {
	  myDropzone.enqueueFiles(myDropzone.getFilesWithStatus(Dropzone.ADDED));
	};
	document.querySelector("#actions .cancel").onclick = function() {
	  myDropzone.removeAllFiles(true);
	};
}

function getLocation(el, task){
 	var location_id = el.get('value');
 	var curLevel = el.get('data-level-id').toInt();
 	var nxtLevel = curLevel + 1;
 	
 	$('id_location').set('value', location_id);	//set the current location id for saving
 	
 	$('level'+curLevel).getAllNext('select').dispose();	//remove the children levels when parent level is changed
 	
	var myRequest = new Request({
		//url: 'index.php?option=com_jblance&task=project.getlocationajax'+'&'+JoomBriToken,
		url: 'index.php?option=com_jblance&task='+task+'&'+JoomBriToken,
		method: 'post',
		data: {'location_id':location_id, 'cur_level': curLevel, 'nxt_level': nxtLevel, 'task_val': task}, 
		onRequest: $('ajax-container').addClass('jbloading'),
		onComplete: function(response){	//alert(response);
 			if(response != 0){
				var selectEl = Elements.from(response);
				selectEl.inject($('level'+curLevel), 'after');
				
			}
 			$('ajax-container').removeClass('jbloading');
		}
	});
	myRequest.send();
}