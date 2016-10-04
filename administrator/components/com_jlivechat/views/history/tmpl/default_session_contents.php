<?php 
/**
 * @package JLive! Chat
 * @version 4.3.3
 * @copyright (C) Copyright 2008-2010 CMS Fruit, CMSFruit.com. All rights reserved.
 * @license GNU/LGPL http://www.gnu.org/licenses/lgpl-3.0.txt

 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU Lesser General Public License as published by
 the Free Software Foundation; either version 3 of the License, or (at your
 option) any later version.

 This program is distributed in the hope that it will be useful, but
 WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU Lesser General Public
 License for more details.

 You should have received a copy of the GNU Lesser General Public License
 along with this program.  If not, see http://www.gnu.org/licenses/.
 */

defined('_JEXEC') or die('Restricted access');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language_code; ?>" lang="<?php echo $this->language_code; ?>" dir="ltr">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    
    <link rel="stylesheet" href="../components/com_jlivechat/assets/css/jlivechat.css" type="text/css" />
    <link rel="stylesheet" href="../components/com_jlivechat/assets/css/popup.css" type="text/css" />
    <style>
	body {
	    padding: 10px !important;
	}
    </style>
</head>
<body>
<?php echo $this->content; ?>
</body>
</html>