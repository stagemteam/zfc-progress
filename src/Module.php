<?php
namespace Stagem\ZfcProgress;

use Zend\Mvc\MvcEvent;
use Zend\EventManager\EventInterface;
use Zend\ServiceManager\ServiceManager;

class Module
{
    /*public function onBootstrap(EventInterface $e)
    {
        $eventManager = $e->getTarget()->getEventManager();
        $sm = $e->getApplication()->getServiceManager();
        $eventManager->attach((new Listener\ProgressListener())->setServiceManager($sm));
    }*/

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }
}
