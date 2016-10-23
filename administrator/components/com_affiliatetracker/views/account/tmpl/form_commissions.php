<?php

$installedPlugins = AffiliateHelper::getInstalledPlugins();
$disabledInput = "";

if(!empty($installedPlugins)) { ?>
    <fieldset class="adminform">
        <legend><?php echo JText::_( 'COMMISSION_DETAILS' ); ?></legend>
        <?php if ($this->account->id == 0) {
            $disabledInput = "disabled"; ?>
            <p class="alert alert-info"><?php echo JText::_('LEAVE_BLANK_COMMISSIONS_INFO'); ?></p>
        <?php } ?>
        <?php for ($i = 0; $i < sizeof($installedPlugins); $i++) {
            $variable_commission = AffiliateHelper::getVariableCommissionByExtensionName(json_decode($this->account->variable_comissions), $installedPlugins[$i]);
            $displayCompName = ucfirst(substr_replace($installedPlugins[$i], '', 0, 4));
            ?>
            <div class="control-group">
                <label class="control-label" for="commission_<?php echo $installedPlugins[$i]; ?>"> <?php echo JText::sprintf( 'COMMISSION_FOR', $displayCompName ); ?> </label>
                <div class="controls">
                    <input <?php echo $disabledInput; ?> class="inputbox input-mini" type="text" name="commission_<?php echo $installedPlugins[$i]; ?>" id="commission_<?php echo $installedPlugins[$i]; ?>" size="8" maxlength="250" value="<?php if(!empty($variable_commission->commission)) echo $variable_commission->commission; ?>" />
                    <select <?php echo $disabledInput; ?> name="type_<?php echo $installedPlugins[$i]; ?>" id="type" class="input-medium">
                        <?php
                        $publish = "";
                        $unpublish = "";
                        if($variable_commission->type == "percent") $publish = "selected";
                        else $unpublish = "selected";

                        ?>
                        <option <?php echo $publish;?> value="percent"><?php echo JText::_('PERCENT');?></option>
                        <option <?php echo $unpublish;?> value="flat"><?php echo JText::_('FLAT');?></option>
                    </select>
                </div>
            </div>
        <?php } ?>
    </fieldset>
<?php } ?>