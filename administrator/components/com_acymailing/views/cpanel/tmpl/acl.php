<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	4.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2013 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><div id="page-acl">
	<table class="admintable" cellspacing="1">
		<?php
		$acltable = acymailing_get('type.acltable');
		$aclcats['subscriber'] = array('view','manage','delete','export','import');
		$aclcats['lists'] = array('manage','delete','filter');
		$aclcats['newsletters'] = array('manage','delete','send','schedule','spam_test','copy');
		$aclcats['autonewsletters'] = array('manage','delete');
		$aclcats['campaign'] = array('manage','delete');
		$aclcats['tags'] = array('view');
		$aclcats['templates'] = array('view','manage','delete','copy');
		$aclcats['queue'] = array('manage','delete','process');
		$aclcats['statistics'] = array('manage','delete');
		$aclcats['cpanel'] = array('manage');
		$aclcats['configuration'] = array('manage');
		foreach($aclcats as $category => $actions){ ?>
		<tr>
			<td width="185" class="key" valign="top">
				<?php $trans = JText::_('ACY_'.strtoupper($category));
				if($trans == 'ACY_'.strtoupper($category)) $trans = JText::_(strtoupper($category));
				echo $trans;
				?>
			</td>
			<td>
				<?php echo $acltable->display($category,$actions)?>
			</td>
		</tr>
		<?php } ?>
	</table>
</div>
