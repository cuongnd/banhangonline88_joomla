<?php
/**
 * @package    HikaSerial for Joomla!
 * @version    1.10.4
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php
class ArticlesController extends hikaserialController {

	protected $rights = array(
		'display' => array('serialprivatecontent'),
		'add' => array(),
		'edit' => array(),
		'modify' => array(),
		'delete' => array()
	);

	public function serialprivatecontent() {
		JRequest::checkToken('request') || die('Invalid Token');

		$formData = JRequest::getVar('data', array(), '', 'array');
		if(!empty($formData['hikaserial']) && $this->serialprivatecontet_process($formData['hikaserial']) === true)
			return;

		JRequest::setVar('layout', 'serialprivatecontent');
		return $this->display();
	}

	private function serialprivatecontet_process($formData) {
		$app = JFactory::getApplication();

		if(empty($formData['pack'])) {
			$app->enqueueMessage(JText::_('PACK_DATA_NONE'), 'error');
			return false;
		}

		JArrayHelper::toInteger($formData['pack']);

		$ed_name = JRequest::getString('ed_name', '');
		$params = array(
			'pack:' . implode(',', $formData['pack'])
		);
		if(!empty($formData['module']))
			$params[] = 'module:1';

		if(!empty($formData['product']))
			$params[] = 'product:'.(int)$formData['product'];

		if(!empty($formData['text']))
			$params[] = 'text:\"'.htmlspecialchars($formData['text'], ENT_COMPAT, 'UTF-8').'\"';

		if(!empty($formData['delimiter']))
			$content = '{privatecontent '.implode(' ', $params).'/}';
		else
			$content = '{privatecontent '.implode(' ', $params).'}{/privatecontent}';

		echo '
<html>
<body>
<script type="text/javascript">
window.parent.jInsertEditorText("'.$content.'", "'.$ed_name.'");
window.parent.jModalClose();
</script>
</body>
</html>
';
		return true;
	}
}
