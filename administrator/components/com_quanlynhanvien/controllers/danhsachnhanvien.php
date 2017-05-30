<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Quanlynhanvien
 * @author     cuong <nguyendinhcuong@gmail.com>
 * @copyright  2016 cuong
 * @license    bản quyền mã nguồn mở GNU phiên bản 2
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Danhsachnhanvien controller class.
 *
 * @since  1.6
 */
class QuanlynhanvienControllerDanhsachnhanvien extends JControllerForm
{
	/**
	 * Constructor
	 *
	 * @throws Exception
	 */
	public function __construct()
	{
		$this->view_list = 'danhsachnhanviens';
		parent::__construct();
	}
}
