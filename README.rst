SonataNewsBundle
================

A blog platform based on Doctrine2 and Symfony2.

Installation
------------

* Add the following entry to ``deps`` the run ``php bin/vendors install``::

    [FOSUserBundle]
        git=git://github.com/FriendsOfSymfony/FOSUserBundle.git
        target=/bundles/FOS/UserBundle

    [SonataNewsBundle]
        git=git@github.com:sonata-project/SonataNewsBundle.git
        target=/bundles/Sonata/NewsBundle

    [SonataMediaBundle]
        git=git@github.com:sonata-project/SonataMediaBundle.git
        target=/bundles/Sonata/MediaBundle

    [SonataUserBundle]
        git=https://github.com/sonata-project/SonataUserBundle.git
        target=/bundles/Sonata/UserBundle

    [SonataAdminBundle]
        git=git@github.com:sonata-project/SonataAdminBundle.git
        target=/bundles/Sonata/AdminBundle

    [SonataFormatterBundle]
        git=http://github.com/sonata-project/SonataFormatterBundle.git
        target=/bundles/Sonata/FormatterBundle

    [KnpMarkdownBundle]
        git=http://github.com/knplabs/KnpMarkdownBundle.git
        target=/bundles/Knp/Bundle/MarkdownBundle

* Register bundles in ``app/AppKernel.php``::

    <?php

    // app/AppKernel.php
    public function registerBundles()
    {
        return array(
            // ...
            new Sonata\NewsBundle\SonataNewsBundle(),
            new Sonata\UserBundle\SonataUserBundle(),
            new Sonata\MediaBundle\SonataMediaBundle(),
            new Sonata\FormatterBundle\SonataFormatterBundle(),
            new FOS\UserBundle\FOSUserBundle(),
            new Knp\Bundle\KnpMarkdownBundle()
            // ...
        );
    }

* Register namespace in ``app/autoload.php``::

    $loader->registerNamespaces(array(
        // ...
        'Knp'              => __DIR__.'/../vendor/bundles',
        'Sonata'           => __DIR__.'/../vendor/bundles',
    ));

* Run the easy-extends command::

    php app/console sonata:easy-extends:generate SonataNewsBundle
    php app/console sonata:easy-extends:generate SonataUserBundle
    php app/console sonata:easy-extends:generate SonataMediaBundle

* Enable the new bundles::

    // app/AppKernel.php
    public function registerBundles()
    {
        return array(
            // ...
            new Application\Sonata\NewsBundle\SonataNewsBundle(),
            new Application\Sonata\UserBundle\SonataUserBundle(),
            new Application\Sonata\UserBundle\SonataMediaBundle(),
            // ...
        );
    }

* Complete the FOS/UserBundle install and use the ``Application\Sonata\UserBundle\Entity\User`` as the user class

Configuration
-------------

* Add SonataNewsBundle routes to your application routing.yml::

    # app/config/routing.yml
    news:
        resource: '@SonataNewsBundle/Resources/config/routing/news.xml'
        prefix: /news

* Add a new context into your ``sonata_media`` configuration::

    sonata_media:
        # [...]
        news:
            providers:
                - sonata.media.provider.dailymotion
                - sonata.media.provider.youtube
                - sonata.media.provider.image

            formats:
                small: { width: 150 , quality: 95}
                big:   { width: 500 , quality: 90}

* Define the text formatters available for your blog post::

    sonata_formatter:
        formatters:
            markdown:
                service: sonata.formatter.text.markdown
                extensions: []

            text:
                service: sonata.formatter.text.text
                extensions: []

            raw:
                service: sonata.formatter.text.raw
                extensions: []
