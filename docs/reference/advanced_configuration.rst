Advanced Configuration
======================

.. code-block:: yaml

    # config/packages/sonata_news.yaml

    sonata_news:
        title:        Sonata Project
        link:         https://sonata-project.org
        description:  Cool bundles on top of Symfony2
        salt:         'secureToken'
        permalink_generator: sonata.news.permalink.date # sonata.news.permalink.collection
        permalink:
            date:     '%%1$04d/%%2$02d/%%3$02d/%%4$s' # => 2012/02/01/slug
        comment:
            notification:
                emails:   [email@example.org, email2@example.org]
                from:     no-reply@sonata-project.org
                template: '@SonataNews/Mail/comment_notification.txt.twig'

        class:
            post:       Application\Sonata\NewsBundle\Entity\Post
            comment:    Application\Sonata\NewsBundle\Entity\Comment
            media:      Application\Sonata\MediaBundle\Entity\Media
            user:       Application\Sonata\UserBundle\Entity\User
            tag:        Application\Sonata\ClassificationBundle\Entity\Tag
            collection: Application\Sonata\ClassificationBundle\Entity\Collection

        admin:
            post:
                class:       Sonata\NewsBundle\Admin\PostAdmin
                controller:  SonataAdminBundle:CRUD
                translation: SonataNewsBundle
            comment:
                class:       Sonata\NewsBundle\Admin\CommentAdmin
                controller:  SonataNewsBundle:CommentAdmin
                translation: SonataNewsBundle

.. code-block:: yaml

    # config/packages/sonata_classification.yaml

    sonata_classification:
        class:
            collection: Application\Sonata\ClassificationBundle\Entity\Collection
            tag:        Application\Sonata\ClassificationBundle\Entity\Tag
            category:   Application\Sonata\ClassificationBundle\Entity\Category

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
