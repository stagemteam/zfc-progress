<?php
/**
 * The MIT License (MIT)
 * Copyright (c) 2018 Stagem Team
 * This source file is subject to The MIT License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/MIT
 *
 * @category Stagem
 * @package Stagem_ZfcPool
 * @author Serhii Popov <popow.serhii@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Stagem\ZfcProgress\Model;

use DateTime;
use Popov\ZfcEntity\Model\Entity;
use Popov\ZfcEntity\Model\Module;
use Popov\ZfcUser\Model\User;

interface ProgressInterface
{
    public function getId();

    public function setId($id): ProgressInterface;

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime;

    /**
     * @param DateTime $createdAt
     * @return ProgressInterface
     */
    public function setCreatedAt(DateTime $createdAt): ProgressInterface;

    /**
     * @return string
     */
    public function getSnippet();

    /**
     * @param string $snippet
     * @return ProgressInterface
     */
    public function setSnippet($snippet): ProgressInterface;

    /**
     * @return array
     */
    public function getExtra(): array;

    /**
     * @param array $extra
     * @return Progress
     */
    public function setExtra(array $extra): ProgressInterface;

    /**
     * @return string
     */
    public function getMessage();

    /**
     * @param string $message
     * @return Progress
     */
    public function setMessage($message): ProgressInterface;

    /**
     * @return string
     */
    public function getDescription(): string;

    /**
     * @param string $description
     * @return Progress
     */
    public function setDescription(string $description): ProgressInterface;

    /**
     * @return Module
     */
    public function getContext();

    /**
     * @param Module $context
     * @return ProgressInterface
     */
    public function setContext(Module $context): ProgressInterface;

    /**
     * @return int
     */
    public function getItemId();

    /**
     * @param int $itemId
     * @return ProgressInterface
     */
    public function setItemId($itemId): ProgressInterface;

    /**
     * @return Entity
     */
    public function getEntity();

    /**
     * @param Entity $entity
     * @return ProgressInterface
     */
    public function setEntity($entity): ProgressInterface;

    /**
     * @return User|null
     */
    public function getCreatedBy();

    /**
     * @param null|User $user
     * @return ProgressInterface
     */
    public function setCreatedBy(?User $user): ProgressInterface;
}