<?php
/**
 * The MIT License (MIT)
 * Copyright (c) 2019 Serhii Popov
 * This source file is subject to The MIT License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/MIT
 *
 * @category Popov
 * @package Popov_<package>
 * @author Serhii Popov <popow.serhii@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Stagem\ZfcProgress\Model;

use DateTime;
use GraphQL\Doctrine\Annotation as API;
use Doctrine\ORM\Mapping as ORM;
use Popov\ZfcEntity\Model\Entity;
use Popov\ZfcEntity\Model\Module;
use Popov\ZfcUser\Model\User;

trait ProgressTrait
{
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
     * @ORM\JoinColumn(name="createdBy", referencedColumnName="id", nullable=true)
     */
    protected $createdBy;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return ProgressInterface
     */
    public function setId($id): ProgressInterface
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
     * @return ProgressInterface
     */
    public function setCreatedAt(DateTime $createdAt): ProgressInterface
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
     * @return ProgressInterface
     */
    public function setSnippet($snippet): ProgressInterface
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
     * @return ProgressInterface
     */
    public function setExtra(array $extra): ProgressInterface
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
     * @return ProgressInterface
     */
    public function setMessage($message): ProgressInterface
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
     * @return ProgressInterface
     */
    public function setDescription(string $description): ProgressInterface
    {
        $this->description = $description;

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
     * @return ProgressInterface
     */
    public function setContext(Module $context): ProgressInterface
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
     * @return ProgressInterface
     */
    public function setItemId($itemId): ProgressInterface
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
     * @return ProgressInterface
     */
    public function setEntity($entity): ProgressInterface
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @param null|User $createdBy
     * @return ProgressInterface
     */
    public function setCreatedBy(?User $createdBy): ProgressInterface
    {
        $this->createdBy = $createdBy;

        return $this;
    }
}