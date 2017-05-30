<?php
/**
 * MaQma Helpdesk Component
 * www.imaqma.com
 *
 * @package   MaQma_Helpdesk
 * @copyright (C) 2006-2012 Components Lab, Lda.
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 *
 */

defined('_JEXEC') or die('Direct Access to this location is not allowed.');

/**
 * @package MaQma Helpdesk
 */
class reports_html
{
	static function show(&$rows, $lists)
	{ ?>
		<form action="index.php" method="post" id="adminForm" name="adminForm">
		    <div class="breadcrumbs">
		        <a href="index.php?option=com_maqmahelpdesk"><?php echo JText::_('control_panel'); ?></a>
		        <span><?php echo JText::_('REPORTS'); ?></span>
		        <span><?php echo JText::_('REPORTS_GEO'); ?></span>
		    </div>
		    <div id="filtersarea">
				<?php echo JString::strtoupper(JText::_('filters'));?> <img src="../media/com_maqmahelpdesk/images/ui/separator.png" style="padding:5px;" align="absmiddle"/>
		        <?php echo $lists['year'] . '&nbsp;' . $lists['month'] . '&nbsp;' . $lists['department']; ?>&nbsp;
		        <a href="javascript:;"
	               class="btn btn-success"
	               onclick="document.adminForm.submit();"><?php echo JText::_('filter');?></a>
		    </div>
			<div class="contentarea">
				<?php if(count($rows)):?>
                <div class="contentbar row-fluid">
					<div id="geomap" class="span6"></div>
					<div class="span6">
						<table class="table table-striped table-bordered" cellspacing="0">
							<thead>
	                            <tr>
	                                <th class="valgmdl"><?php echo JText::_("COUNTRY");?></th>
	                                <th class="algcnt valgmdl"><?php echo JText::_("TICKETS");?></th>
	                            </tr>
							</thead>
							<tbody>
								<?php for($i=0; $i<=(count($rows) < 20 ? (count($rows)-1) : 20); $i++):?>
								<tr>
									<td class="valgmdl"><?php echo $rows[$i]->countryname;?></td>
									<td class="algcnt valgmdl"><?php echo $rows[$i]->total;?></td>
								</tr>
								<?php endfor;?>
							</tbody>
						</table>
					</div>
				</div>
                <?php else:?>
				<div id="contentbox">
                    <div class="detailmsg">
                        <h1><?php echo JText::_('register_not_found'); ?></h1>
                    </div>
	            </div>
                <?php endif;?>
			</div>
			<?php echo JHtml::_('form.token'); ?>
            <input type="hidden" name="option" value="com_maqmahelpdesk"/>
            <input type="hidden" id="task" name="task" value="reports"/>
            <input type="hidden" id="report" name="report" value="geo"/>
		</form>

        <script type="text/javascript">
        google.load('visualization', '1', {packages: ['geomap']});

        function drawVisualization()
        {
            var data = google.visualization.arrayToDataTable([
                ['<?php echo JText::_("COUNTRY");?>', '<?php echo JText::_("TICKETS");?>'],
                <?php foreach($rows as $row):?>
	            ['<?php echo $row->countryname;?>', <?php echo $row->total;?>],
		        <?php endforeach;?>
            ]);

            var geomap = new google.visualization.GeoMap(document.getElementById('geomap'), {width:'100%'});
            geomap.draw(data, null);
        }

        google.setOnLoadCallback(drawVisualization);
		</script><?php
	}
}
