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
jQuery(document).ready(function($){

	var acMenu = $("#flyout_menu_<?php echo $module->id;?>");
	acMenu.find("a").click(function(){
		if ($(this).attr("target") == '_blank') {
			window.open($(this).attr("href"));
		} else {
			location = $(this).attr("href");
		}
		return false;
	});

<?php if($event == "click"){?>
	acMenu.find(".item-wrapper").click(function(){
		var li = $(this).parent('li');
		if(li.hasClass("opened")){
			// Close this item and once hide is complete, ensure children are also closed
			li.children(".ul-wrapper").hide(<?php echo $duration;?>, function() {
				li.find(".menu-button > img").attr("src", "<?php echo $bulletImage;?>");
				li.find("li.opened").removeClass("opened").children(".ul-wrapper").css("display","none");
				li.removeClass("opened");
			});
		}else{
			// Close all siblings (and their children) and open this one
			var openedLi = li.siblings("li.opened");
			openedLi.find(".item-wrapper > .menu-button > img").attr("src", "<?php echo $bulletImage;?>");
			openedLi.find("li.opened .ul-wrapper").css("display","none");
			openedLi.find("li.opened").removeClass("opened");
			openedLi.children(".ul-wrapper").hide(<?php echo $duration;?>, function () {
				openedLi.removeClass('opened');
			});
			li.addClass("opened");
			li.children(".item-wrapper").children(".menu-button").children("img").attr("src", "<?php echo $bulletActive;?>");
			li.children(".ul-wrapper").show(<?php echo $duration;?>);
		}
		return false;
	});
	$("body").click(function(){
		$(".flyout-menu .opened").removeClass("opened");
		$(".flyout-menu .ul-wrapper").hide(<?php echo $duration;?>);
		$(".flyout-menu .menu-button > img").attr("src", "<?php echo $bulletImage;?>");
	});
});
<?php }else{ ?>
	acMenu.find("li").mouseleave(function(){
		$(this).removeClass("opened");
		$(this).children(".item-wrapper").children(".menu-button").children("img").attr("src", "<?php echo $bulletImage;?>");
		$(this).children(".ul-wrapper").hide(<?php echo $duration;?>);
	}).mouseenter(function(){
		$(this).children(".item-wrapper").children(".menu-button").children("img").attr("src", "<?php echo $bulletActive;?>");
		$(this).addClass("opened");
		$(this).children(".ul-wrapper").show(<?php echo $duration;?>);
	});
});
<?php } ?>
</script>
