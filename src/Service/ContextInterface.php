<?php
/**
 * Context interface for getting message
 *
 * @category Agere
 * @package Agere_Progress
 * @author Popov Sergiy <popov@agere.com.ua>
 * @datetime: 06.11.2016 13:36
 */
namespace Stagem\ZfcProgress\Service;

use Zend\I18n\Translator\TranslatorAwareInterface;
use Popov\ZfcUser\Service\UserAwareInterface;

interface ContextInterface extends UserAwareInterface, TranslatorAwareInterface
{
    /**
     * Set raised event
     *
     * @param $event
     * @return self
     */
    public function setEvent($event);

    /**
     * Get raised event
     *
     * @return object
     */
    public function getEvent();

    /**
     * Related item
     *
     * @return object
     */
    public function getItem();

    /**
     * Additional data for progress (logging)
     *
     * @return array
     */
    public function getExtra();

    /**
     * Progress message
     *
     * @return string
     */
    public function getMessage();

    /**
     * Progress description
     *
     * @return string
     */
    public function getDescription();

}