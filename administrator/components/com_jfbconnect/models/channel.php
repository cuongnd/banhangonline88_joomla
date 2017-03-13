<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v6.2.2
 * @build-date      2014/10/03
 */

jimport('joomla.application.component.modeladmin');
class JFBConnectModelChannel extends JModelAdmin
{

    function getForm($data = array(), $loadData = true)
    {
        $form = $this->loadForm('com_jfbconnect_channel_edit', 'channeledit', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form))
            return false;

        return $form;
    }

    function save($data)
    {
        $oldData = array();

        if ($data['id'] > 0)
        {
            $table = $this->getTable();
            $table->load($data['id']);
            $oldData['id'] = $table->id;
            $oldData['provider'] = $table->provider;
            $oldData['type'] = $table->type;
            $oldData['attribs'] = (array)$table->attribs;
        }

        // Set the dates
        $date = JFactory::getDate();
        if ($data['id'] == 0)
            $data['created'] = $date->toSql();
        $data['modified'] = $date->toSql();

        if ($data['provider'] != '--' && $data['type'] != '--')
        {
            $channel = JFBCFactory::provider($data['provider'])->channel($data['type']);
            $data = $channel->onBeforeSave($data); // Manipulate the data however may be necessary
        }
        $return = parent::save($data);

        // Used to removed permissions from users that aren't associated with this channel (or anything else needed for cleanup)
        if ($return && $data['provider'] != '--' && $data['type'] != '--')
            $channel->onAfterSave($data, $oldData);

        return $return;
    }

    /**
     * Method to delete one or more records.
     *
     * @param   array &$pks  An array of record primary keys.
     *
     * @return  boolean  True if successful, false if an error occurs.
     *
     * Copied from the Joomla legacy folder. Removed content triggers and added deletion of scope for associated user
     */
    public function delete(&$pks)
    {
        $pks = (array)$pks;
        $table = $this->getTable();

        // Iterate the items to delete each one.
        foreach ($pks as $i => $pk)
        {
            if ($table->load($pk))
            {
                if ($this->canDelete($table))
                {
                    // Delete any scope for the user associated with this channel
                    $attribs = $table->attribs;
                    if (isset($attribs->user_id))
                    {
                        $userModel = JFBConnectModelUserMap::getUser($attribs->user_id, $table->provider);
                        $userModel->removeAllScope('channel', $table->id);
                    }

                    if (!$table->delete($pk))
                    {
                        $this->setError($table->getError());
                        return false;
                    }
                }
                else
                {
                    // Prune items that you can't change.
                    unset($pks[$i]);
                    $error = $this->getError();
                    if ($error)
                    {
                        JLog::add($error, JLog::WARNING, 'jerror');
                        return false;
                    }
                    else
                    {
                        JLog::add(JText::_('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED'), JLog::WARNING, 'jerror');
                        return false;
                    }
                }
            }
            else
            {
                $this->setError($table->getError());
                return false;
            }
        }
        // Clear the component's cache
        $this->cleanCache();

        return true;
    }

    protected function preprocessForm(JForm $form, $data, $group = 'plugin')
    {
        // Figure out the provider/channel selected.. either from the request or from the saved table data
        if (empty($data))
        {
            $jform = JFactory::getApplication()->input->post->get('jform', array(), 'array');
            $provider = isset($jform['provider']) ? $jform['provider'] : null;
            $type = isset($jform['type']) ? $jform['type'] : null;
        }
        else
        {
            $provider = isset($data->provider) ? $data->provider : null;
            $type = isset($data->type) ? $data->type : null;
        }

        if ($provider && $type)
        {
            JForm::addFieldPath(JPATH_SITE . '/components/com_jfbconnect/libraries/provider/' . $provider . '/channel/fields');
            $formFile = JPATH_SITE . '/components/com_jfbconnect/libraries/provider/' . $provider . '/channel/' . $type . '_outbound.xml';
            if (file_exists($formFile))
            {
                if (!$form->loadFile($formFile, false))
                {
                    throw new Exception(JText::_('JERROR_LOADFILE_FAILED'));
                }
            }
        }

        // Trigger the default form events.
        parent::preprocessForm($form, $data, $group);
    }

    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $app = JFactory::getApplication();
        $data = $app->getUserState('com_jfbconnect.edit.channel.data', array());

        if (empty($data))
        {
            $data = $this->getItem();

            // Prime some default values.
            if ($this->getState('channel.id') == 0)
            {
                $data->set('published', '0');
                $data->set('provider', 'google');
            }
        }

        return $data;
    }
}