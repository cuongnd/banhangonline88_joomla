<?php

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\Exception\InactiveScopeException;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Exception\LogicException;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;

/**
 * CachedCiviContainer
 *
 * This class has been auto-generated
 * by the Symfony Dependency Injection Component.
 */
class CachedCiviContainer extends Container
{
    private $parameters;
    private $targetDirs = array();

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->parameters = $this->getDefaultParameters();

        $this->services =
        $this->scopedServices =
        $this->scopeStacks = array();

        $this->set('service_container', $this);

        $this->scopes = array();
        $this->scopeChildren = array();
        $this->methodMap = array(
            'angular' => 'getAngularService',
            'cache.community_messages' => 'getCache_CommunityMessagesService',
            'cache.default' => 'getCache_DefaultService',
            'cache.js_strings' => 'getCache_JsStringsService',
            'cache.settings' => 'getCache_SettingsService',
            'civi_api_kernel' => 'getCiviApiKernelService',
            'civi_container_factory' => 'getCiviContainerFactoryService',
            'civi_token_compat' => 'getCiviTokenCompatService',
            'crm_activity_tokens' => 'getCrmActivityTokensService',
            'crm_contribute_tokens' => 'getCrmContributeTokensService',
            'crm_event_tokens' => 'getCrmEventTokensService',
            'crm_member_tokens' => 'getCrmMemberTokensService',
            'cxn_reg_client' => 'getCxnRegClientService',
            'dispatcher' => 'getDispatcherService',
            'httpclient' => 'getHttpclientService',
            'i18n' => 'getI18nService',
            'lockmanager' => 'getLockmanagerService',
            'magic_function_provider' => 'getMagicFunctionProviderService',
            'paths' => 'getPathsService',
            'pear_mail' => 'getPearMailService',
            'psr_log' => 'getPsrLogService',
            'resources' => 'getResourcesService',
            'runtime' => 'getRuntimeService',
            'settings_manager' => 'getSettingsManagerService',
            'sql_triggers' => 'getSqlTriggersService',
            'userpermissionclass' => 'getUserpermissionclassService',
            'usersystem' => 'getUsersystemService',
        );

