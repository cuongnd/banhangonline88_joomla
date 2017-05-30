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

/*
 * Comment utilities class.
 *
 */
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'helper.php' );

class EasyBlogCommentHelper
{
	public $pagination = null;

	/**
	 * Format comments data
	 **/
	public static function format( $comments )
	{
		for($i = 0; $i < count($comments); $i++)
		{
			$row			=& $comments[$i];
			$user			= EasyBlogHelper::getTable( 'Profile', 'Table' );
			$row->poster	= $user->load( $row->created_by );
			$row->poster->comment = $row;
			$row->comment	= nl2br($row->comment);
		}

		return $comments;
	}

	/*
	 * Determines whether the current comment system used is built in or an external tool
	 *
	 * @param	null
	 * @return	boolean	True if built in false otherwise.
	 */
	public static function isBuiltin()
	{
		jimport( 'joomla.filesystem.file' );

		$config		= EasyBlogHelper::getConfig();

		if( !$config->get( 'main_comment' ) )
		{
			return false;
		}

		// @rule: If the default comments and multiple comments are enabled, we assume that it is built in.
		if( $config->get( 'main_comment' ) && $config->get( 'main_comment_multiple' ) )
		{
			return true;
		}

		if( $config->get( 'intensedebate' ) )
		{
			return false;
		}

		if( $config->get( 'comment_disqus' ) )
		{
			return false;
		}

		$file	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_jomcomment' . DIRECTORY_SEPARATOR . 'jomcomment.php';

		if( $config->get( 'comment_jomcomment' ) && JFile::exists( $file ) )
		{
			return false;
		}

		$file	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_jcomments' . DIRECTORY_SEPARATOR . 'jcomments.php';

		if( $config->get( 'comment_jcomments' ) && JFile::exists( $file ) )
		{
			return false;
		}

		$file	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_rscomments' . DIRECTORY_SEPARATOR . 'rscomments.php';

		if( $config->get( 'comment_rscomments' ) && JFile::exists( $file ) )
		{
			return false;
		}

		if( $config->get( 'comment_facebook' ) )
		{
			return false;
		}

		return true;
	}

