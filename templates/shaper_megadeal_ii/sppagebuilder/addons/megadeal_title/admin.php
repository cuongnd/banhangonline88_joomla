<?php
/**
* @package SP Page Builder
* @author JoomShaper http://www.joomshaper.com
* @copyright Copyright (c) 2010 - 2016 JoomShaper
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
*/
//no direct accees
defined ('_JEXEC') or die ('restricted aceess');

SpAddonsConfig::addonConfig(
	array( 
		'type'=>'content',
		'addon_name'=>'sp_megadeal_title',
		'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_MEGADEAL_TITLE'),
		'desc'=>JText::_('COM_SPPAGEBUILDER_ADDON_MEGADEAL_TITLE_DESC'),
		'category'=>'megadeal ii',
		'attr'=>array(
			'admin_label'=>array(
				'type'=>'text', 
				'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_ADMIN_LABEL'),
				'desc'=>JText::_('COM_SPPAGEBUILDER_ADDON_ADMIN_LABEL_DESC'),
				'std'=> ''
				),

			'title'=>array(
				'type'=>'text', 
				'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_TITLE'),
				'desc'=>JText::_('COM_SPPAGEBUILDER_ADDON_TITLE_DESC'),
				'std'=>  ''
				),

			'icon'=>array(
				'type'=>'icon', 
				'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_MEGADEAL_ICON'),
				'desc'=>JText::_('COM_SPPAGEBUILDER_ADDON_MEGADEAL_ICON_DESC'),
				),

			'icon_color'=>array(
				'type'=>'color', 
				'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_MEGADEAL_ICON_COLOR'),
				'desc'=>JText::_('COM_SPPAGEBUILDER_ADDON_MEGADEAL_ICON_COLOR_DESC'),
				'std'=> ''
				),

			'class'=>array(
				'type'=>'text', 
				'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_CLASS'),
				'desc'=>JText::_('COM_SPPAGEBUILDER_ADDON_CLASS_DESC'),
				'std'=> ''
				),
			)
		)
	);