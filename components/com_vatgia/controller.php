<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Vatgia
 * @author     cuong <nguyendinhcuong@gmail.com>
 * @copyright  2016 cuong
 * @license    bản quyền mã nguồn mở GNU phiên bản 2
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

/**
 * Class VatgiaController
 *
 * @since  1.6
 */
class VatgiaController extends JControllerLegacy
{
	/**
	 * Method to display a view.
	 *
	 * @param   boolean $cachable  If true, the view output will be cached
	 * @param   mixed   $urlparams An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JController   This object to support chaining.
	 *
	 * @since    1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
        $app   = JFactory::getApplication();
        $start = $app->input->getInt('limitstart', 0);

        if ($start == 0)
        {
            $app->input->set('limitstart', 0);
        }

        $view = $app->input->getCmd('view', '');
		$app->input->set('view', $view);

		parent::display($cachable, $urlparams);

		return $this;
	}
}
