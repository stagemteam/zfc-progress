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

    const MNEMO = 'statusProgress';

    const TABLE = 'progress';

    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", nullable=false, options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var DateTime
     * @ORM\Column(name="createdAt", type="datetime")
     */
    protected $createdAt;

    /**
     * @var string
     * @ORM\Column(name="snippet", type="text", nullable=false)
     */
    protected $snippet;

    /**
     * @var array
     * @ORM\Column(name="extra", type="json")
     */
    protected $extra;

    /**
     * @var string
     * @ORM\Column(name="message", type="text", nullable=false)
     */
    protected $message;

    /**
     * @var string
     * @ORM\Column(name="description", type="text", nullable=false)
     */
    protected $description;

    /**
     * @var DateTime
     * @ORM\Column(name="statusedAt", type="datetime", nullable=true)
     */
    protected $statusedAt;

    /**
     * @var Status
     * @ORM\ManyToOne(targetEntity="Stagem\ZfcStatus\Model\Status")
     * @ORM\JoinColumn(name="statusId", referencedColumnName="id", nullable=true)
     */
    protected $status;

    /**
     * Executed module context
     *
     * @var Module
     * @ORM\ManyToOne(targetEntity="Popov\ZfcEntity\Model\Module")
     * @ORM\JoinColumn(name="contextId", referencedColumnName="id")
     */
    protected $context;

    /**
     * Item (ID) which has modification
     *
     * @var integer
     * @ORM\Column(name="itemId", type="integer", nullable=false)
     */
    protected $itemId;

    /**
     * Registered system entity
     *
     * @var Entity
     * @ORM\ManyToOne(targetEntity="Popov\ZfcEntity\Model\Entity")
     * @ORM\JoinColumn(name="entityId", referencedColumnName="id")
     */
    protected $entity;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="Popov\ZfcUser\Model\User")
     * @ORM\JoinColumn(name="userId", referencedColumnName="id", nullable=true)
     */
    protected $user;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return Progress
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $createdAt
     * @return Progress
     */
    public function setCreatedAt(DateTime $createdAt): Progress
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return string
     */
    public function getSnippet()
    {
        return $this->snippet;
    }

    /**
     * @param string $snippet
     * @return Progress
     */
    public function setSnippet($snippet)
    {
        $this->snippet = $snippet;

        return $this;
    }

    /**
     * @API\Field(type="Stagem\GraphQL\Type\JsonType")
     * @return array
     */
    public function getExtra(): array
    {
        return $this->extra;
    }

    /**
     * @param array $extra
     * @return Progress
     */
    public function setExtra(array $extra): Progress
    {
        $this->extra = $extra;

        return $this;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return Progress
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Progress
     */
    public function setDescription(string $description): Progress
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return null|DateTime
     */
    public function getStatusedAt(): ?DateTime
    {
        return $this->statusedAt;
    }

    /**
     * @param DateTime $statusedAt
     * @return Progress
     */
    public function setStatusedAt(DateTime $statusedAt): Progress
    {
        $this->statusedAt = $statusedAt;

        return $this;
    }

    /**
     * @return null|Status
     */
    public function getStatus(): ?Status
    {
        return $this->status;
    }

    /**
     * @param Status $status
     * @return Progress
     */
    public function setStatus(Status $status): Progress
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Module
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @param Module $context
     * @return Progress
     */
    public function setContext(Module $context)
    {
        $this->context = $context;

        return $this;
    }

    /**
     * @return int
     */
    public function getItemId()
    {
        return $this->itemId;
    }

    /**
     * @param int $itemId
     * @return Progress
     */
    public function setItemId($itemId)
    {
        $this->itemId = $itemId;

        return $this;
    }

    /**
     * @return Entity
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param Entity $entity
     * @return Progress
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return Progress
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }
}
