<?php
/*
* Pixel Point Creative - Cinch Menu Module
* License: GNU General Public License version
* See: http://www.gnu.org/copyleft/gpl.html
* Copyright (c) Pixel Point Creative LLC.
* More info at http://www.pixelpointcreative.com
* Last Updated: 2/18/14
*/

// No direct access.
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<script type="text/javascript">
var onProcess = false;
jQuery(document).ready(function($){
	var acMenu = $("#accordion_menu_<?php echo $module->id;?>");
	acMenu.find("a").click(function(){
		if ($(this).attr("target") == '_blank') {
			window.open($(this).attr("href"));
		} else {
			location = $(this).attr("href");
		}
		return false;
	});

	try{
		var current = $("#accordion_menu_<?php echo $module->id;?> li.opened");
		var root = current.parents('.accordion-menu'), lis = current.parents('li');
		$('li', root).filter(lis).addClass('opened');
	} catch(e){
		console.log(e.message);
	}
	$("#accordion_menu_<?php echo $module->id;?> li.opened > .ul-wrapper").css("display","block");
	$("#accordion_menu_<?php echo $module->id;?> li.opened > .item-wrapper .menu-button img").attr("src", "<?php echo $bulletActive;?>");

<?php if($event == "click"){?>
	acMenu.find(".item-wrapper").click(function(){
		var li = $(this).parent('li');
		if(li.hasClass("opened")){
			// Close this item and once slideUp is complete, ensure children are also closed
			li.children(".ul-wrapper").slideUp(<?php echo $duration;?>, function() {
				li.find(".item-wrapper > .menu-button > img").attr("src", "<?php echo $bulletImage;?>");
				li.find("li.opened").removeClass("opened");
				li.find(".ul-wrapper").css("display","none");
				li.removeClass("opened");
			});
		}else{
			// Close all siblings (and their children) and open this one
			var openedLi = li.parent().children("li.opened");
			openedLi.children(".ul-wrapper").slideUp(<?php echo $duration;?>, function() {
				openedLi.find(".item-wrapper > .menu-button > img").attr("src", "<?php echo $bulletImage;?>");
				openedLi.find("li.opened").removeClass("opened");
				openedLi.find(".ul-wrapper").css("display","none");
				openedLi.removeClass("opened");
			});
			var ul = li.children(".ul-wrapper");
			if(ul.length != 0){
				li.addClass("opened");
				li.children(".item-wrapper").children(".menu-button").children("img").attr("src", "<?php echo $bulletActive;?>");
				ul.slideDown(<?php echo $duration;?>);
			}
		}
		return false;
	});
});
<?php }else{?>
	acMenu.find("li").mouseenter(function(){
		if(onProcess) return true;
		var ul = $(this).children(".ul-wrapper");
		if(ul.length){
			onProcess = true;
			$(this).addClass("opened");
			$(this).children(".item-wrapper").children(".menu-button").children("img").attr("src", "<?php echo $bulletActive;?>");
			ul.slideDown(<?php echo $duration;?>,function(){
				onProcess = false;
			});
		}
	}).mouseleave(function(){
		if(onProcess) return true;
		if($(this).children(".ul-wrapper").length){
			onProcess = true;
			$(this).children(".item-wrapper").children(".menu-button").children("img").attr("src", "<?php echo $bulletImage;?>");
			$(this).children(".ul-wrapper").slideUp(<?php echo $duration;?>,function(){
				onProcess = false;
			});
		}
		onProcess = false;
		$(this).removeClass("opened");
	});
});
<?php } ?>
</script>