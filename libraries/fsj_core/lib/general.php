<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

// stub general file to attempt to resolve any issues with old plugins and new library
// this is horribly inefficient, but should resolve most issues

// THIS FILE SHOULD ONLY EVER BE INCLUDED BY OLD COMPONENTS!

// SHOULD ISSUE A WARNING OF SOME SORT

?>
<div class="alert alert-danger">
	<h4>Freestyle Joomla Version mismatch!</h4>
	<p>Please update any of the following components if installed to the latest versions:</p>	
	<ul>
		<li>Freestyle Includes: Code</li>
		<li>Freestyle Includes: Data</li>
		<li>Freestyle Includes: Fragments</li>
		<li>Freestyle Notices</li>
		<li>Freestyle Translation Manager</li>
	</ul>
</div>
<?

jimport('fsj_core.lib.utils.general');
jimport('fsj_core.lib.utils.plugin_handler');
jimport('fsj_core.lib.utils.template');
jimport('fsj_core.lib.utils.database');
jimport('fsj_core.lib.utils.format');
jimport('fsj_core.lib.utils.xml');
