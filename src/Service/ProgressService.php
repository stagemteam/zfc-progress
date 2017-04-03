<?php
namespace Agere\ZfcProgress\Service;

use Agere\Core\Service\DomainServiceAbstract;
use Magere\Entity\Controller\Plugin\ModulePlugin;
use Agere\ZfcProgress\Model\Repository\ProgressRepository;
use Agere\ZfcProgress\Model\Progress;
use Magere\Entity\Model\Module;
use Magere\Entity\Model\Entity;
use Magere\Users\Model\Users as User;

class ProgressService extends DomainServiceAbstract
{
    protected $entity = Progress::class;

    //protected $user;

    /** @var ModulePlugin */
    protected $modulePlugin;

    public function __construct(/*$user, */ModulePlugin $modulePlugin)
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
        return $this->getModulePlugin()->getEntityPlugin();
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

    public function getProgressByContext($item, ContextInterface $context)
    {
        $entity = $this->getEntities($item);

        /** @var ProgressRepository $repository */
        $repository = $this->getRepository();

        return $repository->getItemProgressByContext($item, $entity, $context);
    }

    public function writeProgress(ContextInterface $contextProgress)
    {
        /** @var \Agere\ZfcDataGrid\Service\Progress\DataGridContext $contextProgress */
        $om = $this->getObjectManager();
        $modulePlugin = $this->getModulePlugin();
        $entityPlugin = $modulePlugin->getEntityPlugin();

        $context = $modulePlugin->setRealContext($contextProgress)->getRealModule();
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
        $entityPlugin = $modulePlugin->getEntityPlugin();

        $items = is_array($item) ? $item : [$item];
        $itemNames = [];
        foreach ($items as $item) {
            $itemNames[] = $entityPlugin->setContext($item)->getContext();
        }

        $entities = $om->getRepository(Entity::class)->findBy(['namespace' => $itemNames]);

        return $entities;
    }
}