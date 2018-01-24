VisualCraftMailerBundle
=======================

Symfony framework bundle, which provides high-level API for sending emails using Swiftmailer


Installation
------------

### Step 1: Install the Bundle

    $ composer require visual-craft/mailer-bundle

### Step 2: Enable the Bundle

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

Usage
-----

### Create handler

    <?php

    namespace AppBundle\MailHandler;

    use VisualCraft\Bundle\MailerBundle\MailHandlerInterface;
    use Symfony\Component\OptionsResolver\OptionsResolver;

    class RegistrationMailHandler implements MailHandlerInterface
    {
        public function configureOptions(OptionsResolver $optionsResolver)
        {
            // configure options that should be provided to buildMessage method
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

### Register handler service

    # services.yml
    services:
        app.mail_handler.rgistration:
            class: AppBundle\MailHandler\RegistrationMailHandler
            tags:
                - { name: 'visual_craft_mailer.mail_handler', alias: 'registration' }

### Send email

    $mailer = $this->container->get('visual_craft_mailer.mailer');
    $mailer->send('registration', ['to' => 'user@example.com']);

### Generating mail body and subject with twig
Symfony uses twig templates engine by default. To simplify injection of twig dependency to the MailerHandler, you can implement interface ```\VisualCraft\Bundle\MailerBundle\MailHandler\TwigAwareMailHandlerInterface``` in your MailHandler (you can use ```\VisualCraft\Bundle\MailerBundle\MailHandler\TwigAwareMailHandlerTrait``` for methods implementation). ```TwigAwareMailHandlerTrait``` also has methods to generate body and subject.


Example handler
```php
<?php

namespace AppBundle\MailHandler;

use VisualCraft\Bundle\MailerBundle\MailHandler\TwigAwareMailHandlerInterface;
use VisualCraft\Bundle\MailerBundle\MailHandler\TwigAwareMailHandlerTrait;
use VisualCraft\Bundle\MailerBundle\MailHandlerInterface;

class RegistrationMailHandler implements MailHandlerInterface, TwigAwareMailHandlerInterface
{
   use TwigAwareMailHandlerTrait;

   /**
    * {@inheritdoc}
    */
   public function buildMessage(\Swift_Message $message, array $options)
   {
       // build message
       $message
           // use twig for render subject
           ->setSubject($this->renderSubject('mail\registration_subject.html.twig', ['variable' => 'value']))
           // use twig for render body
           ->setBody($this->renderBody('mail\registration_body.html.twig', ['variable' => 'value']))
       ;
   }
}
```
License
-------

This bundle is released under the MIT license. See the complete license in the file:

    LICENSE
