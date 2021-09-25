.. index::
    single: Installation
    single: Configuration

Installation
============

Prerequisites
-------------

PHP ^7.3 and Symfony ^4.4 are needed to make this bundle work, there are
also some Sonata dependencies that need to be installed and configured beforehand.

Required dependencies:

* `SonataAdminBundle <https://docs.sonata-project.org/projects/SonataAdminBundle/en/3.x/>`_
* `SonataBlockBundle <https://docs.sonata-project.org/projects/SonataBlockBundle/en/3.x/>`_
* `SonataFormatterBundle <https://docs.sonata-project.org/projects/SonataFormatterBundle/en/4.x/>`_
* `SonataIntlBundle <https://docs.sonata-project.org/projects/SonataIntlBundle/en/2.x/>`_
* `SonataClassificationBundle <https://docs.sonata-project.org/projects/SonataClassificationBundle/en/3.x/>`_
* `SonataMediaBundle <https://docs.sonata-project.org/projects/SonataMediaBundle/en/3.x/>`_

And the persistence bundle (choose one):

* `SonataDoctrineOrmAdminBundle <https://docs.sonata-project.org/projects/SonataDoctrineORMAdminBundle/en/3.x/>`_
* `SonataDoctrineMongoDBAdminBundle <https://docs.sonata-project.org/projects/SonataDoctrineMongoDBAdminBundle/en/3.x/>`_

Follow also their configuration step; you will find everything you need in
their own installation chapter.

.. note::

    If a dependency is already installed somewhere in your project or in
    another dependency, you won't need to install it again.

Enable the Bundle
-----------------

Add ``SonataNewsBundle`` via composer::

    composer require sonata-project/news-bundle

Next, be sure to enable the bundles in your ``config/bundles.php`` file if they
are not already enabled::

    // config/bundles.php

    return [
        // ...
        Sonata\NewsBundle\SonataNewsBundle::class => ['all' => true],
    ];

Configuration
=============

Sonata Configuration
--------------------

.. code-block:: yaml

    # config/packages/sonata_news.yaml

    sonata_news:
        title: Sonata Project
        link: https://sonata-project.org
        description: Cool bundles on top of Symfony
        salt: secureToken
        permalink_generator: sonata.news.permalink.date # sonata.news.permalink.collection
        db_driver: doctrine_orm
        class:
            post: App\Entity\SonataNewsPost
            comment: App\Entity\SonataNewsComment
            media: App\Entity\SonataMediaMedia
            user: App\Entity\SonataUserUser
            tag: App\Entity\SonataClassificationTag
            collection: App\Entity\SonataClassificationCollection
        comment:
            notification:
                emails: [email@example.org, email2@example.org]
                from: no-reply@sonata-project.org
                template: '@SonataNews/Mail/comment_notification.txt.twig'

Doctrine ORM Configuration
--------------------------

Add the bundle in the config mapping definition (or enable `auto_mapping`_)::

    # config/packages/doctrine.yaml

    doctrine:
        orm:
            entity_managers:
                default:
                    mappings:
                        SonataNewsBundle: ~

And then create the corresponding entities, ``src/Entity/SonataNewsComment``::

    // src/Entity/SonataNewsComment.php

    use Doctrine\ORM\Mapping as ORM;
    use Sonata\NewsBundle\Entity\BaseComment;

    /**
     * @ORM\Entity
     * @ORM\Table(name="news__comment")
     */
    class SonataNewsComment extends BaseComment
    {
        /**
         * @ORM\Id
         * @ORM\GeneratedValue
         * @ORM\Column(type="integer")
         */
        protected $id;
    }

and ``src/Entity/SonataNewsPost``::

    // src/Entity/SonataNewsPost.php

    use Doctrine\ORM\Mapping as ORM;
    use Sonata\NewsBundle\Entity\BasePost;

    /**
     * @ORM\Entity
     * @ORM\Table(name="news__post")
     */
    class SonataNewsPost extends BasePost
    {
        /**
         * @ORM\Id
         * @ORM\GeneratedValue
         * @ORM\Column(type="integer")
         */
        protected $id;
    }

The only thing left is to update your schema::

    bin/console doctrine:schema:update --force

Doctrine MongoDB Configuration
------------------------------

You have to create the corresponding documents, ``src/Document/SonataNewsComment``::

    // src/Document/SonataNewsComment.php

    use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
    use Sonata\NewsBundle\Document\BaseComment;

    /**
     * @MongoDB\Document
     */
    class SonataNewsComment extends BaseComment
    {
        /**
         * @MongoDB\Id
         */
        protected $id;
    }

and ``src/Document/SonataNewsPost``::

    // src/Document/SonataNewsPost.php

    use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
    use Sonata\NewsBundle\Document\BasePost;

    /**
     * @MongoDB\Document
     */
    class SonataNewsPost extends BasePost
    {
        /**
         * @MongoDB\Id
         */
        protected $id;
    }

Then configure ``SonataNewsBundle`` to use the newly generated classes::

    # config/packages/sonata_news.yaml

    sonata_news:
        manager_type: doctrine_mongodb
        class:
            post: App\Document\SonataNewsPost
            comment: App\Document\SonataNewsComment
            media: App\Document\SonataMediaMedia
            user: App\Document\SonataUserUser
            tag: App\Document\SonataClassificationTag
            collection: App\Document\SonataClassificationCollection

Add SonataNewsBundle routes
---------------------------

.. code-block:: yaml

    # config/packages/routes.yaml

    news:
        resource: '@SonataNewsBundle/Resources/config/routing/news.xml'
        prefix: /news

Next Steps
----------

At this point, your Symfony installation should be fully functional, without errors
showing up from SonataNewsBundle. If, at this point or during the installation,
you come across any errors, don't panic:

    - Read the error message carefully. Try to find out exactly which bundle is causing the error.
      Is it SonataNewsBundle or one of the dependencies?
    - Make sure you followed all the instructions correctly, for both SonataNewsBundle and its dependencies.
    - Still no luck? Try checking the project's `open issues on GitHub`_.

.. _`open issues on GitHub`: https://github.com/sonata-project/SonataNewsBundle/issues
.. _`auto_mapping`: http://symfony.com/doc/4.4/reference/configuration/doctrine.html#configuration-overviews
