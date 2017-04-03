<?php
/**
 * Enter description here...
 *
 * @category Agere
 * @package Agere_<package>
 * @author Popov Sergiy <popov@agere.com.ua>
 * @datetime: 24.03.2017 22:54
 */
namespace AgereTest\Progress\Fake;

use Mockery;
use Zend\EventManager\Event;
use Zend\I18n\Translator\TranslatorAwareTrait;
use Agere\ZfcProgress\Service\ContextInterface;
use Magere\Users\Service\UserAwareTrait;
use Magere\Users\Model\Users as User;

class ProgressContextFake implements ContextInterface
{
    use TranslatorAwareTrait;
    use UserAwareTrait;

    /** @var Event */
    protected $event;

    public function __construct()
    {
        $this->event = new Event(
            'fakeEvent',
            new ModelStub(),
            ['context' => new ServiceStub()]
        );

        $this->user = Mockery::mock(User::class);
    }

    public function setEvent($event)
    {
        $this->event = $event;
    }

    public function getEvent()
    {
        return $this->event;
    }

    public function getItem()
    {
        return $this->event->getTarget();
    }

    public function getExtra()
    {
        return [];
    }

    public function getMessage()
    {
        return 'Test log message';
    }
}