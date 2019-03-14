<?php
/**
 * Created by PhpStorm.
 * User: Vlad Kozak
 * Date: 28.03.16
 * Time: 20:02
 */

namespace Stagem\ZfcProgress\Listener;

use DateTime;
use Popov\ZfcEntity\Helper\EntityHelper;
use Stagem\ZfcStatus\Helper\StatusHelper;
use Stagem\ZfcStatus\Model\Status;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Stagem\ZfcStatus\Controller\StatusController;
use Zend\EventManager\Event;
use DoctrineModule\Persistence\ProvidesObjectManager;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use Stagem\ZfcStatus\Model\StatusedAtAwareInterface;

class ProgressServiceListener implements ObjectManagerAwareInterface
{
    use ProvidesObjectManager;

    protected $statusHelper;

    protected $entityHelper;

    public function __construct(EntityHelper $entityHelper /*StatusHelper $statusHelper*/)
    {
        $this->entityHelper = $entityHelper;
        //$this->statusHelper = $statusHelper;
    }

    public function setDefaultStatus($e)
    {
        $progress = $e->getTarget();
        if (method_exists($progress, 'getStatus')&& !$progress->getId() && !$progress->getStatus()) {
            $om = $this->getObjectManager();
            $entity = $this->entityHelper->setContext($progress)->getEntity();
            $status = $om->getRepository(Status::class)->findOneBy(['isDefault' => 1, 'entity' => $entity]);
            if ($status) {
                $progress->setStatus($status);
                $progress->setStatusedAt(new DateTime('now'));
            }
        }
    }
}