<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Modules
 * @author     cuong <nguyendinhcuong@gmail.com>
 * @copyright  2016 cuong
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;
use Joomla\Registry\Registry;
jimport('joomla.application.component.controllerform');

/**
 * Module controller class.
 *
 * @since  1.6
 */
class ModulesControllerModule extends JControllerLegacy
{
	public function ajax_render_module()
	{
		$input=JFactory::getApplication()->input;

		$post=json_decode(file_get_contents('php://input'));

		$module_id=$post->module_id;
		$table_module=JTable::getInstance('module');
		$table_module->load($module_id);
		$module_name=$table_module->module;
		$helper_file=JPATH_ROOT.DS."modules/$module_name/helper.php";
		$html='';
		/*if(JFile::exists($helper_file))
		{
			require_once $helper_file;
			if(class_exists($module_name))
			{
				$instance = new $module_name;
				$module=(object)$table_module->getProperties();
				$temp = new Registry;
				$temp->loadString($module->params);
				$module->params=$temp;
				$html=$instance->render_module($module);
			}

		}*/
		echo $input->get('ignoreMessages', true, 'bool');
		die;
		echo new JResponseJson($html, null, false, $input->get('ignoreMessages', true, 'bool'));
	}

}
