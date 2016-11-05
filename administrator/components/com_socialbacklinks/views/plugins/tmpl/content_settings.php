<?php
/**    
 * SocialBacklinks Plugins view Content Settings layout
 *
 * We developed this code with our hearts and passion.
 * We hope you found it useful, easy to understand and change.
 * Otherwise, please feel free to contact us at contact@joomunited.com
 *
 * @package 	Social Backlinks
 * @copyright 	Copyright (C) 2012 JoomUnited (http://www.joomunited.com). All rights reserved.
 * @license 	GNU General Public License version 2 or later; http://www.gnu.org/licenses/gpl-2.0.html
 */

defined( '_JEXEC' ) or die( );

foreach ( $this->plugins as $plugin ) : 
	$alias = $plugin->getAlias( );
	$options = @JArrayHelper::toObject( $plugin->getOptions() );

	?>
	<div id="<?php echo $alias ?>-config" class="content-wrapper block-wrapper">
		<div class="block-header">
			<div class="block-header-inner">
				<div class="block-header-sub">
					<div class="toggle-button"></div>
					<?php echo JText::_( 'SB_' . strtoupper( $alias ) . '_CONTENT_TITLE' ); ?>
				</div>
			</div>
		</div>
		<div id="block-<?php echo $alias; ?>" class="content-block">
			<div class="error-block"></div>
			<div class="block">
				<div class="text-block">
					<?php echo ( $options->selected_content ) ? JText::_( 'SB_SOME_ARTICLES_SELECTED' ) : JText::_( 'SB_ALL_ARTICLES_SELECTED' ); ?>
				</div>
				
				<a class="modal button select-button" rel="{handler:'iframe',size:{x:800,y:500}}"
					href="<?php echo JRoute::_( "index.php?option=com_socialbacklinks&view=plugin&task=selectItems&tmpl=component&content=$alias" ) ?>">
			 		<span id="content"><?php echo JText::_( 'Restrict' ) ?></span>
			 	</a>
			 	
			 	<div class="success-block"></div>
			</div>
			<?php if ( !empty( $options->sync_availability->sync_when_publish->enabled ) ) { ?>
				<div class="block">
					<?php
						$class = ( $options->sync_published ) ? 'on-button' : 'off-button';
						$param = array( 
							'view' => 'plugin', 'task' => 'save', 
							'plugin' => $alias, 'name' => 'sync_published' 
						);
						$js_param = json_encode( $param );
						$js_param = str_replace( array( '{', '}', '"' ), array( '', '', "'" ), $js_param );
					?>
					<div class="on-off-wrapper">
						<div class="on-off-cell">
							<div id="sync_published" class="config-on-off <?php echo $class ?>">
								<span style="display: none"><?php echo $js_param ?></span>
							</div>
						</div>
					</div>
					<div class="text-block"><?php echo JText::_( 'SB_' . strtoupper( $alias ) . '_CONTENT_SYNC_PUBLISHED' ) ?></div>
					
					<div class="success-block"></div>
					<div class="ajax-loader"></div>
					<div class="ajax-overlay"></div>
				</div>
			<?php } ?>
			<?php if (!empty( $options->sync_availability->sync_when_update->enabled )) {?>
				<div class="block">
					<?php
					$class = ( $options->sync_updated ) ? 'on-button' : 'off-button';
					$param = array( 
						'view' => 'plugin', 'task' => 'save', 
						'plugin' => $alias,  'name' => 'sync_updated' 
					);
					$js_param = json_encode( $param );
					$js_param = str_replace( array( '{', '}', '"' ), array( '', '', "'" ), $js_param );
					?>
					<div class="on-off-wrapper">
						<div class="on-off-cell">
							<div id="sync_updated" class="config-on-off <?php echo $class ?>">
								<span style="display: none"><?php echo $js_param ?></span>
							</div>
						</div>
					</div>
					<div class="text-block"><?php echo JText::_( 'SB_' . strtoupper( $alias ) . '_CONTENT_SYNC_UPDATED' ) ?></div>
					
					<div class="success-block"></div>
					<div class="ajax-loader"></div>
					<div class="ajax-overlay"></div>
				</div>
			<?php }?>
		</div>
	</div>
<?php endforeach; ?>
