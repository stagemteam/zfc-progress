<?php
/**
 * @category Agere
 * @package Agere_Progress
 * @author Sergiy Popov <popov@agere.com.ua>
 * @datetime: 29.03.2016 23:14
 */
namespace Stagem\ZfcProgress\Model;

use DateTime;
use GraphQL\Doctrine\Annotation as API;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Popov\ZfcCore\Model\DomainAwareTrait;
use Popov\ZfcEntity\Model\Entity;
use Popov\ZfcEntity\Model\Module;
use Popov\ZfcUser\Model\User;
use Stagem\ZfcStatus\Model\Status;

/**
 * @ORM\Entity(repositoryClass="Stagem\ZfcProgress\Model\Repository\ProgressRepository")
 * @ORM\Table(name="progress", indexes={
 *  @ORM\Index(name="FK_ProgressEntityId", columns={"entityId", "itemId"}),
 *  @ORM\Index(name="FK_ProgressContextEntityId", columns={"contextId", "entityId", "itemId"})
 * })
 */
class Progress
{
    use DomainAwareTrait;

    use ProgressTrait;

    const MNEMO = 'progress';

    const TABLE = 'progress';

}
