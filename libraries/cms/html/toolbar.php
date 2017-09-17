<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  HTML
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('JPATH_PLATFORM') or die;
/**
 * Utility class for icons.
 *
 * @since  3.2
 */
abstract class JHtmlToolbar
{
	/**
	 * Method to generate html code for groups of lists of links
	 *
	 * @param   array  $groupsOfLinks  Array of links
	 *
	 * @return  string
	 *
	 * @since   3.2
	 */
	public static function toolbar_control()
	{
		$toolbar = JToolbar::getInstance('toolbar')->render('toolbar');
		echo $toolbar;
	}
	public static function toolbar_menu($enabled)
	{
		JLoader::register('HtmlMenuHelper', __DIR__ . '/childrenmenu/helper.php');
		JLoader::register('JChildrenSiteCssMenu', __DIR__ . '/childrenmenu/menu.php');
		$lang    = JFactory::getLanguage();
		$user    = JFactory::getUser();
		$input   = JFactory::getApplication()->input;
		$menu    = new JChildrenSiteCssMenu;
		$document = JFactory::getDocument();
		$direction = $document->direction == 'rtl' ? 'pull-right' : '';
		if($enabled){
			require_once __DIR__ . '/childrenmenu/enabled.php';
		}else{
			require_once __DIR__ . '/childrenmenu/disabled.php';
		}
		$website=JFactory::getWebsiteByUserLogin();
		ob_start();
		?>
		<div class="row">
			<div class="col-md-12">
				<div class="navbar pull-left">
				<?php
				$menu->renderMenu('menu', $enabled ? 'nav ' . $direction : 'nav disabled ' . $direction);
				?>
				</div>
				<div class="pull-right">
					<a target="_blank" href="http://<?php echo $website->domain_name ?>.banhangonline88.com"><?php echo "http://$website->domain_name.banhangonline88.com" ?></a>
				</div>
			</div>
		</div>
		<?php
		$content=ob_get_clean();
		echo $content;

	}
}
