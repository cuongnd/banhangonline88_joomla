<?php

JHtml::_('jquery.framework');
JHtml::_('bootstrap.framework');
//JHtml::_('bootstrap.loadCss'); //leave it too template to load bootstrap css

$userDetails = $displayData;

$links = array(
		'newarticle'=>'index.php?option=com_content&view=form&layout=edit'.ARKMenuHelper::getItemId('com_content',array('form'=>'edit','categories' => NULL)),
		'newweblink'=>'index.php?option=com_weblinks&view=form&layout=edit'.ARKMenuHelper::getItemId('com_weblinks',array('form'=>'edit','categories' => NULL)),
		'profile'=>'index.php?option=com_users&view=profile'.ARKMenuHelper::getItemId('com_users',array('profile'=>NULL)),
		'editprofile'=>'index.php?option=com_users&view=profile&layout=edit'.ARKMenuHelper::getItemId('com_users',array('profile'=>'edit')),
		'logout'=>'index.php?option=com_users&task=&user.logout'
)




?>
<style>
	#ark-navbar .title { letter-spacing: 0;}
	#ark-navbar { Background: transparent;}
	#userpopup-content , #popup-content  { display: none;}
	.ark .popover-title{ background-color: #2a2f31;border-bottom-color: #080808; color :#0088CC } 
	.ark .popover-content{ background-color: #2a2f31;  color :#0088CC}
	.ark .popover.top .arrow:after { border-top-color: #2a2f31; color :#0088CC }
	.ark .popover-title a,.popover-content a,
	.ark .popover-title a:hover,.popover-content a:hover,
	.ark .popover-title a:focus,.popover-content a:focus { color :#0088CC}
	.ark .popover { background-color: #2a2f31; border-color: #2a2f31; white-space:nowrap;}
    .ark li.popup .popover { min-width: 115px; min-height:68px; font-family: Calibri; font-size: 12px;border-radius: 0px;}
	.ark .navbar-inner { background-color: #eaeaea;background-image: linear-gradient(to bottom, #f4f4f4, #cfd1cf);} 
	.ark li.popup .popover.top .arrow { left: 15% }
	.ark li.popup a.title {/*background-image:url('layouts/joomla/arkeditor/images/new.png'); background-repeat:no-repeat;background-position: left center;*/}
	.ark li.popup a.title {/*margin-left:12px;*/}
	.ark li.popup a.title span {/*padding-left:12px;*/}
	.ark.navbar { font-family: Calibri; font-size: 12px; color :#0088CC;}
	 ul.nav.right {float:right; left:auto; right:10px; }
	.ark ul.nav li { line-height: 20px; background:none; margin-bottom: 0px; padding-left: 0px;}
	.ark li.userpopup p {margin-top: auto; margin-bottom:10px;}	
	.ark li.userpopup a.title { background-image:url('layouts/joomla/arkeditor/images/user.png'); background-repeat:no-repeat;background-position: right center;}
	.ark li.userpopup a.title span {padding-right:12px;}
	.ark li.userpopup .popover { min-width: 174px; min-height:104px; font-family: Calibri; font-size: 12px;border-radius: 0px;}
	.ark li.userpopup .popover.top .arrow { left: 86% }
	@font-face {
	  font-family: 'Glyphicons Halflings';
	  src: url('layouts/joomla/arkeditor/images/fonts/glyphicons-halflings-regular.eot');
	  src: url('layouts/joomla/arkeditor/images/fonts/glyphicons-halflings-regular.eot?#iefix') format('embedded-opentype'),
		   url('layouts/joomla/arkeditor/images/fonts/glyphicons-halflings-regular.woff') format('woff'),
		   url('layouts/joomla/arkeditor/images/fonts/glyphicons-halflings-regular.ttf') format('truetype'),
		   url('layouts/joomla/arkeditor/images/fonts/glyphicons-halflings-regular.svg#glyphicons_halflingsregular') format('svg');
	}
	.glyphicon { display: inline-block; font-family: 'Glyphicons Halflings'; font-style: normal; font-weight: normal; line-height: 1; position: relative; top: 1px;}
	.glyphicon-floppy-saved:before { content: "";}
	.glyphicon-plus-sign:before { content: "";}
	.glyphicon-off:before { content: "";}
	.ark.navbar .glyphicon {font-size:14px;top:4px;padding-right: 12px;}
	li.save a {padding-left:0px;}
	div.alert { display:none;} 
	div.alert-success { font-size: 14px;}
	.ark.navbar .nav > li > a:hover, .ark.navbar .nav > li > a:focus { background-color: transparent; color: #8b8e94;text-decoration: none;}
</style>
<div class="alert alert-success">
	  <button data-dismiss="" class="close" type="button">×</button>
	  <strong>Saved!</strong> Successfully saved all items.
  </div>
 <div class="ark navbar navbar-fixed-bottom" style="position:fixed">
    <div class="navbar-inner">
		 <ul class="nav">
			<li class="popup">
				<a href="#" class="title"><span class="glyphicon glyphicon-plus-sign"></span><span>New</span></a>
			</li>
			<li class="save"><a href="javascript:void(0);"><span class="glyphicon glyphicon-floppy-saved"></span><span class="title">Save</span></a></li>
			<li class="disable"><a href="javascript:void(0);"><span class="glyphicon glyphicon-off"></span><span class="title">Disable</span></a></li>
			</ul>
			<ul class="nav right">
			 	<li class="userpopup"><a class="title" href="#"><span>Hi, <?php echo $userDetails->name; ?></span></a></li>
			</ul>
	 </div>
 </div>
 <div id="popup-content">
	<div id="article"><a href="<?php echo JRoute::_($links['newarticle']);?>" target="_blank">New Article</a></div>
	<div id ="link"><a href="<?php echo $links['newweblink'];?>" target="_blank">New Web Link</a></div>
</div>
 <div id="userpopup-content">
	<img src="<?php echo  'http://www.gravatar.com/avatar/'.md5( strtolower( trim( $userDetails->email ) ) ).'?s=64&d='.urlencode(JURI::root().'layouts/joomla/arkeditor/images/large-user.png'); ?>" style="float:left;margin-right:9px;">
	<p id="article"><a href="<?php echo JRoute::_($links['profile']);?>" target="_blank"><?php echo $userDetails->username; ?></a></p>
	<div id ="link"><a href="<?php echo JRoute::_($links['editprofile']);?>" target="_blank">Edit Profile</a></div>
	<div id ="link"><a href="<?php echo JRoute::_($links['logout']);?>">log out</a></div>
</div>
 
<script type="text/javascript">

//TODO Look at making this a class

jQuery('li.popup .title').popover({ 
  html: true,
  trigger: 'manual',
  placement: 'top',
  template: '<div class="popover" style="margin-top: 50px;" onmouseover="clearTimeout(timeoutObj);jQuery(this).mouseleave(function() {jQuery(this).hide();});"><div class="arrow"></div><div class="popover-inner"><h3 class="popover-title"></h3><div class="popover-content"><p></p></div></div></div>',
  content: function () {
      return jQuery('#popup-content').html();
  }
}).mouseenter(function(e) {
    jQuery(this).popover('show');
}).mouseleave(function(e) {
    var ref = jQuery(this);
    timeoutObj = setTimeout(function(){
        ref.popover('hide');
    }, 50);
});

jQuery('li.popup .title').on('shown.bs.popover', function () 
{
 jQuery('.popover').css('top',parseInt(jQuery('.popover').css('top')) - 10 + 'px');
});

jQuery('li.userpopup .title').popover({ 
  html: true,
  trigger: 'manual',
  placement: 'top',
  template: '<div class="popover" style="margin-top: 50px;" onmouseover="clearTimeout(timeoutObj);jQuery(this).mouseleave(function() {jQuery(this).hide();});"><div class="arrow"></div><div class="popover-inner"><h3 class="popover-title"></h3><div class="popover-content"><p></p></div></div></div>',
  content: function () {
      return jQuery('#userpopup-content').html();
  }
}).mouseenter(function(e) {
    jQuery(this).popover('show');
}).mouseleave(function(e) {
    var ref = jQuery(this);
    timeoutObj = setTimeout(function(){
        ref.popover('hide');
    }, 50);
});

jQuery('li.userpopup .title').on('shown.bs.popover', function () 
{
 jQuery('.popover').css('left',parseInt(jQuery('.popover').css('left')) - 18 + 'px');
 jQuery('.popover').css('top',parseInt(jQuery('.popover').css('top')) - 10 + 'px');
});


jQuery('li.save a').on('click', function(event)
{
	event.preventDefault();
	jSaveAllInstances();
});

jQuery('li.save a').on('focus', function(event)
{
	jQuery(this).trigger('click');
});

(function()
{
	var hasClicked = false;
	
	function execute()
	{
		jDisableOrEnableAllInstances();
		if(CKEDITOR.enableManualInline)
		{
			if ( window.addEventListener )
					document.body.addEventListener( 'click', CKEDITOR.inlineClick, false );
			else if ( window.attachEvent )
				document.body.attachEvent( 'onclick', CKEDITOR.inlineClick );
			
			jQuery('li.disable a').children('.title').html('Disable');
		}	
		else
		{
			if ( window.removeEventListener )
					document.body.removeEventListener( 'click', CKEDITOR.inlineClick, false );
			else if ( window.detachEvent )
				document.body.detachEvent( 'onclick', CKEDITOR.inlineClick );
			
			jQuery('li.disable a').children('.title').html('Enable');
		}	
	}
	
	jQuery('li.disable a').on('mousedown : click', function(event)
	{
		if(event.type == 'mousedown')
		{
			execute();
			hasClicked = true;
		}
		if(event.type == 'focus' && !hasClicked)
		{
			execute();
			hasClicked = true;
		}
		hasClicked = false;
	});
})();
jQuery('div.alert .close').on('click', function(event)
{
	jQuery(this).parent().css('display','none');
});

</script>