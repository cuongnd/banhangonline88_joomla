<?php

/**

 * @copyright	Copyright (C) 2012 Cedric KEIFLIN alias ced1870

 * http://www.joomlack.fr

 * Module Slideshow CK

 * @license		GNU/GPL

 * */

// no direct access

defined('_JEXEC') or die;

// JHtml::_('behavior.modal');

require_once dirname(__FILE__) . '/helper.php';



if ($params->get('slideshowckhikashop_enable', '0') == '1') {

	if (JFile::exists(JPATH_ROOT . '/plugins/system/slideshowckhikashop/helper/helper_slideshowckhikashop.php')) {

		require_once JPATH_ROOT . '/plugins/system/slideshowckhikashop/helper/helper_slideshowckhikashop.php';

		$items = modSlideshowckhikashopHelper::getItems($params);

	} else {

		echo '<p style="color:red;font-weight:bold;">File /plugins/system/slideshowckhikashop/helper/helper_slideshowckhikashop.php not found ! Please download the patch for Slideshow CK - Hikashop on <a href="http://www.joomlack.fr">http://www.joomlack.fr</a></p>';

		return false;

	}

} else if ($params->get('slideshowckjoomgallery_enable', '0') == '1') {

	if (JFile::exists(JPATH_ROOT . '/plugins/system/slideshowckjoomgallery/helper/helper_slideshowckjoomgallery.php')) {

		require_once JPATH_ROOT . '/plugins/system/slideshowckjoomgallery/helper/helper_slideshowckjoomgallery.php';

		$items = modSlideshowckjoomgalleryHelper::getItems($params);

	} else {

		echo '<p style="color:red;font-weight:bold;">File /plugins/system/slideshowckjoomgallery/helper/helper_slideshowckjoomgallery.php not found ! Please download the patch for Slideshow CK - Hikashop on <a href="http://www.joomlack.fr">http://www.joomlack.fr</a></p>';

		return false;

	}

} else if ($params->get('slideshowckvirtuemart_enable', '0') == '1') {

	if (JFile::exists(JPATH_ROOT . '/plugins/system/slideshowckvirtuemart/helper/helper_slideshowckvirtuemart.php')) {

		require_once JPATH_ROOT . '/plugins/system/slideshowckvirtuemart/helper/helper_slideshowckvirtuemart.php';

		$items = modSlideshowckvirtuemartHelper::getItems($params);

	} else {

		echo '<p style="color:red;font-weight:bold;">File /plugins/system/slideshowckvirtuemart/helper/helper_slideshowckvirtuemart.php not found ! Please download the patch for Slideshow CK - Hikashop on <a href="http://www.joomlack.fr">http://www.joomlack.fr</a></p>';

		return false;

	}

} else {

	switch ($params->get('slidesssource', 'slidesmanager')) {

		case 'folder':

			$items = modSlideshowckHelper::getItemsFromfolder($params);



			break;

		case 'autoloadfolder':

			$items = modSlideshowckHelper::getItemsAutoloadfolder($params);



			break;

		case 'autoloadarticlecategory':

			$items = modSlideshowckHelper::getItemsAutoloadarticlecategory($params);

			break;

		case 'flickr':

			$items = modSlideshowckHelper::getItemsAutoloadflickr($params);

			break;

		default:

			$items = modSlideshowckHelper::getItems($params);

			break;

	}



	if ($params->get('displayorder', 'normal') == 'shuffle')

		shuffle($items);

}
echo json_encode($items);
