<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v6.2.2
 * @build-date      2014/10/03
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.form.helper');

class JFormFieldJlinkedMigrator extends JFormField
{
    public $type = 'JLinkedMigrator';

    private $migrator;
    public function __construct()
    {
        include_once(JPATH_ADMINISTRATOR . '/components/com_jfbconnect/helpers/migrator/jlinked.php');
        $this->migrator = new JFBConnectMigratorJlinked();
    }

    protected function getLabel()
    {
        if ($this->migrator->isInstalled())
            return parent::getLabel();
        else
            return "";
    }

    protected function getInput()
    {
        if ($this->migrator->isInstalled())
        {
            $html = "<p>JLinked was detected on your system. JLinked development has been discontinued and it's features have been integrated into JFBConnect.</p>";
            $html .= "<p>The following steps will allow you to migrate your JLinked settings and delete the JLinked database tables that are no longer needed.</p>";
            $html .= "<p>Migration Steps:</p>";
            $html .= '<ol>';
            $html .= '<li>';
            if ($this->migrator->migrationDone())
                $html .= '<strong>(Done)</strong> - ';
            $html .= '<a href="index.php?option=com_jfbconnect&task=config.migrate&migration=jlinked">Migrate Settings & Usermap</a></li>';
            $html .= '<li>';
            if (!$this->migrator->filesPresent())
                $html .= '<strong>(Done)</strong> - ';
            $html .= '<a href="index.php?option=com_jfbconnect&task=config.migrate&migration=jlinked.uninstall">Uninstall JLinked</a></li>';

            $html .= '<li><a href="index.php?option=com_jfbconnect&task=config.migrate&migration=jlinked.removeTables">Remove JLinked Database Tables</a></li>';
            $html .= '</ol>';

            return $html;
        }
    }

/*
    protected function getOptions()
    {
    }
*/

}