	/**
	 * Retrieves the comment count for the specific blog
	 *
	 * @param	int	$blogId	The blog id.
	 **/
	public static function getCommentCount( $blog )
	{
		$blogId		= $blog->id;
		$config		= EasyBlogHelper::getConfig();

		// If multiple comments, we output a common link
		if( $config->get( 'main_comment_multiple' ) )
		{
			return false;
		}

		if( $config->get( 'comment_komento' ) )
		{
			$file = JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_komento' . DIRECTORY_SEPARATOR . 'bootstrap.php';
			if( JFile::exists($file) )
			{
				require_once($file);
				$commentsModel	= Komento::getModel( 'comments' );
				$commentCount = $commentsModel->getCount( 'com_easyblog', $blog->id );
				return $commentCount;
			}
		}

		$easysocial 	= EasyBlogHelper::getHelper( 'EasySocial' );

		if( $config->get( 'comment_easysocial' ) && $easysocial->exists() )
		{
			return $easysocial->getCommentCount( $blog );
		}

		if( $config->get( 'comment_compojoom' ) )
		{
			$file	= JPATH_ROOT . '/administrator/components/com_comment/plugin/com_easyblog/josc_com_easyblog.php';

			if( JFile::exists( $file ) )
			{
				require_once( $file );
				return CommentEasyBlog::output( $blog , array() , true );
			}

			$file = JPATH_ROOT . '/components/com_comment/helpers/utils.php';
			if( JFile::exists( $file ) )
			{
		        JLoader::discover('ccommentHelper', JPATH_ROOT . '/components/com_comment/helpers');
		        $commentPlugin = (ccommentHelperUtils::getPlugin('com_easyblog', $blog));
		        
		        return (ccommentHelperUtils::getCommentCount($commentPlugin, 'com_easyblog'));
			}
		}

		if( $config->get( 'intensedebate' ) )
		{
			return false;
		}

		if( $config->get( 'comment_disqus' ) )
		{
			static $disqus = false;

			if( !$disqus )
			{
				ob_start();
				?>
					var disqus_shortname = '<?php echo $config->get( 'comment_disqus_code' );?>';
					(function () {
					var s = document.createElement('script'); s.async = true;
					s.type = 'text/javascript';
					s.src = 'http://' + disqus_shortname + '.disqus.com/count.js';
					(document.getElementsByTagName('HEAD')[0] || document.getElementsByTagName('BODY')[0]).appendChild(s);
					}());
				<?php
				$contents 	= ob_get_contents();
				ob_end_clean();

				JFactory::getDocument()->addScriptDeclaration( $contents );

				$disqus 	= true;
			}

			$string = '<!-- Disqus -->';
			$string .= '<span class="discus-comment">';
			$string .= '<a href="' . EasyBlogRouter::_('index.php?option=com_easyblog&view=entry&id='.$blogId) . '#disqus_thread"><span>'.JText::_('COM_EASYBLOG_COMMENTS').'</span></a>';
			$string .= '</span>';

			return $string;
			// return false;
		}

		if( $config->get( 'comment_livefyre') )
		{
			return false;
		}

		if( $config->get( 'comment_jomcomment' ) )
		{
			$file 	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_jomcomment' . DIRECTORY_SEPARATOR . 'helper' . DIRECTORY_SEPARATOR . 'minimal.helper.php';

			jimport( 'joomla.filesystem.file' );

			if( !JFile::exists( $file ) )
			{
				return false;
			}

			require_once( $file );

			return jcCountComment( $blogId , 'com_easyblog' );
		}

		$file	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_jcomments' . DIRECTORY_SEPARATOR . 'jcomments.php';

		if( $config->get( 'comment_jcomments' ) && JFile::exists( $file ) )
		{
			$db 	= EasyBlogHelper::db();
			$query 	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__jcomments' ) . ' '
					. 'WHERE ' . $db->nameQuote( 'object_id' ) . '=' . $db->Quote( $blogId ) . ' '
					. 'AND ' . $db->nameQuote( 'object_group' ) . '=' . $db->Quote( 'com_easyblog' ) . ' '
					. 'AND ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( 1 );
			$db->setQuery( $query );

			$total 	= $db->loadResult();

			return $total;
		}

		if( $config->get( 'comment_rscomments' ) )
		{
			return false;
		}

		if( $config->get( 'comment_facebook' ) )
		{
			return false;
		}

		// @task: Let's allow the plugin to also trigger the comment count.
		$params	= EasyBlogHelper::getRegistry();
		$result	= EasyBlogHelper::triggerEvent( 'easyblog.commentCount' , $blog , $params , 0 );
		$count	= trim( implode( " " , $result ) );

		if( !empty( $count ) )
		{
			return $count;
		}

		$db		= EasyBlogHelper::db();
		$query	= 'SELECT COUNT(1) FROM '
				. $db->nameQuote( '#__easyblog_comment' )
				. ' WHERE ' . $db->nameQuote( 'post_id' ) . '=' . $db->Quote( $blogId )
				. ' AND `published` = ' . $db->Quote('1');

		$db->setQuery( $query );

		$count	= $db->loadResult();

		return $count;
	}

	public static function getBlogCommentLite(  $blogId, $limistFrontEnd = 0, $sort = 'asc')
	{
		return EasyBlogCommentHelper::getBlogComment($blogId, $limistFrontEnd, $sort, true);
	}

	public function getBlogComment($id, $limistFrontEnd = 0, $sort = 'asc', $isLite = false)
	{
		$config = EasyBlogHelper::getConfig();
		$comments = array();

		require_once( EBLOG_MODELS . DIRECTORY_SEPARATOR . 'blog.php' );
		$model = new EasyBlogModelBlog();
		$comments = $model->getBlogComment($id, $limistFrontEnd , $sort, $isLite);
		$this->pagination = $model->getPagination();

		return $comments;
	}

