<?php
/**
* @version 1.4.0
* @package RSform!Pro 1.4.0
* @copyright (C) 2007-2013 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

class RSFormProScripting
{
	public static function compile(&$subject, $replace, $with) {
		$placeholders = array_combine($replace, $with);
		
		$condition 	= '{[a-z0-9\_\- ]+:[a-z_]+}';
		$inner 		= '((?:(?!{/?if).)*)';
		$pattern 	= '#{if ('.$condition.')}'.$inner.'{/if}#is';
		
		while (preg_match($pattern, $subject, $match)) {
			$placeholder = $match[1];
			$content 	 = $match[2];

			// if empty value remove whole line
			// else show line but remove pseudo-code
			$subject = preg_replace($pattern,
									empty($placeholders[$placeholder]) ? '' : $content,
									$subject,
									1);
		}
	}
}