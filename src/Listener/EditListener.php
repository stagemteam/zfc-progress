<?php
/**
 * @category Agere
 * @package Agere_Progress
 * @author Popov Sergiy <popov@agere.com.ua>
 * @datetime: 09.08.2016 17:13
 */
namespace Agere\ZfcProgress\Listener;

use Agere\ZfcProgress\Service\ContextInterface;
use Zend\EventManager\Event;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;
use DoctrineModule\Persistence\ProvidesObjectManager;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;

use Agere\Current\Plugin\Current as CurrentPlugin;
use Agere\Core\Service\ServiceManagerAwareTrait;
use Agere\ZfcProgress\Service\ProgressService;

class EditListener implements ListenerAggregateInterface, ObjectManagerAwareInterface
{
    use ListenerAggregateTrait;

    use ProvidesObjectManager;

    use ServiceManagerAwareTrait;

    /** @var array */
    protected $config;

    /** @var */
    protected $currentPlugin;

    public function setCurrentPlugin(CurrentPlugin $currentPlugin)
    {
        $this->currentPlugin = $currentPlugin;

        return $this;
    }

    /**
     * @return CurrentPlugin
     */
    public function getCurrentPlugin()
    {
        if (!$this->currentPlugin) {
            $sm = $this->getServiceManager();
            $cpm = $sm->get('ControllerPluginManager');
            $this->currentPlugin = $cpm->get('current');
        }

        return $this->currentPlugin;
    }

    public function setConfig(array $config)
    {
        $this->config = $config;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        if (null === $this->config) {
            $this->config = $this->getServiceManager()->get('Config');
        }

        return $this->config;
    }

    public function attach(EventManagerInterface $events)
    {
        $sem = $events->getSharedManager(); // shared events manager
        $config = $this->getConfig();

        foreach ($config['progress']['listeners'] as $id => $eventName) {
            $this->listeners[] = $sem->attach($id, $eventName, [$this, 'writeProgress']);
        }
    }

    public function writeProgress(Event $e)
    {
        if (!($context = $e->getParam('context'))) {
            return;
        }

        if (!($contextProgress = $this->getProgressContext($context))) {
            return;
        }

        $contextProgress->setEvent($e);

        $sm = $this->getServiceManager();
        /** @var ProgressService $progressService */
        $progressService = $sm->get(ProgressService::class);
        $progressService->writeProgress($contextProgress);
    }

    /**
     * @param $context
     * @return bool|ContextInterface
     */
    public function getProgressContext($context)
    {
        $sm = $this->getServiceManager();
        $config = $this->getConfig();
        $currentPlugin = $this->getCurrentPlugin();
        $contextNamespace = $currentPlugin->currentModule($context);

        if (!isset($config['progress'][$contextNamespace]['context'])) {
            return false;
        }

        $contextProgress = $sm->get($config['progress'][$contextNamespace]['context']);

        return $contextProgress;
    }
}