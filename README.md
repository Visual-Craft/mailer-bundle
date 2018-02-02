VisualCraftMailerBundle
=======================

[![Build Status](https://travis-ci.org/Visual-Craft/mailer-bundle.svg?branch=master)](https://travis-ci.org/Visual-Craft/mailer-bundle)

Symfony bundle which provides high-level API for emails creation and sending


Installation
------------

### Step 1: Install the VisualCraftMailerBundle

    $ composer require visual-craft/mailer-bundle

### Step 2: Enable the VisualCraftMailerBundle
```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new VisualCraft\Bundle\MailerBundle\VisualCraftMailerBundle(),
        );
        // ...
    }
    // ...
}
```

Usage
-----

### Create mail type class

```php
<?php

namespace AppBundle\MailType;

use VisualCraft\Bundle\MailerBundle\MailType\MailTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationMailType implements MailTypeInterface
{
    public function configureOptions(OptionsResolver $optionsResolver)
    {
        // configure options which should be provided to buildMessage method
        $optionsResolver->setRequired(['to']);
    }

    public function buildMessage(\Swift_Message $message, array $options)
    {
        // build message
        $message
            ->setSubject('Registration')
            ->setTo($options['to'])
        ;
    }
}
```

### Register mail type service
```yaml
# services.yml
services:
    app.mail_type.rgistration:
        class: AppBundle\MailType\RegistrationMailType
        tags:
            - { name: 'visual_craft_mailer.mail_type' }
```

### Send email
```php
<?php

use AppBundle\MailType\RegistrationMailType;

$mailer = $this->container->get('visual_craft_mailer.mailer');
$mailer->send(RegistrationMailType::class, [
    'to' => 'user@example.com',
]);
```

### Mail type

By default you need to use mail type class as 1st argument for `$mailer->send` method, to change this you should do the following:
```yaml
# services.yml
services:
    app.mail_type.rgistration:
        class: AppBundle\MailType\RegistrationMailType
        tags:
            # note for additional tag attribute 'type':
            - { name: 'visual_craft_mailer.mail_type', type: 'registration' }
```
```php
<?php

$mailer->send('registration', [
    'to' => 'user@example.com',
]);
```


### Generating mail body and subject using twig templates
In order to simplify usage of twig for rendering mail body/subject you should do 2 things:
 * Implement `VisualCraft\Bundle\MailerBundle\TwigAwareInterface` by your `MailType`, bundle will automatically inject twig service into you mail type service using method call `setTwig`.
 * Use trait `VisualCraft\Bundle\MailerBundle\TwigMailRendererTrait` by your `MailType` which will add `setTwig`, `renderBody` and `renderSubject` methods.

Example:
```php
<?php

namespace AppBundle\MailType;

use VisualCraft\Bundle\MailerBundle\TwigAwareInterface;
use VisualCraft\Bundle\MailerBundle\TwigMailRendererTrait;
use VisualCraft\Bundle\MailerBundle\MailType\MailTypeInterface;

class RegistrationMailType implements MailTypeInterface, TwigAwareInterface
{
    use TwigMailRendererTrait;

    // ...

    /**
     * {@inheritdoc}
     */
    public function buildMessage(\Swift_Message $message, array $options)
    {
        // ...

        $message
            // use twig to render subject
            ->setSubject($this->renderSubject('mail/registration_subject.html.twig', [
                'variable' => 'value',
            ]))
            // use twig to render body
            ->setBody($this->renderBody('mail/registration_body.html.twig', [
                'variable' => 'value',
            ]))
        ;

        // ...
    }
}
```

Tests
-----
```sh
$ composer install
$ vendor/bin/phpunit
```

License
-------

This bundle is released under the MIT license. See the complete license in the file: `LICENSE`
