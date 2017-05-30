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
defined( '_JEXEC' ) or die( 'Restricted access' );

$style = <<<EOT
	#flyout_menu_{$module->id} {
		background: {$mainItemColor};
	}
	#flyout_menu_{$module->id} .ul-wrapper,
	#flyout_menu_{$module->id} ul {
		background: {$mainItemColor};
	}
	#flyout_menu_{$module->id} a {
		color: {$textLinkColor};
	}
	#flyout_menu_{$module->id} .item-wrapper:hover a,
	#flyout_menu_{$module->id} li.current > .item-wrapper a,
	#flyout_menu_{$module->id} li.opened > .item-wrapper a {
		color: {$textHoverColor};
	}
	#flyout_menu_{$module->id} li.open,
	#flyout_menu_{$module->id} li:hover {
		background: {$bgHoverColor};
	}

	@media screen and (max-width:767px) {
	#flyout_menu_{$module->id} .ul-wrapper,
	#flyout_menu_{$module->id} ul {
		width: auto;
	}
	#flyout_menu_{$module->id}.horizontal ul .menu-link {
		width: auto;
	}
}

@media screen and (min-width:768px) {
	#flyout_menu_{$module->id} .ul-wrapper,
	#flyout_menu_{$module->id} ul {
		width: {$subWidth};
	}
	#flyout_menu_{$module->id}.msie6 ul,
	#flyout_menu_{$module->id}.msie7 ul,
	#flyout_menu_{$module->id}.msie8 ul {
		width: {$subWidthULIE8}px;
	}
	#flyout_menu_{$module->id}.horizontal.msie6 .menu-link,
	#flyout_menu_{$module->id}.horizontal.msie7 .menu-link,
	#flyout_menu_{$module->id}.horizontal.msie8 .menu-link {
		width: {$subWidthA}px;
	}
	#flyout_menu_{$module->id}.horizontal.msie6 > li > .item-wrapper,
	#flyout_menu_{$module->id}.horizontal.msie7 > li > .item-wrapper,
	#flyout_menu_{$module->id}.horizontal.msie8 > li > .item-wrapper {
		width: {$subWidthLI8}px;
	}
}
EOT;
