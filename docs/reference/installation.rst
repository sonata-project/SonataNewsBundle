Installation
============

Download the Bundle(s)
----------------------

.. code-block:: bash

    composer require sonata-project/news-bundle

.. code-block:: bash

     composer require sonata-project/doctrine-orm-admin-bundle

If you want to use the API, you also need ``friendsofsymfony/rest-bundle`` and ``nelmio/api-doc-bundle``.

.. code-block:: bash

    composer require nelmio/api-doc-bundle

    composer require friendsofsymfony/rest-bundle

Enable the Bundle(s)
--------------------

Then, enable the bundle by adding it to the list of registered bundles
in ``bundles.php`` file of your project::

    // config/bundles.php

    return [
        // ...
        Sonata\Form\Bridge\Symfony\Bundle\SonataFormBundle::class => ['all' => true],
        Sonata\Doctrine\Bridge\Symfony\Bundle\SonataDoctrineBundle::class => ['all' => true],
        Ivory\CKEditorBundle\IvoryCKEditorBundle::class => ['all' => true],
        Sonata\NewsBundle\SonataNewsBundle::class => ['all' => true],
        Sonata\BlockBundle\SonataBlockBundle::class => ['all' => true],
        Sonata\MediaBundle\SonataMediaBundle::class => ['all' => true],
        Sonata\AdminBundle\SonataAdminBundle::class => ['all' => true],
        Sonata\IntlBundle\SonataIntlBundle::class => ['all' => true],
        Sonata\FormatterBundle\SonataFormatterBundle::class => ['all' => true],
        Sonata\ClassificationBundle\SonataClassificationBundle::class => ['all' => true],
        Knp\Bundle\MarkdownBundle\KnpMarkdownBundle::class => ['all' => true],
        Knp\Bundle\MenuBundle\KnpMenuBundle::class => ['all' => true],
        Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle::class => ['all' => true],
        JMS\SerializerBundle\JMSSerializerBundle::class => ['all' => true],
        Sonata\TranslationBundle\SonataTranslationBundle::class => ['all' => true],
        Sonata\EasyExtendsBundle\SonataEasyExtendsBundle::class => ['dev' => true],
    ];

.. note::

    `You need to setup SonataBlockBundle first. <https://sonata-project.org/bundles/block/master/doc/reference/installation.html>`_

Default configuration
---------------------

.. code-block:: yaml

    # config/packages/sonata_news.yaml

    sonata_news:
        title:        Sonata Project
        link:         https://sonata-project.org
        description:  Cool bundles on top of Symfony2
        salt:         'secureToken'
        permalink_generator: sonata.news.permalink.date # sonata.news.permalink.collection
        db_driver:    'no_driver' # doctrine_orm or doctrine_mongodb it is mandatory to choose one here

        comment:
            notification:
                emails:   [email@example.org, email2@example.org]
                from:     no-reply@sonata-project.org
                template: '@SonataNews/Mail/comment_notification.txt.twig'

.. code-block:: yaml

    # config/packages/doctrine.yaml

    doctrine:
        orm:
            entity_managers:
                default:
                    #metadata_cache_driver: apc
                    #query_cache_driver: apc
                    #result_cache_driver: apc
                    mappings:
                        #ApplicationSonataNewsBundle: ~
                        SonataNewsBundle: ~

* Define default ``news`` FOS CKEditor configuration

.. code-block:: yaml

    # config/packages/fos_ckeditor.yaml

    fos_ck_editor:
        configs:
            news: ~

* Add a new context into your ``sonata_media.yaml`` configuration if you don't have go there https://sonata-project.org/bundles/media/master/doc/reference/installation.html:

.. code-block:: yaml

    # config/packages/sonata_media.yaml

    news:
        providers:
            - sonata.media.provider.dailymotion
            - sonata.media.provider.youtube
            - sonata.media.provider.image

        formats:
            small: { width: 150 , quality: 95}
            big:   { width: 500 , quality: 90}

* Create configuration file ``sonata_formatter.yaml`` the text formatters available for your blog post:

.. code-block:: yaml

    # config/packages/sonata_formatter.yaml

    sonata_formatter:
        formatters:
            markdown:
                service: sonata.formatter.text.markdown
                extensions:
                    - sonata.formatter.twig.control_flow
                    - sonata.formatter.twig.gist
                    - sonata.media.formatter.twig

            text:
                service: sonata.formatter.text.text
                extensions:
                    - sonata.formatter.twig.control_flow
                    - sonata.formatter.twig.gist
                    - sonata.media.formatter.twig

            rawhtml:
                service: sonata.formatter.text.raw
                extensions:
                    - sonata.formatter.twig.control_flow
                    - sonata.formatter.twig.gist
                    - sonata.media.formatter.twig

            richhtml:
                service: sonata.formatter.text.raw
                extensions:
                    - sonata.formatter.twig.control_flow
                    - sonata.formatter.twig.gist
                    - sonata.media.formatter.twig

Generate the application bundles
--------------------------------

.. code-block:: bash

    bin/console sonata:easy-extends:generate SonataNewsBundle -d src
    bin/console sonata:easy-extends:generate SonataMediaBundle -d src
    bin/console sonata:easy-extends:generate SonataClassificationBundle -d src

Enable the application bundles
------------------------------

.. code-block:: php

    // config/bundles.php

    return [
        // ...
        App\Application\Sonata\NewsBundle\ApplicationSonataNewsBundle::class => ['all' => true],
        App\Application\Sonata\MediaBundle\ApplicationSonataMediaBundle::class => ['all' => true],
        App\Application\Sonata\ClassificationBundle\ApplicationSonataClassificationBundle::class => ['all' => true],
    ];

Uncomment the ApplicationSonataNewsBundle mapping
-------------------------------------------------

.. code-block:: yaml

    # config/packages/sonata_news.yaml

    doctrine:
        orm:
            entity_managers:
                default:
                    # ...
                    mappings:
                        ApplicationSonataNewsBundle: ~
                        SonataNewsBundle: ~

Update Database Schema
----------------------

.. code-block:: bash

    bin/console doctrine:schema:update --force

Add SonataNewsBundle routes
---------------------------

.. code-block:: yaml

    # config/packages/routes.yaml

    news:
        resource: '@SonataNewsBundle/Resources/config/routing/news.xml'
        prefix: /news
