<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_footer
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
$logo_path=$params->get('logo');
?>
<a href="<?php echo JUri::root() ?>" ><img src="<?php echo JUri::root().$logo_path?>"></a>
