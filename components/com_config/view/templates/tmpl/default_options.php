<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_config
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Load chosen.css
JHtml::_('formbehavior.chosen', 'select');

?>


<div class="form-horizontal">
    <?php
    echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'details'));


    $fieldSets = $this->form->getFieldsets('params');

    JHtml::_('formbehavior.chosen', 'select');

    ?>

    <?php
    $fieldSets = $this->form->getFieldsets('params');

    $i = 0;

    foreach ($fieldSets as $name => $fieldSet) :
        echo JHtml::_('bootstrap.addTab', 'myTab','attrib-'.$name, $name);


        ?>
        <?php foreach ($this->form->getFieldset($name) as $field) : ?>
        <?php echo $field->input; ?>
        <?php endforeach;
        echo JHtml::_('bootstrap.endTab');
    endforeach;
    ?>
    <?php


    echo JHtml::_('bootstrap.endTabSet');


    ?>


</div>

