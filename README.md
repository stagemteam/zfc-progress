# ZF2 Progress Module

This module is part of Agere ecosystem and main goal is logging any change registered in `Context`.

## Requirements
* `Agere\Entity` module
* `Agere\User` module

Logging principle is based on execution context. Conditionally realisation can describe follows (for different modules):
 - item status is changing in `Status Context`;
 - grid is changing in `Grid Context`;
 - mail is sending in `Mail Context`;
 - item is saving in `Saver Context`.
 
You can imagine any other `Context` and describe this in config or even implement custom realisation.

## Usage
Module has low coupling and develop with *Event Driven* in mind.

Base config include three main *action* names which is listened on all interfaces: *edit, change, sync*.
You can extend this list simply add your own interface/action name in config:
```
// config/module.config.php

'progress' => [
    'listeners' => [
        '*' => ['edit', 'change', 'sync']
    ]
]
```

Module has `ContextInterface` for convenience realisation of custom logging **Context**.
```
// config/module.config.php

'progress' => [
	__NAMESPACE__ => [
		'context' => Service\Progress\StatusContext::class,
	]
],
```


## Advanced usage
Suppose, you need implement logging in `Status` context.
For this register new context in `Status` module and declare in services
```php
namespace Agere\Status;

'progress' => [
	__NAMESPACE__ => [
		'context' => Service\Progress\StatusContext::class,
	]
],
'service_manager' => [
	'invokables' => [
		Service\Progress\StatusContext::class => Service\Progress\StatusContext::class,
	],
	'delegators' => [
		Service\Progress\StatusContext::class => [
			\Agere\Translator\Service\Factory\TranslatorDelegatorFactory::class
		]
	],
],
```

Context realisation
```php
namespace Magere\Status\Service\Progress;

use Zend\Mvc\I18n\Translator;
use Zend\I18n\Translator\TranslatorAwareTrait;
use Agere\Progress\Service\ContextInterface;
use Magere\Status\Model\Status;

/**
 * @method Translator getTranslator()
 */
class StatusContext implements ContextInterface
{
    use TranslatorAwareTrait;

    protected $event;

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
        return [
            'newStatusId' => $this->getEvent()->getParam('newStatus')->getId(),
            'oldStatusId' => $this->getEvent()->getParam('oldStatus')->getId(),
        ];
    }

    public function getMessage()
    {
        $translator = $this->getTranslator();
        /** @var Status $newStatus */
        $newStatus = $this->getEvent()->getParam('newStatus');
        /** @var Status $oldStatus */
        $oldStatus = $this->getEvent()->getParam('oldStatus');

        $prefix = $translator->translate(
            'Status change',
            $this->getTranslatorTextDomain(),
            $translator->getFallbackLocale()
        ) . ':';

        $template = $translator->translate(
            '%s from %s to %s',
            $this->getTranslatorTextDomain(),
            $translator->getFallbackLocale()
        );

        return sprintf($template, $prefix, $oldStatus->getName(), $newStatus->getName());
    }
}
```
Following execution rely on `Progress` module with has registered `EditListener` 
and which listen all events `['edit', 'change', 'sync'']` one of which run `Status` module.

Also you simply can register custom event name if pre registered is inopportune:
```
'progress' => [
    'listeners' => [
        Service\Api\InvoiceSoapService::class => ['syncInvoice'],
    ]
],
```