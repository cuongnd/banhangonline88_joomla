<?php

class FSJ_Cron_Message {
	static function Display()
	{
		$output = array();

		$url = JURI::root() . 'index.php?option=com_fsj_main&view=cron';

		$output[] = "<div class='alert alert-info'>";
		$output[] = "<h4>" . JText::_('FSJ_CRON_INSTRUCTIONS_HEAD') . "</h4>";
		$output[] = JText::sprintf('FSJ_CRON_INSTRUCTIONS_BODY', $url, $url, $url);
		$output[] = "</div>";

		echo implode("\n", $output);
	}
}