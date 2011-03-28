# Newsbundle

A blog platform based on Doctrine2 and Symfony2.

## Installation

* Add SonataNewsBundle to your src/Bundle dir

        git submodule add git@github.com:sonata-project/NewsBundle.git src/Sonata/NewsBundle

* Add SonataNewsBundle to your application kernel

        // app/AppKernel.php
        public function registerBundles()
        {
            return array(
                // ...
                new Sonata\NewsBundle\SonataNewsBundle(),
                // ...
            );
        }

* Add SonataNewsBundle routes to your application routing.yml

        # app/config/routing.yml
        news:
            resource: '@SonataNews/Resources/config/routing/news.xml'
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
