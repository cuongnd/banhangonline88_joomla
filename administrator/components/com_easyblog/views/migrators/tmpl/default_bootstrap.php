<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<ul class="nav nav-tabs">
	<li class="active">
		<a href="#articles" data-toggle="tab">Joomla! Articles</a>
	</li>

	<li>
		<a href="#smartblog" data-toggle="tab">SmartBlog</a>
	</li>

	<li>
		<a href="#lyften" data-toggle="tab">LyftenBloggie</a>
	</li>

	<li>
		<a href="#myblog" data-toggle="tab">MyBlog</a>
	</li>

	<li>
		<a href="#wpforjoomla" data-toggle="tab">WordPress for Joomla!</a>
	</li>

	<li>
		<a href="#wordpressimport" data-toggle="tab">WordPress XML Import</a>
	</li>

	<li>
		<a href="#k2" data-toggle="tab">K2</a>
	</li>

	<li>
		<a href="#blogger" data-toggle="tab">Blogger XML Import</a>
	</li>

</ul>

<div class="tab-content">

	<div class="tab-pane active" id="articles">
		<?php echo $this->loadTemplate( 'articles' ); ?>
	</div>

	<div class="tab-pane" id="smartblog">
		<?php echo $this->loadTemplate( 'smartblog' ); ?>
	</div>

	<div class="tab-pane" id="lyften">
		<?php echo $this->loadTemplate( 'lyften' ); ?>
	</div>

	<div class="tab-pane" id="myblog">
		<?php echo $this->loadTemplate( 'myblog' ); ?>
	</div>

	<div class="tab-pane" id="wpforjoomla">
		<?php echo $this->loadTemplate( 'wordpress' ); ?>
	</div>

	<div class="tab-pane" id="wordpressimport">
		<?php echo $this->loadTemplate( 'wordpressimport' ); ?>
	</div>

	<div class="tab-pane" id="k2">
		<?php echo $this->loadTemplate( 'k2' ); ?>
	</div>

	<div class="tab-pane" id="blogger">
		<?php echo $this->loadTemplate( 'bloggerimport' ); ?>
	</div>

</div>
