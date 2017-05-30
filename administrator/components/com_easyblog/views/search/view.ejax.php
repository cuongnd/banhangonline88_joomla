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

require_once( EBLOG_ADMIN_ROOT . DIRECTORY_SEPARATOR . 'views.php' );
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'string.php' );
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'date.php' );
require_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'ejax.php' );

class EasyBlogViewSearch extends EasyBlogAdminView
{
	public function search( $query = '' , $elementId ='' )
	{
		$ajax	= new Ejax();

	    $lang		= JFactory::getLanguage();
	    $lang->load( 'com_easyblog' , JPATH_ROOT );

		$model 	= EasyBlogHelper::getModel( 'Search' );
		$posts	= $model->searchText( $query );

		if( empty( $elementId ) )
			$elementId  = 'write_content';

		if( empty($posts) )
		{
			$ajax->script( '$("#editor-'.$elementId.' .search-results-content").height(24);' );
			$ajax->assign( 'editor-' . $elementId . ' .search-results-content', JText::_( 'No results found' ) );
			return $ajax->send();
		}

		$count = count($posts);

		if($count > 10)
		{
			$height = "240";
		}
		else
		{
			$height = "24" * $count;
		}
		$config = EasyBlogHelper::getConfig();

		ob_start();
?>
<ul class="blog-search-items reset-ul">
	<?php foreach( $posts as $entry )
	{
		$postLink		= EasyBlogRouter::_( 'index.php?option=com_easyblog&view=entry&id=' . $entry->id );
		$externalLink	= EasyBlogRouter::getRoutedURL( 'index.php?option=com_easyblog&view=entry&id=' . $entry->id , false , true );
	?>
	<li>
        <input type="button" onclick="eblog.editor.search.insert('<?php echo $externalLink; ?>', '<?php echo $entry->title; ?>', '<?php echo $elementId ?>');return false;" value="<?php echo JText::_('COM_EASYBLOG_DASHBOARD_EDITOR_INSERT_LINK'); ?>" class="ui-button float-r mts" />
        <div class="tablecell">
            <a href="<?php echo $externalLink; ?>" target="_BLANK"><?php echo $entry->title; ?></a>
            <?php echo JText::_( 'COM_EASYBLOG_ON' );?>
            <?php echo $this->formatDate( $config->get('layout_dateformat') , $entry->created ); ?>
        </div>
        <div class="clear"></div>
	</li>
	<?php
	}
	?>
</ul>
<?php
		$html	= ob_get_contents();
		ob_end_clean();
		$ajax->assign( 'editor-content .search-results-content' , $html );
		$ajax->script( '$("#editor-content .search-results-content").height('.$height.');' );
		$ajax->script( '$("#editor-content .search-results-content").show();' );
		return $ajax->send();
	}

	function formatDate( $format , $dateString )
	{
		$date	= EasyBlogDateHelper::dateWithOffSet($dateString);
		return EasyBlogDateHelper::toFormat($date, $format);
	}
}
