Advanced Configuration
======================


.. code-block:: yaml

    sonata_news:
        title:        Sonata Project
        link:         http://sonata-project.org
        description:  Cool bundles on top of Symfony2
        salt:         'secureToken'
        permalink_generator: sonata.news.permalink.date # sonata.news.permalink.collection
        permalink:
            date:     %%1$04d/%%2$02d/%%3$02d/%%4$s => 2012/02/01/slug
        comment:
            notification:
                emails:   [email@example.org, email2@example.org]
                from:     no-reply@sonata-project.org
                template: 'SonataNewsBundle:Mail:comment_notification.txt.twig'

        class:
            post:       Application\Sonata\NewsBundle\Entity\Post
            tag:        Application\Sonata\NewsBundle\Entity\Tag
            comment:    Application\Sonata\NewsBundle\Entity\Comment
            collection:   Application\Sonata\NewsBundle\Entity\Collection
            media:      Application\Sonata\MediaBundle\Entity\Media
            user:       Application\Sonata\UserBundle\Entity\User

        admin:
            post:
                class:       Sonata\NewsBundle\Admin\PostAdmin
                controller:  SonataAdminBundle:CRUD
                translation: SonataNewsBundle
            comment:
                class:       Sonata\NewsBundle\Admin\CommentAdmin
                controller:  SonataAdminBundle:CRUD
                translation: SonataNewsBundle
            collection:
                class:       Sonata\NewsBundle\Admin\CollectionAdmin
                controller:  SonataAdminBundle:CRUD
                translation: SonataNewsBundle
            tag:
                class:       Sonata\NewsBundle\Admin\TagAdmin
                controller:  SonataAdminBundle:CRUD
                translation: SonataNewsBundle

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