	public static function getCommentHTML( $blog , $comments = array() , $pagination = '' )
	{
		$config			= EasyBlogHelper::getConfig();
		$path			= EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'comments';
		$registration	= $config->get( 'comment_registeroncomment' );

		$commentSystems	= array();


		// Double check this with Joomla's registration component
		if( $registration )
		{
			$params			= JComponentHelper::getParams( 'com_users' );
			$registration	= $params->get( 'allowUserRegistration' ) == '0' ? false : $registration;
		}

		if( $config->get( 'comment_facebook' ) )
		{
			require_once( $path . DIRECTORY_SEPARATOR . 'facebook.php' );
			$commentSystems[ 'FACEBOOK' ] = EasyBlogCommentFacebook::getHTML( $blog );

			if( !$config->get( 'main_comment_multiple' ) )
			{
				return $commentSystems[ 'FACEBOOK' ];
			}
		}

		$easysocial 	= EasyBlogHelper::getHelper( 'EasySocial' );

		if( $config->get( 'comment_easysocial' ) && $easysocial->exists() )
		{
			$easysocial->init();

			$commentSystems[ 'EASYSOCIAL' ]	= $easysocial->getCommentHTML( $blog );

			if( !$config->get( 'main_comment_multiple' ) )
			{
				return $commentSystems[ 'EASYSOCIAL' ];
			}
		}

		if( $config->get( 'comment_compojoom' ) )
		{
			$file	= JPATH_ROOT . '/administrator/components/com_comment/plugin/com_easyblog/josc_com_easyblog.php';

			if( JFile::exists( $file ) )
			{
				require_once( $file );
				$commentSystems[ 'COMPOJOOM' ] = CommentEasyBlog::output( $blog , array() );
			}

			$file = JPATH_ROOT . '/components/com_comment/helpers/utils.php';

			if( JFile::exists( $file ) )
			{
				JLoader::discover('ccommentHelper', JPATH_ROOT . '/components/com_comment/helpers');

				$commentSystems[ 'COMPOJOOM'] = ccommentHelperUtils::commentInit('com_easyblog', $blog);
			}

			if( !$config->get( 'main_comment_multiple' ) )
			{
				return $commentSystems[ 'COMPOJOOM' ];
			}
		}

		if( $config->get('comment_intensedebate') )
		{
			require_once( $path . DIRECTORY_SEPARATOR . 'intensedebate.php' );

			$commentSystems[ 'INTENSEDEBATE' ] = EasyBlogCommentIntenseDebate::getHTML( $blog );

			if( !$config->get( 'main_comment_multiple' ) )
			{
				return $commentSystems[ 'INTENSEDEBATE' ];
			}
		}

		if( $config->get('comment_disqus') )
		{
			require_once( $path . DIRECTORY_SEPARATOR . 'disqus.php' );

			$commentSystems[ 'DISQUS' ] = EasyBlogCommentDisqus::getHTML( $blog );

			if( !$config->get( 'main_comment_multiple' ) )
			{
				return $commentSystems[ 'DISQUS' ];
			}
		}

		if( $config->get('comment_jomcomment') )
		{
			$file	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_jomcomment' . DIRECTORY_SEPARATOR . 'jomcomment.php';

			// Test if jomcomment exists.
			if( JFile::exists( $file ) )
			{
				require_once( $path . DIRECTORY_SEPARATOR . 'jomcomment.php' );

				$commentSystems[ 'JOMCOMMENT' ] = EasyBlogCommentJomComment::getHTML( $blog );

				if( !$config->get( 'main_comment_multiple' ) )
				{
					return $commentSystems[ 'JOMCOMMENT' ];
				}
			}
		}

		if( $config->get('comment_livefyre') )
		{
			require_once( $path . DIRECTORY_SEPARATOR . 'livefyre.php' );

			$commentSystems[ 'LIVEFYRE' ] = EasyBlogCommentLiveFyre::getHTML( $blog );

			if( !$config->get( 'main_comment_multiple' ) )
			{
				return $commentSystems[ 'LIVEFYRE' ];
			}
		}

		if( $config->get('comment_jcomments' ) )
		{
			$file	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_jcomments' . DIRECTORY_SEPARATOR . 'jcomments.php';

			if( JFile::exists( $file ) )
			{
				require_once( $path . DIRECTORY_SEPARATOR . 'jcomments.php' );

				$commentSystems[ 'JCOMMENTS' ] = EasyBlogCommentJComments::getHTML( $blog );

				if( !$config->get( 'main_comment_multiple' ) )
				{
					return $commentSystems[ 'JCOMMENTS' ];
				}
			}
		}

		if($config->get('comment_rscomments') )
		{
			$file	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_rscomments' . DIRECTORY_SEPARATOR . 'rscomments.php';

			if( JFile::exists( $file ) )
			{
				include_once( $path . DIRECTORY_SEPARATOR . 'rscomments.php' );
				$commentSystems[ 'RSCOMMENTS' ] = EasyBlogCommentRSComments::getHTML( $blog );

				if( !$config->get( 'main_comment_multiple' ) )
				{
					return $commentSystems[ 'RSCOMMENTS' ];
				}
			}
		}

		if( $config->get( 'comment_easydiscuss' ) )
		{
			$enabled	= JPluginHelper::isEnabled( 'content' , 'easydiscuss' );

			if( $enabled )
			{
				JPluginHelper::importPlugin( 'content' , 'easydiscuss' );

				$articleParams	= new stdClass();
				$result 		= JFactory::getApplication()->triggerEvent( 'onDisplayComments' , array( &$blog , &$articleParams ) );

				if( isset( $result[ 0 ] ) || isset( $result[ 1 ] ) )
				{
					// There could be komento running on the site
					if( isset( $result[ 1 ] ) && $result[ 1 ] )
					{
						$commentSystems['EASYDISCUSS']		= $result[ 1 ];	
					}
					else
					{
						$commentSystems['EASYDISCUSS']		= $result[ 0 ];
					}

					if( !$config->get( 'main_comment_multiple') )
					{
						return $commentSystems[ 'EASYDISCUSS' ];
					}
				}
			}
		}

		if( $config->get( 'comment_komento' ) )
		{
			$file	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_komento' . DIRECTORY_SEPARATOR . 'bootstrap.php';

			if( JFile::exists( $file ) )
			{
				include_once( $file );
				$commentSystems[ 'KOMENTO' ] = Komento::commentify( 'com_easyblog', $blog, array('trigger'=>'onDisplayComments') );

				if( !$config->get( 'main_comment_multiple' ) )
				{
					return $commentSystems[ 'KOMENTO' ];
				}
			}
		}

		if( !$config->get( 'main_comment_multiple') || $config->get( 'comment_easyblog' ) )
		{
			//check if bbcode enabled or not.
			if($config->get('comment_bbcode'))
			{
				EasyBlogCommentHelper::loadBBCode();
			}

			// If all else fail, try to use the default comment system
			$theme		= new CodeThemes();

			// setup my own info to show in comment form area
			$my			= JFactory::getUser();
			$profile	= EasyBlogHelper::getTable( 'Profile', 'Table' );
			$profile->load( $my->id );

			$my->avatar 		= $profile->getAvatar();
			$my->displayName    = $profile->getName();
			$my->url			= $profile->url;
			$blogURL			= base64_encode( EasyBlogRouter::_('index.php?option=com_easyblog&view=entry&id=' . $blog->id , false ) );
			$loginURL			= EasyBlogHelper::getLoginLink( $blogURL );
			$enableRecaptcha	= $config->get('comment_recaptcha');
			$publicKey			= $config->get('comment_recaptcha_public');

			// check if the user has subcribed to this thread
			$subscriptionId		= false;
			if ($my->id > 0)
			{
				$blogModel		= EasyblogHelper::getModel('Blog');
				$subscriptionId	= $blogModel->isBlogSubscribedUser( $blog->id , $my->id , $my->email );
				$subscriptionId	= is_null($subscriptionId) ? false : $subscriptionId;
			}

			$theme->set('loginURL' 		, $loginURL );
			$theme->set('blog'			, $blog );
			$theme->set('my'			, $my );
			$theme->set('config'		, $config );
			$theme->set('blogComments'	, $comments );
			$theme->set('pagination'	, $pagination );
			$theme->set('allowComment'	, true );
			$theme->set('canRegister'	, $registration );
			$theme->set('acl'			, EasyBlogACLHelper::getRuleSet() );
			$theme->set('subscriptionId'	, $subscriptionId);

			$commentSystems[ 'EASYBLOGCOMMENTS' ]	= $theme->fetch( 'blog.comment.box.php' );
		}


		if( !$config->get( 'main_comment_multiple' ) )
		{
			return $commentSystems[ 'EASYBLOGCOMMENTS' ];
		}

		// If there's 1 system only, there's no point loading the tabs.
		if( count( $commentSystems ) == 1 )
		{
			return $commentSystems[ key( $commentSystems ) ];
		}

		unset( $theme );

		// Reverse the comment systems array so that easyblog comments are always the first item.
		$commentSystems	= array_reverse( $commentSystems );

		$theme 	= new CodeThemes();
		$theme->set( 'commentSystems' , $commentSystems );
		return $theme->fetch( 'blog.comment.multiple.php' );
	}

