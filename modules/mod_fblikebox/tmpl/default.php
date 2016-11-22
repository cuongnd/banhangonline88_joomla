<?php
/*------------------------------------------------------------------------
# mod_ultimatefacebooklikeboxslider
# ------------------------------------------------------------------------
# @author - Twitter Slider
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# copyright Copyright (C) 2013 TwitterSlider.com. All Rights Reserved.
# Websites: http://twitterslider.com/
# Technical Support:  Forum - http://twitterslider.com/index.php/forum
-------------------------------------------------------------------------*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
JHtml::_('jQuery.lazyload');
?>

<?php switch ($params->get("fbRendering",0)){

    case 1: // XFBML ?>

<?php if($params->get("facebookRootDiv", 1)) {?>
<div id="fb-root"></div>
<?php }?>

<?php if($params->get("fbLoadJsLib", 1)) {?>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/<?php echo $locale;?>/all.js#xfbml=1<?php echo $facebookLikeAppId;?>";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
<?php }?>

    <fb:like-box
    href="<?php echo $params->get("fbPageLink");?>"
    width="<?php echo $params->get("fbWidth");?>"
    height="<?php echo $params->get("fbHeight");?>"
    colorscheme="<?php echo $params->get("fbColour");?>"
    show_faces="<?php echo (!$params->get("fbFaces")) ? "false" : "true";?>"
    border_color="<?php echo $params->get("fbBColour", "");?>"
    stream="<?php echo (!$params->get("fbStream")) ? "false" : "true";?>"
    header="<?php echo (!$params->get("fbHeader")) ? "false" : "true";?>"
    force_wall="<?php echo (!$params->get("facebookForceWall")) ? "false" : "true";?>"></fb:like-box>

<?php break; ?>


<?php case 2: // HTML5 ?>

<?php if($params->get("facebookRootDiv", 1)) {?>
<div id="fb-root"></div>
<?php }?>

<?php if($params->get("fbLoadJsLib", 1)) {?>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/<?php echo $locale;?>/all.js#xfbml=1<?php echo $facebookLikeAppId;?>";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
<?php }?>

	<div class="fb-like-box"
	data-href="<?php echo $params->get("fbPageLink");?>"
	data-width="<?php echo $params->get("fbWidth");?>"
	data-height="<?php echo $params->get("fbHeight");?>"
	data-colorscheme="<?php echo $params->get("fbColour");?>"
	data-show-faces="<?php echo $params->get("fbFaces", 1); ?>"
	data-border-color="<?php echo $params->get("fbBColour", "");?>"
	data-stream="<?php echo $params->get("fbStream", 1); ?>"
	data-header="<?php echo $params->get("fbHeader", 1); ?>"
	data-force-wall="<?php echo (!$params->get("facebookForceWall")) ? "false" : "true";?>"></div>
<?php break; ?>


<?php default: // iframe ?>

<?php
	$forceWall ="";
	if($params->get("facebookForceWall"))  {
		$forceWall = '&amp;force_wall=true';
	}
 ?>

<iframe
	data-src="http://www.facebook.com/plugins/likebox.php?href=<?php echo $params->get("fbPageLink");?>&amp;locale=<?php echo $locale;?>&amp;width=<?php echo $params->get("fbWidth");?>&amp;colorscheme=<?php echo $params->get("fbColour");?>&amp;show_faces=<?php echo $params->get("fbFaces", 1);?>&amp;border_color=<?php echo rawurlencode($params->get("fbBColour", ""));?>&amp;stream=<?php echo $params->get("fbStream", 1);?>&amp;header=<?php echo $params->get("fbHeader", 1);?>&amp;height=<?php echo $params->get("fbHeight");?><?php echo $facebookLikeAppId;?><?php echo $forceWall;?>"
scrolling="no"
frameborder="0"
style="border:none; overflow:hidden; width:<?php echo $params->get("fbWidth");?>; height:<?php echo $params->get("fbHeight");?>px;"
allowTransparency="true"></iframe>

<?php break; ?>

<?php }// END switch?>
<div style="font-size: 9px; color: #808080; font-weight: normal; font-family: tahoma,verdana,arial,sans-serif; line-height: 1.28; text-align: right; direction: ltr;"><a class="nolink"></a></div>
