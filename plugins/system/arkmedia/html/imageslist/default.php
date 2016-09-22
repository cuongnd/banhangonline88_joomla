<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$document = JFactory::getDocument();
$document->addScriptDeclaration("var MediaManager = window.parent.MediaManager;");

$this->documents = $this->get('documents');

function setDoc($index, $ref)
{
	if (isset($ref->documents[$index]))
	{
		$ref->_tmp_doc = $ref->documents[$index];
	}
	else
	{
		$ref->_tmp_doc = new JObject;
	}
}

$documentsCount = count($this->documents);
$foldersCount = count($this->folders);
$imageCount = count($this->images);

?>
<?php if ($documentsCount > 0 || $foldersCount > 0 || $imageCount > 0) : ?>
<ul class="manager thumbnails">
	<?php for ($i = 0, $n = count($this->folders); $i < $n; $i++) :
		$this->setFolder($i,$this);
		echo $this->loadTemplate('folder');
	endfor; ?>
	<?php for ($i = 0, $n = count($this->documents); $i < $n; $i++) :
			setDoc($i,$this);
			echo $this->loadTemplate('doc');
	endfor; ?>
	<?php for ($i = 0, $n = count($this->images); $i < $n; $i++) :
		$this->setImage($i);
		echo $this->loadTemplate('image');
	endfor; ?>
</ul>
<?php else : ?>
	<div id="media-noimages">
		<div class="alert alert-info"><?php echo JText::_('JNO').' '.JText::_('COM_MEDIA_FILES'); ?></div>
	</div>
<?php endif; ?>
