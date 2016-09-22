<?php
/**
 * @package Helix3 Framework
 * Template Name - Shaper Helix - iii
 * @author JoomShaper http://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2015 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
*/
//no direct accees
defined ('_JEXEC') or die ('resticted aceess');

$doc = JFactory::getDocument();
$params = JFactory::getApplication()->getTemplate('true')->params;

//Favicon
if($favicon = $params->get('favicon')) {
    $doc->addFavicon( JURI::base(true) . '/' .  $favicon);
} else {
    $doc->addFavicon( $this->baseurl . '/templates/' . $this->template . '/images/favicon.ico' );
}

//Stylesheets
$doc->addStylesheet( $this->baseurl . '/templates/' . $this->template . '/css/bootstrap.min.css' );
$doc->addStylesheet( $this->baseurl . '/templates/' . $this->template . '/css/font-awesome.min.css' );
$doc->addStylesheet( $this->baseurl . '/templates/' . $this->template . '/css/template.css' );

$doc->setTitle($this->error->getCode() . ' - '.$this->title);
require_once(JPATH_LIBRARIES.'/joomla/document/html/renderer/head.php');
$header_renderer = new JDocumentRendererHead($doc);
$header_contents = $header_renderer->render(null);

//Logo
jimport( 'joomla.filesystem.file' );
$logo_exist = JFile::exists(JPATH_ROOT . '/templates/' . $this->template . '/images/404-logo.png');

if($logo_exist) {
    $error_logo = $this->baseurl . '/templates/' . $this->template . '/images/404-logo.png';
} else {
    $error_logo = $this->baseurl . '/templates/' . $this->template . '/images/presets/preset1/logo@2x.png';
}

?>
<!DOCTYPE html>
<html class="error-page" xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
	<head>
	  	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	    <meta name="viewport" content="width=device-width, initial-scale=1">
		<?php echo $header_contents; ?>
	</head>
	<body>
		<div class="error-page-inner">
			<div>
				<div class="container">
					<div class="error-logo">
						<img src="<?php echo $error_logo; ?>" alt="404 error">
					</div>
					<p class="error-message"><?php echo $this->error->getMessage(); ?></p>
					<a class="btn btn-primary" href="<?php echo $this->baseurl; ?>/" title="<?php echo JText::_('HOME'); ?>"><i class="fa fa-arrow-left"></i> <?php echo JText::_('HELIX_GO_BACK'); ?></a>
					<?php echo $doc->getBuffer('modules', '404', array('style' => 'sp_xhtml')); ?>
				</div>
			</div>
		</div>
	</body>
</html>