Advanced Configuration
======================

.. code-block:: yaml

    # config/packages/sonata_news.yaml

    sonata_news:
        title: Sonata Project
        link: https://sonata-project.org
        description: Cool bundles on top of Symfony2
        salt: 'secureToken'
        permalink_generator: sonata.news.permalink.date # sonata.news.permalink.collection
        permalink:
            date: '%%1$04d/%%2$02d/%%3$02d/%%4$s' # => 2012/02/01/slug
        db_driver: 'no_driver'
        comment:
            notification:
                emails: [email@example.org, email2@example.org]
                from: no-reply@sonata-project.org
                template: '@SonataNews/Mail/comment_notification.txt.twig'

        class:
            post: App\Entity\SonataNewsPost
            comment: App\Entity\SonataNewsComment
            media: App\Entity\SonataMediaMedia
            user: App\Entity\SonataUserUser
            tag: App\Entity\SonataClassificationTag
            collection: App\Entity\SonataClassificationCollection

        admin:
            post:
                class: Sonata\NewsBundle\Admin\PostAdmin
                controller: SonataAdminBundle:CRUD
                translation: SonataNewsBundle
            comment:
                class: Sonata\NewsBundle\Admin\CommentAdmin
                controller: SonataNewsBundle:CommentAdmin
                translation: SonataNewsBundle

.. code-block:: yaml

    # config/packages/sonata_classification.yaml

    sonata_classification:
        class:
            collection: Application\Sonata\ClassificationBundle\Entity\Collection
            tag: Application\Sonata\ClassificationBundle\Entity\Tag
            category: Application\Sonata\ClassificationBundle\Entity\Category

.. code-block:: yaml

    # config/packages/jms_serializer.yaml

    jms_serializer:
        metadata:
            directories:
                - { name: 'sonata_datagrid', path: "%kernel.project_dir%/vendor/sonata-project/datagrid-bundle/src/Resources/config/serializer", namespace_prefix: 'Sonata\DatagridBundle' }
                - { name: 'sonata_news', path: "%kernel.project_dir%/vendor/sonata-project/news-bundle/src/Resources/config/serializer", namespace_prefix: 'Sonata\NewsBundle' }

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
                        ApplicationSonataNewsBundle: ~
                        SonataNewsBundle: ~
