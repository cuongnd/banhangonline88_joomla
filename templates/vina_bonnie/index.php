<?phpJHTML::_("jquery.ui");JHTML::_("bootstrap.framework");$doc = JFactory::getDocument();JHTML::_("jQuery.jquery_load_file");jimport('joomla.html.htmlrender');//$doc->addLessStyleSheet(JUri::root() . 'templates/vina_bonnie/less/custom.less');//$doc->addScript(JUri::root() . 'media/jui/bootstrap-3.3.7/js/tooltip.js');//$doc->addScript(JUri::root() . 'media/jui/bootstrap-3.3.7/js/popover.js');JHTML::_("jquery.template");$document=JFactory::getDocument();require_once JPATH_ROOT.DS.'libraries/less.php_1.7.0.10/less.php/Less.php';$parser = Less_Parser::getInstance();$parser->parseFile(JPATH_ROOT.DS.'templates/vina_bonnie/bootstrap-3.3.7/less/bootstrap.less', JUri::root());$css = $parser->getCss();JFile::write(JPATH_ROOT.DS.'templates/vina_bonnie/bootstrap-3.3.7/css/bootstrap.css', $css);$document->addStyleSheet(JUri::root() . 'templates/vina_bonnie/bootstrap-3.3.7/css/bootstrap.css');$reflector = new ReflectionClass(get_class($less));$app = JFactory::getApplication();$js_content = '';$doc = JFactory::getDocument();ob_start();?><script type="text/javascript"></script><?php$js_content = ob_get_clean();$js_content = JUtility::remove_string_javascript($js_content);$doc->addScriptDeclaration($js_content);$menu = JFactory::getApplication()->getMenu();$active_menu = $menu->getActive();if (!$active_menu) {    $active_menu = $menu->getDefault();}$background_image = $active_menu->params->get('background_image', '');if ($background_image){$style_content = '';$doc = JFactory::getDocument();ob_start();?><style type="text/css">    body {        background: url("<?php echo JUri::root().$background_image ?>");    }</style><?php$style_content = ob_get_clean();$style_content = JUtility::remove_string_style_sheet($style_content);$doc->addStyleDeclaration($style_content);}/** * @package Helix Framework * Template Name - Shaper Helix * @author JoomShaper http://www.joomshaper.com * @copyright Copyright (c) 2010 - 2013 JoomShaper * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later *///no direct acceesdefined('_JEXEC') or die ('resticted aceess');?><!DOCTYPE html><!--[if lt IE 7]><html class=" lt-ie9 lt-ie8 lt-ie7" lang="<?php echo $this->language; ?>"> <![endif]--><!--[if IE 7]><html class=" lt-ie9 lt-ie8" lang="<?php echo $this->language; ?>"> <![endif]--><!--[if IE 8]><html class=" lt-ie9" lang="<?php echo $this->language; ?>"> <![endif]--><!--[if gt IE 8]><!--><html class="" lang="<?php echo $this->language; ?>"> <!--<![endif]--><head>    <meta name="viewport" content="width=device-width, initial-scale=1">    <!--<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">-->    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">    <jdoc:include type="head"/>    <?php    $style = array('gallery.css');    $script = array('jquery.isotope.min.js', 'jquery.touchSwipe.min.js', 'bootstrap-select.min.js', 'template.js');    if ($this->helix->Param('scroll_effect')) {        $script[] = 'wow.js';        $style[] = 'animate.css';    }    ?></head><body ><div <?php echo $this->helix->bodyClass('bg hfeed clearfix'); ?>>    <div>        <div class="body-innerwrapper">            <!--[if lt IE 8]>            <div class="chromeframe alert alert-danger" style="text-align:center">You are using an                <strong>outdated</strong> browser. Please <a target="_blank" href="http://browsehappy.com/">upgrade your                    browser</a> or <a target="_blank" href="http://www.google.com/chromeframe/?redirect=true">activate                    Google Chrome Frame</a> to improve your experience.            </div>            <![endif]-->            <?php            $this->helix->layout();            $this->helix->footer();            ?>            <jdoc:include type="modules" name="debug"/>            <div id="overlay_body"></div>        </div>    </div></div><?phpecho Jhtmlrender::alo_phone('alo_phone');echo Jhtmlrender::select_device('select_device');?></body></html>