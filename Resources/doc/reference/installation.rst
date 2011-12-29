Installation
============

* Add SonataNewsBundle to your vendor/bundles dir with the deps file::

.. code-block::

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
        git=git@github.com:sonata-project/SonataUserBundle.git
        target=/bundles/Sonata/UserBundle

    [SonataAdminBundle]
        git=git@github.com:sonata-project/SonataAdminBundle.git
        target=/bundles/Sonata/AdminBundle

    [SonataFormatterBundle]
        git=git@github.com:sonata-project/SonataFormatterBundle.git
        target=/bundles/Sonata/FormatterBundle

    [SonataIntlBundle]
        git=git@github.com:sonata-project/SonataIntlBundle.git
        target=/bundles/Sonata/IntlBundle

    [KnpMarkdownBundle]
        git=git@github.com:knplabs/KnpMarkdownBundle.git
        target=/bundles/Knp/Bundle/MarkdownBundle

* Add SonataNewsBundle to your application kernel::

.. code-block:: php

    // app/AppKernel.php
    public function registerBundles()
    {
        return array(
            // ...
            new Sonata\NewsBundle\SonataNewsBundle(),
            new Sonata\UserBundle\SonataUserBundle(),
            new Sonata\MediaBundle\SonataMediaBundle(),
            new Sonata\AdminBundle\SonataAdminBundle(),
            new Sonata\IntlBundle\SonataIntlBundle(),
            new Sonata\FormatterBundle\SonataFormatterBundle(),
            new FOS\UserBundle\FOSUserBundle(),
            new Knp\Bundle\MarkdownBundle\KnpMarkdownBundle(),
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
            // ...
        );
    }

* Update the ``autoload.php`` to add a new namespace:

.. code-block:: php

    <?php
    $loader->registerNamespaces(array(
        'Sonata'        => __DIR__,
        'Application'   => __DIR__,
        'FOS'           => __DIR__.'/../vendor/bundles',
        'Knp'           => array(
            __DIR__.'/../vendor/bundles',
            __DIR__.'/../vendor/knp/menu/src'
        ),
        // ... other declarations
    ));


* Run the easy-extends command::

    php app/console sonata:easy-extends:generate SonataNewsBundle
    php app/console sonata:easy-extends:generate SonataUserBundle
    php app/console sonata:easy-extends:generate SonataMediaBundle

* Enable the new bundles::

.. code-block:: php

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

* Add SonataNewsBundle routes to your application routing.yml::

.. code-block:: yaml

    # app/config/routing.yml
    news:
        resource: '@SonataNewsBundle/Resources/config/routing/news.xml'
        prefix: /news

* Add a configuration file : ``sonata_news.yml``::

.. code-block:: yaml

    sonata_news:
        title:        Sonata Project
        link:         http://sonata-project.org
        description:  Cool bundles on top of Symfony2
        salt:         'secureToken'
        permalink_generator: sonata.news.permalink.date # sonata.news.permalink.category

        comment:
            notification:
                emails:   [email@example.org, email2@example.org]
                from:     no-reply@sonata-project.org
                template: 'SonataNewsBundle:Mail:comment_notification.txt.twig'

    doctrine:
        orm:
            entity_managers:
                default:
                    #metadata_cache_driver: apc
                    #query_cache_driver: apc
                    #result_cache_driver: apc
                    mappings:
                        ApplicationSonataNewsBundle: ~
                        SonataNewsBundle: ~

* import the ``sonata_news.yml`` file::

.. code-block:: yaml

    imports:
        - { resource: sonata_news.yml }

* Add a new context into your ``sonata_media.yml`` configuration::

.. code-block:: yaml

    news:
        providers:
            - sonata.media.provider.dailymotion
            - sonata.media.provider.youtube
            - sonata.media.provider.image

        formats:
            small: { width: 150 , quality: 95}
            big:   { width: 500 , quality: 90}

* Define the text formatters available for your blog post::

.. code-block:: yaml

    sonata_formatter:
        formatters:
            markdown:
                service: sonata.formatter.text.markdown
                extensions:
                    - sonata.formatter.twig.control_flow
                    - sonata.formatter.twig.gist
                    - sonata.media.formatter.twig