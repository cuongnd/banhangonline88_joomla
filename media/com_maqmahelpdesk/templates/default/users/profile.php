<div class="maqmahelpdesk container-fluid">

	<h2><?php echo JText::_('wk_profile');?></h2>

	<p>&nbsp;</p>

	<p class="tar">
	    <small><span class="required">*</span> <b><?php echo JText::_('field_required_desc');?></b></small>
	</p>

	<form action="<?php echo JRoute::_("index.php");?>" method="post" id="profileForm" name="profileForm"
	      class="form-horizontal" enctype="multipart/form-data" onsubmit="return submitbutton_reg();">
	    <?php echo JHtml::_('form.token'); ?>

	    <div class="control-group row-fluid">
	        <label class="control-label" for="name"><?php echo JText::_('name');?> <span class="required">*</span></label>

	        <div class="control span9">
	            <input type="text"
	                   id="name"
	                   name="name"
	                   size="50"
	                   class="span10"
	                   value="<?php echo $userInfo->name;?>" />
	        </div>
	    </div>
	    <div class="control-group row-fluid">
	        <label class="control-label" for="email"><?php echo JText::_('email');?> <span class="required">*</span></label>

	        <div class="control span9">
	            <input type="text"
	                   id="email"
	                   name="email"
	                   size="50"
	                   class="span10"
	                   value="<?php echo $userInfo->email;?>" />
	        </div>
	    </div>
	    <?php if ($supportConfig->show_login_details): ?>
	    <div class="control-group row-fluid">
	        <label class="control-label" for="password"><?php echo JText::_('password');?></label>

	        <div class="control span9">
	            <input type="password" id="password" name="password" size="50" class="span10" value=""
	                 />
	        </div>
	    </div>
	    <div class="control-group row-fluid">
	        <label class="control-label" for="password2"><?php echo JText::_('verify_password');?></label>

	        <div class="control span9">
	            <input type="password" id="password2" name="password2" size="50" class="span10" value=""
	                 />
	        </div>
	    </div>
	    <?php endif;?>
	    <div class="control-group row-fluid">
	        <label class="control-label"
	               for="phone"><?php echo JText::_('phone');?> <?php echo $supportConfig->rf_phone ? $req_img : '';?></label>

	        <div class="control span9">
	            <input type="text" id="phone" name="phone" size="50" class="span10"
	                   value="<?php echo $userInfo->phone;?>" />
	        </div>
	    </div>
	    <div class="control-group row-fluid">
	        <label class="control-label"
	               for="fax"><?php echo JText::_('fax');?> <?php echo $supportConfig->rf_fax ? $req_img : '';?></label>

	        <div class="control span9">
	            <input type="text" id="fax" name="fax" size="50" class="span10" value="<?php echo $userInfo->fax;?>"
	                 />
	        </div>
	    </div>
	    <div class="control-group row-fluid">
	        <label class="control-label"
	               for="mobile"><?php echo JText::_('mobile');?> <?php echo $supportConfig->rf_mobile ? $req_img : '';?></label>

	        <div class="control span9">
	            <input type="text" id="mobile" name="mobile" size="50" class="span10"
	                   value="<?php echo $userInfo->mobile;?>" />
	        </div>
	    </div>
	    <div class="control-group row-fluid">
	        <label class="control-label"
	               for="address1"><?php echo JText::_('address1');?> <?php echo $supportConfig->rf_address1 ? $req_img : '';?></label>

	        <div class="control span9">
	            <input type="text" id="address1" name="address1" size="50" class="span10"
	                   value="<?php echo $userInfo->address1;?>"
	                 />
	        </div>
	    </div>
	    <div class="control-group row-fluid">
	        <label class="control-label"
	               for="address2"><?php echo JText::_('address2');?> <?php echo $supportConfig->rf_address2 ? $req_img : '';?></label>

	        <div class="control span9">
	            <input type="text" id="address2" name="address2" size="50" class="span10"
	                   value="<?php echo $userInfo->address2;?>"
	                 />
	        </div>
	    </div>
	    <div class="control-group row-fluid">
	        <label class="control-label"
	               for="zipcode"><?php echo JText::_('zipcode');?> <?php echo $supportConfig->rf_zipcode ? $req_img : '';?></label>

	        <div class="control span9">
	            <input type="text" id="zipcode" name="zipcode" size="50" class="span10"
	                   value="<?php echo $userInfo->zipcode;?>" />
	        </div>
	    </div>
	    <div class="control-group row-fluid">
	        <label class="control-label"
	               for="location"><?php echo JText::_('location');?> <?php echo $supportConfig->rf_location ? $req_img : '';?></label>

	        <div class="control span9">
	            <input type="text" id="location" name="location" size="50" class="span10"
	                   value="<?php echo $userInfo->location;?>"
	                 />
	        </div>
	    </div>
	    <div class="control-group row-fluid">
	        <label class="control-label"
	               for="city"><?php echo JText::_('city');?> <?php echo $supportConfig->rf_city ? $req_img : '';?></label>

	        <div class="control span9">
	            <input type="text" id="city" name="city" size="50" class="span10"
	                   value="<?php echo $userInfo->city;?>" />
	        </div>
	    </div>
	    <div class="control-group row-fluid">
	        <label class="control-label"
	               for="country"><?php echo JText::_('country');?> <?php echo $supportConfig->rf_country ? $req_img : '';?></label>

	        <div class="control span9">
	            <select id="country" name="country">
	                <option value=""></option>
	                <?php echo $countries;?>
	            </select>
	        </div>
	    </div>

	    <?php if (count($custom_fields)): ?>
	    <?php foreach ($custom_fields as $row): ?>
	        <div class="control-group row-fluid">
	            <label class="control-label" for="input01"><?php echo $row['caption'] . ' ' . $row['required'];?></label>
				<div class="control span9">
	                <?php echo $row['field'];?>
	            </div>
	        </div>
	        <?php endforeach; ?>
	    <?php endif;?>

	    <div class="control-group row-fluid">
	        <label class="control-label"><?php echo JText::_('selected_avatar');?></label>

	        <div class="control span9">
	            <img id="selectedavatar" name="selectedavatar" width="32" height="32" src="<?php echo $userInfo->avatar;?>"
	                 align="absmiddle"/> <a href="#avatars_list" data-toggle="modal"
	                                        class="btn"><?php echo JText::_('change_avatar');?></a> &bull; <input
	            type="file" id="avatar_file" name="avatar_file"/>
	        </div>
	    </div>

	    <div class="form-actions">
	        <button type="submit" class="btn btn-success"><?php echo JText::_('save');?></button>
	        <button type="button" class="btn" onclick="javascript:history.go(-1);"><?php echo JText::_('cancel');?></button>
	    </div>

	    <input type="hidden" name="id_workgroup" value="<?php echo $id_workgroup;?>"/>
	    <input type="hidden" name="option" value="com_maqmahelpdesk"/>
	    <input type="hidden" name="Itemid" value="<?php echo $Itemid;?>"/>
	    <input type="hidden" name="task" value="users_saveuseredit"/>
	    <input type="hidden" id="avatar" name="avatar" value="<?php echo $userInfo->avatar;?>"/>
	</form>

	<div id="alertMessage" class="modal">
	    <div class="modal-header">
	        <a class="close" data-dismiss="modal">&times;</a>

	        <h3><?php echo JText::_('warning');?></h3>
	    </div>
	    <div class="modal-body"><p></p></div>
	    <div class="modal-footer">
	        <a href="javascript:;" onclick="$jMaQma('#alertMessage').modal('hide');" data-dismiss="modal"
	           class="btn"><?php echo JText::_('close');?></a>
	    </div>
	</div>

	<div id="avatars_list" style="display:none;width:350px;" class="modal fade">
	    <div class="modal-header">
	        <a class="close" data-dismiss="modal">&times;</a>

	        <h3><?php echo JText::_('change_avatar'); ?></h3>
	    </div>
	    <div class="modal-body">
	        <div>
	            <img src="media/com_maqmahelpdesk/images/avatars/anonymous.png" align="absmiddle"
	                 onclick="SelectAvatar('anonymous');" style="cursor:pointer;" hspace="5" vspace="5"/>
	            <img src="media/com_maqmahelpdesk/images/avatars/businessman1.png" align="absmiddle"
	                 onclick="SelectAvatar('businessman1');" style="cursor:pointer;" hspace="5" vspace="5"/>
	            <img src="media/com_maqmahelpdesk/images/avatars/businessman2.png" align="absmiddle"
	                 onclick="SelectAvatar('businessman2');" style="cursor:pointer;" hspace="5" vspace="5"/>
	            <img src="media/com_maqmahelpdesk/images/avatars/businessman3.png" align="absmiddle"
	                 onclick="SelectAvatar('businessman3');" style="cursor:pointer;" hspace="5" vspace="5"/>
	            <img src="media/com_maqmahelpdesk/images/avatars/businesswoman.png" align="absmiddle"
	                 onclick="SelectAvatar('businesswoman');" style="cursor:pointer;" hspace="5" vspace="5"/>
	            <img src="media/com_maqmahelpdesk/images/avatars/doctorscientist.png" align="absmiddle"
	                 onclick="SelectAvatar('doctorscientist');" style="cursor:pointer;" hspace="5" vspace="5"/>
	            <img src="media/com_maqmahelpdesk/images/avatars/man1.png" align="absmiddle" onclick="SelectAvatar('man1');"
	                 style="cursor:pointer;" hspace="5" vspace="5"/>
	            <img src="media/com_maqmahelpdesk/images/avatars/man2.png" align="absmiddle" onclick="SelectAvatar('man2');"
	                 style="cursor:pointer;" hspace="5" vspace="5"/>
	            <img src="media/com_maqmahelpdesk/images/avatars/man3.png" align="absmiddle" onclick="SelectAvatar('man3');"
	                 style="cursor:pointer;" hspace="5" vspace="5"/>
	            <img src="media/com_maqmahelpdesk/images/avatars/man4.png" align="absmiddle" onclick="SelectAvatar('man4');"
	                 style="cursor:pointer;" hspace="5" vspace="5"/>
	            <img src="media/com_maqmahelpdesk/images/avatars/man5.png" align="absmiddle" onclick="SelectAvatar('man5');"
	                 style="cursor:pointer;" hspace="5" vspace="5"/>
	            <img src="media/com_maqmahelpdesk/images/avatars/man6.png" align="absmiddle" onclick="SelectAvatar('man6');"
	                 style="cursor:pointer;" hspace="5" vspace="5"/>
	            <img src="media/com_maqmahelpdesk/images/avatars/man7.png" align="absmiddle" onclick="SelectAvatar('man7');"
	                 style="cursor:pointer;" hspace="5" vspace="5"/>
	            <img src="media/com_maqmahelpdesk/images/avatars/man8.png" align="absmiddle" onclick="SelectAvatar('man8');"
	                 style="cursor:pointer;" hspace="5" vspace="5"/>
	            <img src="media/com_maqmahelpdesk/images/avatars/man9.png" align="absmiddle" onclick="SelectAvatar('man9');"
	                 style="cursor:pointer;" hspace="5" vspace="5"/>
	            <img src="media/com_maqmahelpdesk/images/avatars/mansupport1.png" align="absmiddle"
	                 onclick="SelectAvatar('mansupport1');" style="cursor:pointer;" hspace="5" vspace="5"/>
	            <img src="media/com_maqmahelpdesk/images/avatars/mansupport2.png" align="absmiddle"
	                 onclick="SelectAvatar('mansupport2');" style="cursor:pointer;" hspace="5" vspace="5"/>
	            <img src="media/com_maqmahelpdesk/images/avatars/painter.png" align="absmiddle"
	                 onclick="SelectAvatar('painter');" style="cursor:pointer;" hspace="5" vspace="5"/>
	            <img src="media/com_maqmahelpdesk/images/avatars/security.png" align="absmiddle"
	                 onclick="SelectAvatar('security');" style="cursor:pointer;" hspace="5" vspace="5"/>
	            <img src="media/com_maqmahelpdesk/images/avatars/woman1.png" align="absmiddle"
	                 onclick="SelectAvatar('woman1');" style="cursor:pointer;" hspace="5" vspace="5"/>
	            <img src="media/com_maqmahelpdesk/images/avatars/woman2.png" align="absmiddle"
	                 onclick="SelectAvatar('woman2');" style="cursor:pointer;" hspace="5" vspace="5"/>
	            <img src="media/com_maqmahelpdesk/images/avatars/woman3.png" align="absmiddle"
	                 onclick="SelectAvatar('woman3');" style="cursor:pointer;" hspace="5" vspace="5"/>
	            <img src="media/com_maqmahelpdesk/images/avatars/woman4.png" align="absmiddle"
	                 onclick="SelectAvatar('woman4');" style="cursor:pointer;" hspace="5" vspace="5"/>
	            <img src="media/com_maqmahelpdesk/images/avatars/woman5.png" align="absmiddle"
	                 onclick="SelectAvatar('woman5');" style="cursor:pointer;" hspace="5" vspace="5"/>
	            <img src="media/com_maqmahelpdesk/images/avatars/woman6.png" align="absmiddle"
	                 onclick="SelectAvatar('woman6');" style="cursor:pointer;" hspace="5" vspace="5"/>
	            <img src="media/com_maqmahelpdesk/images/avatars/woman7.png" align="absmiddle"
	                 onclick="SelectAvatar('woman7');" style="cursor:pointer;" hspace="5" vspace="5"/>
	            <img src="media/com_maqmahelpdesk/images/avatars/woman8.png" align="absmiddle"
	                 onclick="SelectAvatar('woman8');" style="cursor:pointer;" hspace="5" vspace="5"/>
	            <img src="media/com_maqmahelpdesk/images/avatars/womansupport1.png" align="absmiddle"
	                 onclick="SelectAvatar('womansupport1');" style="cursor:pointer;" hspace="5" vspace="5"/>
	            <img src="media/com_maqmahelpdesk/images/avatars/womansupport2.png" align="absmiddle"
	                 onclick="SelectAvatar('womansupport2');" style="cursor:pointer;" hspace="5" vspace="5"/>
	            <img src="media/com_maqmahelpdesk/images/avatars/workman1.png" align="absmiddle"
	                 onclick="SelectAvatar('workman1');" style="cursor:pointer;" hspace="5" vspace="5"/>
	            <img src="media/com_maqmahelpdesk/images/avatars/workman2.png" align="absmiddle"
	                 onclick="SelectAvatar('workman2');" style="cursor:pointer;" hspace="5" vspace="5"/>
	        </div>
	    </div>
	    <div class="modal-footer">
	        <a href="javascript:;" onclick="$jMaQma('#avatars_list').modal('hide');" data-dismiss="modal"
	           class="btn"><?php echo JText::_('close');?></a>
	    </div>
	</div>

</div>