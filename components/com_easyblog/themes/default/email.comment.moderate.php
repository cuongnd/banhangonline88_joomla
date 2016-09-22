<?php
/**
 * @package		EasyBlog
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *  
 * EasyBlog is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');
?>

Hi {target},<br />
<br />
<br />
New comment has been added to the blog '<?php echo $blogTitle; ?>' and currently is under moderation and required your attention.
Below is the comment snippet that has been created.<br />
<br />
From:<br />
<?php echo $commentPoster; ?><br />
<br />
<br />
Title:<br />
<?php echo $commentTitle; ?><br />
<br />
<br />
Comment:<br />
<?php echo $comment; ?><br />
<br />
<br />
<br />
<?php echo $commentModeration; ?><br />
<br />
<br />
Have a nice day!