	public static function loadBBCode()
	{
		$document	= JFactory::getDocument();
		$out	= '<link rel="stylesheet" type="text/css" href="'.rtrim(JURI::root(), '/').'/components/com_easyblog/classes/markitup/skins/simple/style.css" />' . "\n";
		$out	.= '<link rel="stylesheet" type="text/css" href="'.rtrim(JURI::root(), '/').'/components/com_easyblog/classes/markitup/sets/bbcode/style.css" />' . "\n";

		$out	.= '<script type="text/javascript" src="'.rtrim(JURI::root(), '/').'/components/com_easyblog/classes/markitup/jquery.markitup.pack.js"></script>' . "\n";

		$bold		= JText::_( 'COM_EASYBLOG_BBCODE_BOLD' , true );
		$italic		= JText::_( 'COM_EASYBLOG_BBCODE_ITALIC' , true );
		$underline	= JText::_( 'COM_EASYBLOG_BBCODE_UNDERLINE' , true );
		$picture	= JText::_( 'COM_EASYBLOG_BBCODE_PICTURE' , true );
		$bullet		= JText::_( 'COM_EASYBLOG_BBCODE_BULLETS' , true );
		$numeric	= JText::_( 'COM_EASYBLOG_BBCODE_NUMERIC' , true );
		$list		= JText::_( 'COM_EASYBLOG_BBCODE_LIST' , true );
		$quote		= JText::_( 'COM_EASYBLOG_BBCODE_QUOTES' , true );
		$clean		= JText::_( 'COM_EASYBLOG_BBCODE_CLEAN' , true );
		$happy		= JText::_( 'COM_EASYBLOG_BBCODE_HAPPY' , true );
		$smile		= JText::_( 'COM_EASYBLOG_BBCODE_SMILE' , true );
		$surprised	= JText::_( 'COM_EASYBLOG_BBCODE_SURPRISED' , true );
		$tongue		= JText::_( 'COM_EASYBLOG_BBCODE_TONGUE' , true );
		$unhappy	= JText::_( 'COM_EASYBLOG_BBCODE_UNHAPPY' , true );
		$wink		= JText::_( 'COM_EASYBLOG_BBCODE_WINK' , true );

		$bbcode	=<<<EOF
EasyBlogBBCodeSettings = {

	previewParserVar: 'data',
	markupSet: [
		{name:'$bold', key:'B', openWith:'[b]', closeWith:'[/b]'},
		{name:'$italic', key:'I', openWith:'[i]', closeWith:'[/i]'},
		{name:'$underline', key:'U', openWith:'[u]', closeWith:'[/u]'},
		{separator:'---------------' },
		{name:'$picture', key:'P', replaceWith:'[img][![Url]!][/img]'},
		{separator:'---------------' },
		{name:'$bullet', openWith:'[list]\\n', closeWith:'\\n[/list]'},
		{name:'$numeric', openWith:'[list=[![Starting number]!]]\\n', closeWith:'\\n[/list]'},
		{name:'$list', openWith:'[*] '},
		{separator:'---------------' },
		{name:'$quote', openWith:'[quote]', closeWith:'[/quote]'},
		{name:'$clean', className:"clean", replaceWith:function(markitup) { return markitup.selection.replace(/\[(.*?)\]/g, "") } },
		{separator:'---------------' },
		{name:'$happy', openWith:':D'},
		{name:'$smile', openWith:':)'},
		{name:'$surprised', openWith:':o'},
		{name:'$tongue', openWith:':p'},
		{name:'$unhappy', openWith:':('},
		{name:'$wink', openWith:';)'}
	]
};
EOF;

		$out	.= '<script type="text/javascript">' . "\n";
		$out	.= $bbcode;
		$out	.= 'EasyBlog.require().library("markitup").done(function($) {' . "\n";
		$out	.= '	$("#comment").markItUp(EasyBlogBBCodeSettings);' . "\n";
		$out	.= '});' . "\n";
		$out	.= '</script>' . "\n";
		$document->addCustomTag($out);
	}

