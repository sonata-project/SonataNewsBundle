# Newsbundle

A blog platform based on Doctrine2 and Symfony2.

## Installation

* Add SonataNewsBundle to your src/Bundle dir

        git submodule add git://github.com/sonata-project/NewsBundle.git src/Sonata/NewsBundle
        git submodule add git://github.com/sonata-project/MediaBundle.git src/Sonata/MediaBundle
        git submodule add git://github.com/sonata-project/UserBundle.git src/Sonata/UserBundle
        git submodule add git://github.com/FriendsOfSymfony/UserBundle.git src/FOS/UserBundle

* Add SonataNewsBundle to your application kernel

        // app/AppKernel.php
        public function registerBundles()
        {
            return array(
                // ...
                new Sonata\NewsBundle\SonataNewsBundle(),
                new Sonata\UserBundle\SonataUserBundle(),
                new Sonata\UserBundle\SonataMediaBundle(),
                new FOS\UserBundle\FOSUserBundle(),
                // ...
            );
        }

* Run the easy-extends command

        php app/console sonata:easy-extends:generate SonataNewsBundle
        php app/console sonata:easy-extends:generate SonataUserBundle
        php app/console sonata:easy-extends:generate SonataMediaBundle

* Enable the new bundles

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

* Add SonataNewsBundle routes to your application routing.yml

        # app/config/routing.yml
        news:
            resource: '@SonataNewsBundle/Resources/config/routing/news.xml'
            prefix: /news

* Add a new context into your ``sonata_media`` configuration

        news:
            providers:
                - sonata.media.provider.dailymotion
                - sonata.media.provider.youtube
                - sonata.media.provider.image

            formats:
                small: { width: 150 , quality: 95}
                big:   { width: 500 , quality: 90}
