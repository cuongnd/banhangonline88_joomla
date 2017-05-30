<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

class FSJ_Plugin_Comments_Disqus
{
	var $custom_count = true;
	
	function getCommentCounts($item_set, $ids)
	{
		$shortname = $this->plugin->settings->plugin->shortname;
		
		if ($shortname == "")
			return array();

		$code = array();
		
		$code[] = "<script type='text/javascript'>";
		$code[] = "var disqus_shortname = '".$shortname."';";
		$code[] = "(function () {";
		$code[] = "var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;";
		$code[] = "dsq.src = '//' + disqus_shortname + '.disqus.com/count.js';";
		$code[] = "(document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);";
		$code[] = "}());";
		$code[] = "</script>";
		
		FSJ_Page::Footer(implode("\n", $code));

		return array();
	}
	
	function countDisplay($link, $title, $set, $id)
	{
		return "<a href='".$link."#disqus_thread' data-disqus-identifier='" . $set . "-" . $id . "'>Add a comment</a>";
	}
	
	function displayComments($id, $set, $title)
	{
		$shortname = $this->plugin->settings->plugin->shortname;
		
		if ($shortname == "")
			return "<div class='alert alert-error'>
						<h4>Please configure your Disqus site shortname to enable the disqus comments plugin.</h4>
						<p style='margin-top: 8px'>You can do this by going to '<b>Components</b>' -> '<b>Freestyle Joomla</b>' -> '<b>Plugin 
						Manager</b>'. Select the '<b>Comments provider plugins</b>' -> '<b>View Plugins (5)</b>' link to list all the comments 
						plugins. Then you can select the '<b>Disqus</b>' plugin, and enter your sites shortname in the '<b>Plugin Settings</b>' 
						tab. Dont forget to save the settings.</p>
					</div>";
		
		$code = array();
	
		$code[] = "<script type='text/javascript'>";
		$code[] = "var disqus_shortname = '".$shortname."';";
		$code[] = "var disqus_identifier = '" . $set . "-" . $id . "';";
		$code[] = "var disqus_title = '" . htmlspecialchars($title, ENT_QUOTES) . "';";
		$code[] = "(function () {";
		$code[] = "var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;";
		$code[] = "dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';";
		$code[] = "(document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);";
		$code[] = "}());";
		$code[] = "</script>";
		
		FSJ_Page::Footer(implode("\n", $code));
		
		return "<div id='disqus_thread'></div>";
	}
}