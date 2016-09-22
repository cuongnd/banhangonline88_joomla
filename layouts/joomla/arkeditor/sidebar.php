<?php

JHtml::_('jquery.framework');
JHtml::_('bootstrap.framework');
//JHtml::_('bootstrap.loadCss');

$userDetails = $displayData;

$links = array(
		'newarticle'=>'index.php?option=com_content&view=form&layout=edit'.ARKMenuHelper::getItemId('com_content',array('form'=>'edit','categories' => NULL))
);

$base = JURI::base();

?>
<style>
    
    .ark ul { padding:  0px}
   .ark.navbar .nav > li > a {
    color: #555;
    float: none;
    padding: 11px 15px;
    text-decoration: none;
    text-shadow: 0 1px 0 #ffffff;
    }
    .ark.navbar {
    margin-bottom: 18px;
    overflow: visible;
    }
    .ark .navbar-inner:before, .navbar-inner:after {
    content: "";
    display: table;
    line-height: 0;
    }
    .ark .navbar-inner:after {
        clear: both;
    }
    .ark .nav > li > a {
        display: block;
    }
    .ark.navbar .nav {
        display: block;
        float: left;
        left: 0;
        margin: 0 10px 0 0;
        position: relative;
    }
    .ark .nav { list-style: none outside none;}

    .ark.navbar .nav > li {
        float: left;
    }
    .ark.navbar .nav > li.logo > a {
        padding: 11px 10px 9px;
    }
    .ark.navbar .nav > li > a {
        color: #444444;
        padding: 11px 10px;
        text-shadow: 0 1px 0 #ffffff;
    }
    .ark.navbar .nav > li > a { float: none;text-decoration: none;}

    .ark .nav > li > a { display: block;}
 	.ark .navbar-inner { 
		background-color: #eaeaea;background-image: linear-gradient(to bottom, #f4f4f4, #eaeaea);
		-webkit-border-radius: 30px;
		-moz-border-radius: 30px;
		border-radius: 30px; 
        min-height: 40px;
		border: 1px solid #d4d4d4;
		box-shadow: 0 1px 4px rgba(0, 0, 0, 0.067);
	} 
	#ark-navbar { font-family: Calibri; font-size: 12px; color :#0088CC;}
    #ark-navbar .title  { font-family: Calibri; font-size: 12px;line-height:normal;}
	.ark ul.nav.right {float:right; left:auto; right:10px; }
	.ark ul.nav li { line-height: 20px; background:none; margin-bottom: 0px; padding-left: 0px;}
	@font-face {
	  font-family: 'Glyphicons Halflings';
	  src: url('<?php echo $base;?>layouts/joomla/arkeditor/images/fonts/glyphicons-halflings-regular.eot');
	  src: url('<?php echo $base;?>layouts/joomla/arkeditor/images/fonts/glyphicons-halflings-regular.eot?#iefix') format('embedded-opentype'),
		   url('<?php echo $base;?>layouts/joomla/arkeditor/images/fonts/glyphicons-halflings-regular.woff') format('woff'),
		   url('<?php echo $base;?>layouts/joomla/arkeditor/images/fonts/glyphicons-halflings-regular.ttf') format('truetype'),
		   url('<?php echo $base;?>layouts/joomla/arkeditor/images/fonts/glyphicons-halflings-regular.svg#glyphicons_halflingsregular') format('svg');
	}
	
	@font-face {
	  font-family: 'arklogo';
	  src: url('<?php echo $base;?>layouts/joomla/arkeditor/images/fonts/arklogo.eot?45275132');
	  src: url('<?php echo $base;?>layouts/joomla/arkeditor/images/fonts/arklogo.eot?45275132#iefix') format('embedded-opentype'),
		   url('<?php echo $base;?>layouts/joomla/arkeditor/images/fonts/arklogo.woff?45275132') format('woff'),
		   url('<?php echo $base;?>layouts/joomla/arkeditor/images/fonts/arklogo.ttf?45275132') format('truetype'),
		   url('<?php echo $base;?>layouts/joomla/arkeditor/images/fonts/arklogo.svg?45275132#arklogo') format('svg');
	  font-weight: normal;
	  font-style: normal;
	}
	
	.arkicon { display: inline-block; font-family: 'arklogo'; line-height: 1; position: relative; top: 1px; color: #0099cc;}
	.glyphicon { display: inline-block; font-family: 'Glyphicons Halflings'; font-style: normal; font-weight: normal; line-height: 1; position: relative; top: 1px;}
	
	.arkicon-arkeditor:before { content: "\e801";}
	.glyphicon-floppy-saved:before { content: "";}
	.glyphicon-plus-sign:before { content: "";}
	.glyphicon-off:before { content: "";}
	.ark.navbar span {position:relative;top:9px;}
	.ark.navbar .glyphicon {font-size:14px;top:12px;padding-right: 12px;}
	.ark.navbar .arkicon {font-size:35px;top:0px}
	li.save a {padding-left:0px;}
	div.alert { display:none;} 
	div.alert-success { font-size: 14px;}
	.ark.navbar {position: fixed;width:340px;top:45%;right:-275px;z-index:1001;display: block;}
	.ark .navbar-inner { padding-left: 0px;}
	.ark.navbar .nav > li > a {padding: 11px 10px;color: #444444; text-shadow: 0px 1px 0px #ffffff;}
	.ark.navbar .nav > li.logo > a { padding: 11px 10px 9px 10px;}
	.ark.navbar a:focus {outline:none;}
	.ark.navbar a::-moz-focus-inner {border:0;}
    .ark.navbar .nav > li.logo > a:hover {opacity: 0.7;}
    .ark.navbar .nav > li > a {background-color: transparent;text-decoration: none;}
	.ark.navbar .nav > li > a:hover, .ark.navbar .nav > li > a:focus { background-color: transparent; color: #8b8e94;text-decoration: none;}
    .arkicon-disable {color: #d4d4d4;}
	.ark-navbar .nav > li.logo > a {position: static;}
	#ark-navbar .title { margin: 0; border: none;  display: initial;}
</style>
<div class="alert ark inline alert-alert">
	  <button data-dismiss="" class="close" type="button">×</button>
	  <strong>Saved!</strong> Please note: You may have to refresh this page to see the fully rendered content.
</div>
<div class="alert ark inline alert-success">
	  <button data-dismiss="" class="close" type="button">×</button>
	  <strong>Saved!</strong> Successfully saved all items.
</div>
 <div class="ark navbar" id="ark-navbar">
    <div class="navbar-inner">
		 <ul class="nav">
			<li class="logo"><a href="javascript:void(0);"><span class="arkicon arkicon-arkeditor"></span></a></li>
			<li class="save"><a href="javascript:void(0);"><span class="glyphicon glyphicon-floppy-saved"></span><span class="title">Save</span></a></li>
			<li class="article">
				<a href="<?php echo JRoute::_($links['newarticle']);?>" target="_blank"><span class="glyphicon glyphicon-plus-sign"></span><span>New</span></a>
			</li>
			<li class="disable"><a href="javascript:void(0);"><span class="glyphicon glyphicon-off"></span><span class="title">Disable</span></a></li>
			</ul>
	 </div>
 </div>
<script type="text/javascript">

    //TODO Look at making this a class

    (function ($) //navbar slider
    {
        $(function () {
            $(".ark .nav .logo a").on('click', function () {
                if ($(".ark .navbar-inner").parent().css("right") == "-275px") {
                    $(".ark .navbar-inner").parent().animate({ right: '-55px' }, { queue: false, duration: 500 });
                } else {
                    $(".ark .navbar-inner").parent().animate({ right: '-275px' }, { queue: false, duration: 500 });
                }
            });
        });
    })(jQuery)


    jQuery('li.save a').on('click', function (event) {
        event.preventDefault();
        jSaveAllInstances();
    });

    jQuery('li.save a').on('focus', function (event) {
        jQuery(this).trigger('click');
    });

    (function () {
        var hasClicked = false;

        function execute(toggleNativeLinks) {
            jDisableOrEnableAllInstances(toggleNativeLinks);
            if (CKEDITOR.enableManualInline) {
                if (window.addEventListener)
                    document.body.addEventListener('click', CKEDITOR.inlineClick, false);
                else if (window.attachEvent)
                    document.body.attachEvent('onclick', CKEDITOR.inlineClick);

                jQuery('li.disable a').children('.title').html('Disable');
                jQuery('#ark-navbar li span.arkicon-arkeditor').toggleClass('arkicon-disable', false);
				url = 'index.php?option=com_ajax&plugin=inlinemodestatelistener&format=json&state=0';
				jQuery.get(url,function( response ){});		
            }
            else {
                if (window.removeEventListener)
                    document.body.removeEventListener('click', CKEDITOR.inlineClick, false);
                else if (window.detachEvent)
                    document.body.detachEvent('onclick', CKEDITOR.inlineClick);

                jQuery('li.disable a').children('.title').html('Enable');
                jQuery('#ark-navbar li span.arkicon-arkeditor').toggleClass('arkicon-disable', true);
				url = 'index.php?option=com_ajax&plugin=inlinemodestatelistener&format=json&state=1';
				jQuery.get(url,function( response ){});	
            };
        }

        jQuery('li.disable a').on('mousedown : click', function (event) {
            if (event.type == 'mousedown') {
                execute(true);
                hasClicked = true;
            }
            if (event.type == 'focus' && !hasClicked) {
                execute(true);
                hasClicked = true;
            }
            haslicked = false;
        });
        CKEDITOR.on('autoDisableInline', function () {
            execute();
        });
    })();
       jQuery('div.ark.inline.alert-success .close').on('click', function (event) {
        jQuery(this).parent().css('display', 'none');
    });
	 jQuery('div.ark.inline.alert-alert .close').on('click', function (event) {
        jQuery(this).parent().css('display', 'none');
    });
	if(CKEDITOR.disableInlineEventHandlers) jQuery('#ark-navbar').css('display','none');
	
</script>