	public static function parseBBCode($text)
	{
		//$text	= htmlspecialchars($text , ENT_NOQUOTES );
		$text	= trim($text);
		//$text   = nl2br( $text );

		//$text = preg_replace_callback('/\[code\](.*?)\[\/code\]/ms', "escape", $text);
		$text = preg_replace_callback('/\[code( type="(.*?)")?\](.*?)\[\/code\]/ms', 'escape' , $text );

		// BBCode to find...
		$in = array( 	 '/\[b\](.*?)\[\/b\]/ms',
						 '/\[i\](.*?)\[\/i\]/ms',
						 '/\[u\](.*?)\[\/u\]/ms',
						 '/\[img\](.*?)\[\/img\]/ms',
						 '/\[email\](.*?)\[\/email\]/ms',
						 '/\[url\="?(.*?)"?\](.*?)\[\/url\]/ms',
						 '/\[size\="?(.*?)"?\](.*?)\[\/size\]/ms',
						 '/\[color\="?(.*?)"?\](.*?)\[\/color\]/ms',
						 '/\[quote](.*?)\[\/quote\]/ms',
						 '/\[list\=(.*?)\](.*?)\[\/list\]/ms',
						 '/\[list\](.*?)\[\/list\]/ms',
						 '/\[\*\]\s?(.*?)\n/ms'
		);
		// And replace them by...
		$out = array(	 '<strong>\1</strong>',
						 '<em>\1</em>',
						 '<u>\1</u>',
						 '<img src="\1" alt="\1" />',
						 '<a href="mailto:\1">\1</a>',
						 '<a href="\1">\2</a>',
						 '<span style="font-size:\1%">\2</span>',
						 '<span style="color:\1">\2</span>',
						 '<blockquote>\1</blockquote>',
						 '<ol start="\1">\2</ol>',
						 '<ul>\1</ul>',
						 '<li>\1</li>'
		);

		$tmp    = preg_replace( $in , '' , $text );

		$config = EasyBlogHelper::getConfig();

		if( $config->get( 'comment_autohyperlink' ) )
		{
			$text	= EasyBlogCommentHelper::replaceURL( $tmp, $text );
		}

		$text	= preg_replace($in, $out, $text);

		// Smileys to find...
		$in = array( 	 ':D',
						 ':)',
						 ':o',
						 ':p',
						 ':(',
						 ';)'
		);

		// And replace them by...
		$out = array(	
						'<img alt=":D" src="'.EBLOG_EMOTICONS_DIR.'emoticon-happy.png" />', 
						'<img alt=":)" src="'.EBLOG_EMOTICONS_DIR.'emoticon-smile.png" />',
						
						 '<img alt=":o" src="'.EBLOG_EMOTICONS_DIR.'emoticon-surprised.png" />',
						 '<img alt=":p" src="'.EBLOG_EMOTICONS_DIR.'emoticon-tongue.png" />',
						 '<img alt=":(" src="'.EBLOG_EMOTICONS_DIR.'emoticon-unhappy.png" />',
						 '<img alt=";)" src="'.EBLOG_EMOTICONS_DIR.'emoticon-wink.png" />'
		);
		$text = str_replace($in, $out, $text);

		// paragraphs
		$text = str_replace("\r", "", $text);
		$text = "<p>".preg_replace("/(\n){2,}/", "</p><p>", $text)."</p>";


		$text = preg_replace_callback('/<pre>(.*?)<\/pre>/ms', "removeBr", $text);
		$text = preg_replace('/<p><pre>(.*?)<\/pre><\/p>/ms', "<pre>\\1</pre>", $text);

		$text = preg_replace_callback('/<ul>(.*?)<\/ul>/ms', "removeBr", $text);
		$text = preg_replace('/<p><ul>(.*?)<\/ul><\/p>/ms', "<ul>\\1</ul>", $text);

		return $text;
	}

