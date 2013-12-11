Installation
============

* Add SonataNewsBundle to your vendor/bundles dir with the deps file::

.. code-block:: php

    //composer.json
    "require": {
    //...
        "sonata-project/news-bundle": "dev-master",
        "sonata-project/doctrine-orm-admin-bundle": "dev-master",
        "sonata-project/easy-extends-bundle": "dev-master",
    //...
    }


* Add SonataNewsBundle to your application kernel::

.. code-block:: php

    // app/AppKernel.php
    public function registerBundles()
    {
        return array(
            // ...
            new Sonata\CoreBundle\SonataCoreBundle(),
            new Sonata\MarkItUpBundle\SonataMarkItUpBundle(),
            new Ivory\CKEditorBundle\IvoryCKEditorBundle(),
            new Sonata\NewsBundle\SonataNewsBundle(),
            new Sonata\UserBundle\SonataUserBundle(),
            new Sonata\MediaBundle\SonataMediaBundle(),
            new Sonata\AdminBundle\SonataAdminBundle(),
            new Sonata\IntlBundle\SonataIntlBundle(),
            new Sonata\FormatterBundle\SonataFormatterBundle(),
            new Sonata\ClassificationBundle\SonataClassificationBundle(),
            new FOS\UserBundle\FOSUserBundle(),
            new Knp\Bundle\MarkdownBundle\KnpMarkdownBundle(),
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle(),
            new Sonata\EasyExtendsBundle\SonataEasyExtendsBundle(),
            // ...
        );
    }

* Create a configuration file : ``sonata_news.yml``::

.. code-block:: yaml

    sonata_news:
        title:        Sonata Project
        link:         http://sonata-project.org
        description:  Cool bundles on top of Symfony2
        salt:         'secureToken'
        permalink_generator: sonata.news.permalink.date # sonata.news.permalink.collection

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

* import the ``sonata_news.yml`` file and enable json type for doctrine ::

.. code-block:: yaml

    imports:
        #...
        - { resource: sonata_news.yml }
    #...
    doctrine:
        dbal:
        # ...
            types:
                json: Sonata\Doctrine\Types\JsonType

* Add a new context into your ``sonata_media.yml`` configuration if you don't have go there http://sonata-project.org/bundles/media/master/doc/reference/installation.html::

.. code-block:: yaml

    news:
        providers:
            - sonata.media.provider.dailymotion
            - sonata.media.provider.youtube
            - sonata.media.provider.image

        formats:
            small: { width: 150 , quality: 95}
            big:   { width: 500 , quality: 90}

* create configuration file sonata_formatter.yml the text formatters available for your blog post::

.. code-block:: yaml

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


* Run the easy-extends command::

    php app/console sonata:easy-extends:generate SonataNewsBundle
    php app/console sonata:easy-extends:generate SonataUserBundle
    php app/console sonata:easy-extends:generate SonataMediaBundle

* If the bundle is generated in /app cut application folder and paste it in src/
* Enable the new bundles::

.. code-block:: php

    // app/AppKernel.php
    public function registerBundles()
    {
        return array(
            // ...
            new Application\Sonata\NewsBundle\ApplicationSonataNewsBundle(),
            new Application\Sonata\UserBundle\ApplicationSonataUserBundle(),
            new Application\Sonata\MediaBundle\ApplicationSonataMediaBundle(),
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

