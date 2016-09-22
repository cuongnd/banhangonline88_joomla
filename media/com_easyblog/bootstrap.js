<?php
/**
 * @package     EasyBlog
 * @copyright   Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
 *
 * EasyBlog is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access');
?>
/*<![CDATA[*/
dispatch("Foundry/2.1").to(function($, manifest) {

	$.Component(
		"EasyBlog",
		{
			/* Overwrite the component's URL */
			baseUrl: '<?php echo $url; ?>',

			environment: '<?php echo $easyblogEnvironment; ?>',

			version: '<?php echo EasyBlogHelper::getLocalVersion() ?>',

			responsive: <?php echo EasyBlogHelper::getConfig()->get( 'responsive' ) ? 'true' : 'false'; ?>,

			scriptVersioning: <?php echo EasyBlogHelper::getConfig()->get( 'main_script_versioning' ) ? 'true' : 'false';?>,

			optimizeResources: true,

			dependencies: function(require) {

				require
					.library(
						'ui/core',
						'ui/widget',
						'ui/position',
						'ui/stars',
						'fancybox',
						'bookmarklet',
						'checked',
						'checkList'
					)
					.script(
						'eblog',
						'ejax'
					);
			},

			ajax: {
				data: {
					"<?php echo EasyBlogHelper::getHelper( 'Token' )->get(); ?>" : 1
				}
			}
		},
		function() {

			EasyBlog.responsive	= <?php echo EasyBlogHelper::getConfig()->get( 'layout_responsive' ) ? 'true' : 'false'; ?>;

			if( EasyBlog.environment == 'development' )
			{
				try {
					console.info("EasyBlog client-side component is now ready!");
				} catch(e) {

				}
			}
		}
	);

});

/*]]>*/