	public static function replaceURL( $tmp , $text )
	{
		$pattern = '@(?i)\b((?:https?://|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’]))@';

		preg_match_all( $pattern , $tmp , $matches );

		if( isset( $matches[ 0 ] ) && is_array( $matches[ 0 ] ) )
		{
			// to avoid infinite loop, unique the matches
			$uniques = array_unique($matches[ 0 ]);

			foreach( $uniques as $match )
			{
				$match	= str_ireplace( array( '<br' , '<br />' ) , '' , $match );
				$text	= str_ireplace( $match , '<a href="' . $match . '">' . $match . '</a>' , $text );
			}
		}

		$text	= str_ireplace( '&quot;' , '"', $text );
		return $text;
	}
}

// clean some tags to remain strict
// not very elegant, but it works. No time to do better ;)
if (!function_exists('removeBr')) {
	function removeBr($s) {
		return str_replace("<br />", "", $s[0]);
	}
}

// BBCode [code]
if (!function_exists('escape')) {
	function escape($s) {
		global $text;
		$text = strip_tags($text);
		$code = $s[1];
		$code = htmlspecialchars($code);
		$code = str_replace("[", "&#91;", $code);
		$code = str_replace("]", "&#93;", $code);
		return '<pre><code>'.$code.'</code></pre>';
	}
}
