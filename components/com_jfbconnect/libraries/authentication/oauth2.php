<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Google
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JFBConnectAuthenticationOauth2Base', JPATH_SITE . '/components/com_jfbconnect/libraries/authentication/oauth2base.php');

/**
 * Google OAuth authentication class
 *
 * @package     Joomla.Platform
 * @subpackage  Google
 * @since       12.3
 */
class JFBConnectAuthenticationOauth2 extends JFBConnectAuthenticationOauth2Base
{
    /**
     * Constructor.
     *
     * @param   JRegistry $options  JGoogleAuth options object.
     * @param   JOAuth2Client $client   OAuth client for Google authentication.
     *
     * @since   12.3
     */
    public $transport;
    public function __construct(JRegistry $options = null, JHttp $http = null, JInput $input = null, JApplicationWeb $application = null)
    {
        // We want to use cURL, so need to build this all up. This also helps poke into each of the things when debugging
        $httpOptions = new JRegistry();
        $httpOptions->set('follow_location', false);
        try
        {
            $transport = new JHttpTransportCurl($httpOptions);
        }
        catch (Exception $e)
        {
            JFactory::getApplication()->enqueueMessage('JFBConnect requires the CURL PHP extension to be installed and callable.', 'error');
            $transport = null;
        }
        $http = new JHttp($httpOptions, $transport);
        $this->transport = $transport;

        $options = isset($options) ? $options : new JRegistry;
        $options->set('state', JSession::getFormToken()); // This is sent back to us to identify the provider
        if (!$options->exists('sendheaders'))
            $options->set('sendheaders', true);

        parent::__construct($options, $http, $input, $application);
    }

    protected $provider;
    public function initialize(JFBConnectProvider $p)
    {
        $this->provider = $p;
        $this->setOption('redirecturi', JURI::base() . 'index.php?option=com_jfbconnect&task=authenticate.callback&provider=' . strtolower($p->name));
        $this->setOption('clientid', $p->appId);
        $this->setOption('clientsecret', $p->secretKey);

        $this->setOption('authurl', $this->options->get('authurl'));
        $this->setOption('tokenurl', $this->options->get('tokenurl'));

        if (!$this->getOption('requestparams'))
        {
            $this->setOption('requestparams', Array());
        }

        $params = $this->getOption('requestparams');

        if (!array_key_exists('access_type', $params))
        {
            $params['access_type'] = 'offline';
        }
        if ($params['access_type'] == 'offline' && $this->getOption('userefresh') === null)
        {
            $this->setOption('userefresh', true);
        }
        if (!array_key_exists('approval_prompt', $params))
        {
            $params['approval_prompt'] = 'auto';
        }

        $this->setOption('requestparams', $params);
    }

    public function query($url, $data = null, $headers = array(), $method = 'get', $timeout = null)
    {
        $headers = array_merge($headers, $this->options->get('headers', array()));
        return parent::query($url, $data, $headers, $method);
    }
}
