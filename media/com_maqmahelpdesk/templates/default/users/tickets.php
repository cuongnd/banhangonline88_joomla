<div class="maqmahelpdesk container-fluid">

	<h2><?php echo JText::_('pathway_user_tickets');?></h2>

    <ul id="tab" class="nav nav-tabs">
        <li class="active"><a href="#tab1"
                              data-toggle="tab"><?php echo '<img src="' . JURI::root() . 'media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/16px/comments.png" align="absmiddle" /> ' . JText::_('user_details'); ?></a>
        </li>
        <li><a href="#tab2"
               data-toggle="tab"><?php echo '<img src="' . JURI::root() . 'media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/16px/tickets.png" align="absmiddle" /> ' . JText::_('pathway_user_tickets');?></a>
        </li>
    </ul>

    <div id="my-tab-content" class="tab-content">
        <div id="tab1" class="tab-pane fade in active">
            <fieldset style="padding:5px;">
                <div>
                    <label><?php echo JText::_('name');?>:</label>
                    <b><?php echo $userInfo->name;?></b>
                </div>
                <div class="wrap">&nbsp;</div>

                <div>
                    <label><?php echo JText::_('username');?>:</label>
                    <b><?php echo $userInfo->username;?></b>
                </div>
                <div class="wrap">&nbsp;</div>

                <div>
                    <label><?php echo JText::_('email');?>:</label>
                    <b><?php echo $userInfo->email;?></b>
                </div>
                <div class="wrap">&nbsp;</div>

                <div>
                    <label><?php echo JText::_('phone');?>:</label>
                    <b><?php echo $userInfo->phone;?></b>
                </div>
                <div class="wrap">&nbsp;</div>

                <div>
                    <label><?php echo JText::_('fax');?>:</label>
                    <b><?php echo $userInfo->fax;?></b>
                </div>
                <div class="wrap">&nbsp;</div>

                <div>
                    <label><?php echo JText::_('mobile');?>:</label>
                    <b><?php echo $userInfo->mobile;?></b>
                </div>
                <div class="wrap">&nbsp;</div>

                <div>
                    <label><?php echo JText::_('address');?>:</label>
                    <b><?php echo $userInfo->address1;?></b>
                </div>
                <div class="wrap">&nbsp;</div>

                <div>
                    <label><?php echo JText::_('zipcode');?>:</label>
                    <b><?php echo $userInfo->zipcode;?></b>
                </div>
                <div class="wrap">&nbsp;</div>

                <div>
                    <label><?php echo JText::_('location');?>:</label>
                    <b><?php echo $userInfo->location;?></b>
                </div>
                <div class="wrap">&nbsp;</div>

                <div>
                    <label><?php echo JText::_('city');?>:</label>
                    <b><?php echo $userInfo->city;?></b>
                </div>
                <div class="wrap">&nbsp;</div>

                <div>
                    <label><?php echo JText::_('country');?>:</label>
                    <b><?php echo $userInfo->country;?></b>
                </div>
                <div class="wrap">&nbsp;</div>

                <?php echo $html_cfields;?>
            </fieldset>
        </div>
        <div id="tab2" class="tab-pane fade">
            <fieldset style="padding:5px;">
                <?php if (count($tickets)): ?>
                <table class="table table-striped table-bordered" cellspacing="0">
                    <thead>
                    <tr>
                        <th width="70" nowrap="nowrap"><?php echo JText::_('ticketid');?></th>
                        <th><?php echo JText::_('subject');?></th>
                        <th width="120" nowrap="nowrap"><?php echo JText::_('date_created');?></th>
                        <th nowrap="nowrap"><?php echo JText::_('status');?></th>
                    </tr>
                    </thead>
                    <?php foreach ($tickets_rows as $row): ?>
                    <tr>
                        <td class="even" width="70" nowrap><a
                            href="<?php echo $row['link'];?>"><?php echo $row['ticketid'];?></a></td>
                        <td class="even"><?php echo $row['subject'];?></td>
                        <td class="even" width="120" nowrap="nowrap"><?php echo $row['date_created'];?></td>
                        <td class="even" nowrap="nowrap"><?php echo $row['status'];?></td>
                    </tr>
                    <?php endforeach;?>
                </table>
                <?php else: ?>

                <div class="alert">
                    <img src="media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/info.png"
                         align="absmiddle"/> <?php echo JText::_('no_tickets');?>
                </div>

                <?php endif;?>
            </fieldset>
        </div>
    </div>

</div>