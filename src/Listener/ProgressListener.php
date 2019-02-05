<?php
/**
 * @category Agere
 * @package Agere_Progress
 * @author Popov Sergiy <popov@agere.com.ua>
 * @datetime: 09.08.2016 17:13
 */

namespace Stagem\ZfcProgress\Listener;

use Stagem\ZfcProgress\Service\ContextCreator;
use Stagem\ZfcProgress\Service\ContextInterface;
use Zend\EventManager\Event;
use Zend\EventManager\ListenerAggregateTrait;
use DoctrineModule\Persistence\ProvidesObjectManager;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use Popov\ZfcCurrent\CurrentHelper;
use Stagem\ZfcProgress\Service\ProgressService;

class ProgressListener implements ObjectManagerAwareInterface
{
    use ListenerAggregateTrait;
    use ProvidesObjectManager;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var CurrentHelper
     */
    protected $currentHelper;

    /**
     * @var ProgressService
     */
    protected $progressService;

    /**
     * @var ContextCreator
     */
    protected $contextCreator;

    public function __construct(
        CurrentHelper $currentHelper,
        ProgressService $progressService,
        ContextCreator $contextCreator,
        array $config = null
    ) {
        $this->currentHelper = $currentHelper;
        $this->progressService = $progressService;
        $this->contextCreator = $contextCreator;
        $this->config = $config;
    }

    /**
     * @return CurrentHelper
     */
    public function getCurrentHelper()
    {
        return $this->currentHelper;
    }
    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /*public function attach(EventManagerInterface $events)
    {
        $sem = $events->getSharedManager(); // shared events manager
        $config = $this->getConfig();

        foreach ($config['progress']['listeners'] as $id => $eventName) {
            $this->listeners[] = $sem->attach($id, $eventName, [$this, 'writeProgress']);
        }
    }*/
    public function writeProgress(Event $e)
    {
        if (!($context = $e->getParam('context'))) {
            return;
        }
        if (!($contextProgress = $this->getProgressContext($context))) {
            return;
        }
        $contextProgress->setEvent($e);
        /** @var ProgressService $progressService */
        $this->progressService->writeProgress($contextProgress);
    }

    /**
     * @param $context
     * @return bool|ContextInterface
     */
    public function getProgressContext($context)
    {
        $config = $this->getConfig();
        $currentPlugin = $this->getCurrentHelper();
        $contextNamespace = $currentPlugin->currentModule($context);
        if (!isset($config['progress'][$contextNamespace]['context'])) {
            return false;
        }
        $contextProgress = $this->contextCreator->create($config['progress'][$contextNamespace]['context']);

        return $contextProgress;
    }
}