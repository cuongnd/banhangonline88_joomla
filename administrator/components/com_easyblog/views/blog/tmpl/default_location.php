<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

$hasInitialLocation = (isset($this->blog->address)) ? "hasInitialLocation" : "";
?>

<script type="text/javascript">
EasyBlog.require()
	.script(
		"location"
	)
	.done(function($){

		$(".locationForm").implement(
			"EasyBlog.Controller.Location.Form",
			{
				<?php if ($hasInitialLocation): ?>
				initialLocation: "<?php echo $this->blog->address; ?>",
				<?php endif; ?>

				language: "<?php echo $this->config->get( 'main_locations_blog_language', 'en' ); ?>",

				"{locationInput}"    : "input[name=address]",

				"{locationLatitude}" : "input[name=latitude]",

				"{locationLongitude}": "input[name=longitude]",

				mapType	: "<?php echo $this->config->get( 'main_locations_map_type' );?>"
			}
		)
	});
</script>

<ul class="list-form reset-ul">
	<li>
		<label><?php echo JText::_( 'COM_EASYBLOG_LOCATIONS_LOCATION' );?></label>

		<div class="locationForm <?php echo $hasInitialLocation; ?>">
			<input type="text" name="address" class="input has-icon width-250 publish-location loading" disabled="disabled" autocomplete="off" value="<?php echo $this->blog->address;?>" />
			<a class="button autoDetectButton" href="javascript: void(0);"><?php echo JText::_("COM_EASYBLOG_LOCATIONS_USE_CURRENT");?></a>
			<input type="hidden" name="latitude" value="<?php echo $this->blog->latitude;?>" />
			<input type="hidden" name="longitude" value="<?php echo $this->blog->longitude;?>" />
			<div class="locationMap">&nbsp;</div>
		</div>
	</li>
</ul>