        $this->aliases = array();
    }

    /**
     * Gets the 'angular' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return \Civi\Angular\Manager A Civi\Angular\Manager instance.
     */
    protected function getAngularService()
    {
        return $this->services['angular'] = $this->get('civi_container_factory')->createAngularManager();
    }

    /**
     * Gets the 'cache.community_messages' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return \CRM_Utils_Cache_Interface A CRM_Utils_Cache_Interface instance.
     */
    protected function getCache_CommunityMessagesService()
    {
        return $this->services['cache.community_messages'] = \CRM_Utils_Cache::create(array('name' => 'community_messages', 'type' => array(0 => '*memory*', 1 => 'SqlGroup', 2 => 'ArrayCache')));
    }

    /**
     * Gets the 'cache.default' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return \CRM_Utils_Cache A CRM_Utils_Cache instance.
     */
    protected function getCache_DefaultService()
    {
        return $this->services['cache.default'] = \CRM_Utils_Cache::singleton();
    }

    /**
     * Gets the 'cache.js_strings' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return \CRM_Utils_Cache_Interface A CRM_Utils_Cache_Interface instance.
     */
    protected function getCache_JsStringsService()
    {
        return $this->services['cache.js_strings'] = \CRM_Utils_Cache::create(array('name' => 'js_strings', 'type' => array(0 => '*memory*', 1 => 'SqlGroup', 2 => 'ArrayCache')));
    }

    /**
     * Gets the 'cache.settings' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @throws RuntimeException always since this service is expected to be injected dynamically
     */
    protected function getCache_SettingsService()
    {
        throw new RuntimeException('You have requested a synthetic service ("cache.settings"). The DIC does not know how to construct this service.');
    }

    /**
     * Gets the 'civi_api_kernel' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return \Civi\API\Kernel A Civi\API\Kernel instance.
     */
    protected function getCiviApiKernelService()
    {
        return $this->services['civi_api_kernel'] = $this->get('civi_container_factory')->createApiKernel($this->get('dispatcher'), $this->get('magic_function_provider'));
    }

    /**
     * Gets the 'civi_container_factory' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return \Civi\Core\Container A Civi\Core\Container instance.
     */
    protected function getCiviContainerFactoryService()
    {
        return $this->services['civi_container_factory'] = new \Civi\Core\Container();
    }

    /**
     * Gets the 'civi_token_compat' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return \Civi\Token\TokenCompatSubscriber A Civi\Token\TokenCompatSubscriber instance.
     */
    protected function getCiviTokenCompatService()
    {
        return $this->services['civi_token_compat'] = new \Civi\Token\TokenCompatSubscriber();
    }

    /**
     * Gets the 'crm_activity_tokens' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return \CRM_Activity_Tokens A CRM_Activity_Tokens instance.
     */
    protected function getCrmActivityTokensService()
    {
        return $this->services['crm_activity_tokens'] = new \CRM_Activity_Tokens();
    }

    /**
     * Gets the 'crm_contribute_tokens' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return \CRM_Contribute_Tokens A CRM_Contribute_Tokens instance.
     */
    protected function getCrmContributeTokensService()
    {
        return $this->services['crm_contribute_tokens'] = new \CRM_Contribute_Tokens();
    }

    /**
     * Gets the 'crm_event_tokens' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return \CRM_Event_Tokens A CRM_Event_Tokens instance.
     */
    protected function getCrmEventTokensService()
    {
        return $this->services['crm_event_tokens'] = new \CRM_Event_Tokens();
    }

    /**
     * Gets the 'crm_member_tokens' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return \CRM_Member_Tokens A CRM_Member_Tokens instance.
     */
    protected function getCrmMemberTokensService()
    {
        return $this->services['crm_member_tokens'] = new \CRM_Member_Tokens();
    }

    /**
     * Gets the 'cxn_reg_client' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return \Civi\Cxn\Rpc\RegistrationClient A Civi\Cxn\Rpc\RegistrationClient instance.
     */
    protected function getCxnRegClientService()
    {
        return $this->services['cxn_reg_client'] = \CRM_Cxn_BAO_Cxn::createRegistrationClient();
    }

    /**
     * Gets the 'dispatcher' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return \Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher A Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher instance.
     */
    protected function getDispatcherService()
    {
        $this->services['dispatcher'] = $instance = $this->get('civi_container_factory')->createEventDispatcher($this);

        $instance->addSubscriberService('civi_token_compat', 'Civi\\Token\\TokenCompatSubscriber');
        $instance->addSubscriberService('crm_activity_tokens', 'CRM_Activity_Tokens');
        $instance->addSubscriberService('crm_contribute_tokens', 'CRM_Contribute_Tokens');
        $instance->addSubscriberService('crm_event_tokens', 'CRM_Event_Tokens');
        $instance->addSubscriberService('crm_member_tokens', 'CRM_Member_Tokens');

        return $instance;
    }

    /**
     * Gets the 'httpclient' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return \CRM_Utils_HttpClient A CRM_Utils_HttpClient instance.
     */
    protected function getHttpclientService()
    {
        return $this->services['httpclient'] = \CRM_Utils_HttpClient::singleton();
    }

    /**
     * Gets the 'i18n' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return \CRM_Core_I18n A CRM_Core_I18n instance.
     */
    protected function getI18nService()
    {
        return $this->services['i18n'] = \CRM_Core_I18n::singleton();
    }

    /**
     * Gets the 'lockmanager' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @throws RuntimeException always since this service is expected to be injected dynamically
     */
    protected function getLockmanagerService()
    {
        throw new RuntimeException('You have requested a synthetic service ("lockmanager"). The DIC does not know how to construct this service.');
    }

    /**
     * Gets the 'magic_function_provider' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return \Civi\API\Provider\MagicFunctionProvider A Civi\API\Provider\MagicFunctionProvider instance.
     */
    protected function getMagicFunctionProviderService()
    {
        return $this->services['magic_function_provider'] = new \Civi\API\Provider\MagicFunctionProvider();
    }

    /**
     * Gets the 'paths' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @throws RuntimeException always since this service is expected to be injected dynamically
     */
    protected function getPathsService()
    {
        throw new RuntimeException('You have requested a synthetic service ("paths"). The DIC does not know how to construct this service.');
    }

    /**
     * Gets the 'pear_mail' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return \Mail A Mail instance.
     */
    protected function getPearMailService()
    {
        return $this->services['pear_mail'] = \CRM_Utils_Mail::createMailer();
    }

    /**
     * Gets the 'psr_log' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return \CRM_Core_Error_Log A CRM_Core_Error_Log instance.
     */
    protected function getPsrLogService()
    {
        return $this->services['psr_log'] = new \CRM_Core_Error_Log();
    }

    /**
     * Gets the 'resources' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return \CRM_Core_Resources A CRM_Core_Resources instance.
     */
    protected function getResourcesService()
    {
        return $this->services['resources'] = \CRM_Core_Resources::singleton();
    }

    /**
     * Gets the 'runtime' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @throws RuntimeException always since this service is expected to be injected dynamically
     */
    protected function getRuntimeService()
    {
        throw new RuntimeException('You have requested a synthetic service ("runtime"). The DIC does not know how to construct this service.');
    }

    /**
     * Gets the 'settings_manager' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @throws RuntimeException always since this service is expected to be injected dynamically
     */
    protected function getSettingsManagerService()
    {
        throw new RuntimeException('You have requested a synthetic service ("settings_manager"). The DIC does not know how to construct this service.');
    }

    /**
     * Gets the 'sql_triggers' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return \Civi\Core\SqlTriggers A Civi\Core\SqlTriggers instance.
     */
    protected function getSqlTriggersService()
    {
        return $this->services['sql_triggers'] = new \Civi\Core\SqlTriggers();
    }

    /**
     * Gets the 'userpermissionclass' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @throws RuntimeException always since this service is expected to be injected dynamically
     */
    protected function getUserpermissionclassService()
    {
        throw new RuntimeException('You have requested a synthetic service ("userpermissionclass"). The DIC does not know how to construct this service.');
    }

    /**
     * Gets the 'usersystem' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @throws RuntimeException always since this service is expected to be injected dynamically
     */
    protected function getUsersystemService()
    {
        throw new RuntimeException('You have requested a synthetic service ("usersystem"). The DIC does not know how to construct this service.');
    }

    /**
     * {@inheritdoc}
     */
    public function getParameter($name)
    {
        $name = strtolower($name);

        if (!(isset($this->parameters[$name]) || array_key_exists($name, $this->parameters))) {
            throw new InvalidArgumentException(sprintf('The parameter "%s" must be defined.', $name));
        }

        return $this->parameters[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function hasParameter($name)
    {
        $name = strtolower($name);

        return isset($this->parameters[$name]) || array_key_exists($name, $this->parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function setParameter($name, $value)
    {
        throw new LogicException('Impossible to call set() on a frozen ParameterBag.');
    }

    /**
     * {@inheritdoc}
     */
    public function getParameterBag()
    {
        if (null === $this->parameterBag) {
            $this->parameterBag = new FrozenParameterBag($this->parameters);
        }

        return $this->parameterBag;
    }
    /**
     * Gets the default parameters.
     *
     * @return array An array of the default parameters
     */
    protected function getDefaultParameters()
    {
        return array(
            'civicrm_base_path' => '/home/banhangonl/domains/banhangonline88.com/public_html/administrator/components/com_civicrm/civicrm',
        );
    }
}
