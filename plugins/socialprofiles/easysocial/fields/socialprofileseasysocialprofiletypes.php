<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @build-date      2014/10/03
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

class JFormFieldSocialProfilesEasySocialProfileTypes extends JFormFieldList
{
    public $type = 'SocialProfilesEasySocialProfileTypes';

    protected function getOptions()
    {
        require_once(JPATH_ROOT . '/administrator/components/com_easysocial/includes/foundry.php');

        $model = Foundry::model('profiles');

        $profileTypes = $model->getProfiles();

        $options = array();
        foreach ($profileTypes as $p)
            $options[] = JHtml::_('select.option', $p->id, $p->title);

        return $options;
    }
}
