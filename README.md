VisualCraftMailerBundle
=======================

Installation
------------

### Step 1: Install the Bundle

    $ composer visual-craft/mailer-bundle

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
            // configure options that shoul be provided to buildMessage method
            $optionsResolver->setRequired(['to']);
        }

        public function buildMessage(\Swift_Message $message, array $options)
        {
            // buld message
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

License
-------

This bundle is released under the MIT license. See the complete license in the file:

    LICENSE
