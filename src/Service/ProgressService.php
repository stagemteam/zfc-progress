<?php

namespace Stagem\ZfcProgress\Service;

use DateTime;
use Popov\ZfcEntity\Helper\ModuleHelper;
use Popov\ZfcCore\Service\DomainServiceAbstract;
use Stagem\ZfcProgress\Model\Repository\ProgressRepository;
use Stagem\ZfcProgress\Model\Progress;
use Popov\ZfcEntity\Model\Entity;
use Popov\ZfcUser\Model\User;

class ProgressService extends DomainServiceAbstract
{
    protected $entity = Progress::class;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var ModuleHelper
     */
    protected $modulePlugin;

    public function __construct(/*User $user, */ModuleHelper $modulePlugin)
    {
        //$this->user = $user;
        $this->modulePlugin = $modulePlugin;
    }

    /*public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    public function getUser()
    {
        return $this->user;
    }*/

    public function getModuleHelper()
    {
        return $this->modulePlugin;
    }

    public function getEntityHelper()
    {
        return $this->getModuleHelper()->getEntityHelper();
    }

    /**
     * Get progress for one or several items
     *
     * @param object|array $item Object or objects set
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getProgress($item)
    {
        $items = is_array($item) ? $item : [$item];
        $entities = $this->getEntities($items);
        /** @var ProgressRepository $repository */
        $repository = $this->getRepository();

        //return $repository->getItemProgress($item, $module);
        return $repository->getItemsProgress($items, $entities);
    }

    public function getRecursiveProgress($item, $level = 1)
    {

    }

    public function getProgressByContext($item, $context)
    {
        $entity = $this->getEntities($item);
        /** @var ProgressRepository $repository */
        $repository = $this->getRepository();

        return $repository->getItemProgressByContext($item, $entity, $context);
    }

    public function writeProgress(ContextInterface $contextProgress)
    {
        $om = $this->getObjectManager();
        $modulePlugin = $this->getModuleHelper();
        $entityPlugin = $modulePlugin->getEntityHelper();
        $context = $modulePlugin->setRealContext($contextProgress)->getModule();
        $entity = $entityPlugin->setContext($item = $contextProgress->getItem())->getEntity();

        if ($this->getEntityHelper()->isNew($item)) { // @todo Щоб уникнути небажаного flush реалізувати single_table або розібратись у Statusable (від Taggable, Sortable etc.)
            if (!$om->contains($item)) {
                $om->persist($item);
            }
            $om->flush();
        }

        $createdAt = new DateTime('now');

        /** @var Progress $progress */
        $progress = $this->getObjectModel();
        $progress->setMessage($contextProgress->getMessage())
            ->setDescription($contextProgress->getDescription())
            ->setItemId($item->getId())
            ->setUser($contextProgress->getUser())
            ->setContext($context)
            ->setEntity($entity)
            ->setCreatedAt($createdAt)
            ->setSnippet(serialize($item))
            ->setExtra($contextProgress->getExtra());

        $om->persist($progress);

        $this->getEventManager()->trigger('write', $progress, ['context' => $this, 'item' => $item]);

        #$om->flush();
        return $progress;
    }

    /**
     * @param object|[] $item
     * @return Entity[]
     */
    protected function getEntities($item)
    {
        $om = $this->getObjectManager();
        $modulePlugin = $this->getModuleHelper();
        $entityPlugin = $modulePlugin->getEntityHelper();
        $items = is_array($item) ? $item : [$item];
        $itemNames = [];
        foreach ($items as $item) {
            $itemNames[] = $entityPlugin->setContext($item)->getContext();
        }
        $entities = $om->getRepository(Entity::class)->findBy(['namespace' => $itemNames]);

        return $entities;
    }
}