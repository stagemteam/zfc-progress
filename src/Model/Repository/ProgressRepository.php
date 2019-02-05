<?php
namespace Stagem\ZfcProgress\Model\Repository;

use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Popov\ZfcCore\Model\Repository\EntityRepository;

class ProgressRepository extends EntityRepository {

    protected $_alias = 'statusProgress';

    public function getItemProgress($item, $entity) {
        return $this->getItemsProgress([$item], [$entity]);
    }

    public function getItemsProgress($items, $entities) {
        $u = 'user';
        //$s = 'status';

        $qb = $this->createQueryBuilder($this->_alias)
            //->leftJoin($this->_alias . '.status', $s)
            ->leftJoin($this->_alias . '.user', $u);

        $qb->where(
            $qb->expr()->andX(
                $qb->expr()->in($this->_alias . '.entity', '?1'),
                $qb->expr()->in($this->_alias . '.itemId', '?2')
            )
        );

        $qb->orderBy($this->_alias . '.createdAt', 'ASC');

        $qb->setParameters([1 => $entities, 2 => $items]);
        //$query = $qb->getQuery();
        //\Zend\Debug\Debug::dump([$query->getSql(), $query->getParameters()]); die(__METHOD__);

        return $qb;
    }

    public function getItemProgressByContext($item, $entity, $context) {
        $u = 'user';

        $qb = $this->createQueryBuilder($this->_alias)
            ->leftJoin($this->_alias . '.user', $u);

        $qb->where(
            $qb->expr()->andX(
                $qb->expr()->in($this->_alias . '.entity', '?1'),
                $qb->expr()->in($this->_alias . '.itemId', '?2'),
                $qb->expr()->in($this->_alias . '.context', '?3')
            )
        );

        $qb->orderBy($this->_alias . '.createdAt', 'ASC');

        $qb->setParameters([1 => $entity, 2 => $item, 3 => $context]);

        return $qb;
    }

}