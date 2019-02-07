<?php

namespace Stagem\ZfcProgress\Service;

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

    public function getModulePlugin()
    {
        return $this->modulePlugin;
    }

    public function getEntityPlugin()
    {
        return $this->getModulePlugin()->getEntityHelper();
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
        $modulePlugin = $this->getModulePlugin();
        $entityPlugin = $modulePlugin->getEntityHelper();
        $context = $modulePlugin->setRealContext($contextProgress)->getModule();
        $entity = $entityPlugin->setContext($item = $contextProgress->getItem())->getEntity();


        /** @var Progress $progress */
        $progress = $this->getObjectModel();
        if (!$item->getId()) { // @todo Щоб уникнути небажаного flush реалізувати single_table або розібратись у Statusable (від Taggable, Sortable etc.)
            if (!$om->contains($item)) {
                $om->persist($item);
            }
            $om->flush();
        }
        $progress->setMessage($contextProgress->getMessage())
            ->setDescription($contextProgress->getDescription())
            ->setItemId($item->getId())
            ->setUser($contextProgress->getUser())
            ->setContext($context)
            ->setEntity($entity)
            ->setCreatedAt(new \DateTime('now'))
            ->setSnippet(serialize($item))
            ->setExtra($contextProgress->getExtra());
        $om->persist($progress);

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
        $modulePlugin = $this->getModulePlugin();
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