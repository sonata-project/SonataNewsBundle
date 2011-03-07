# Newsbundle

A blog plateform based on Doctrine2 and Symfony2.

## Installation

* Add PageBundle to your src/Bundle dir

        git submodule add git@github.com:sonata-project/NewsBundle.git src/Sonata/NewsBundle

* Add PageBundle to your application kernel

        // app/AppKernel.php
        public function registerBundles()
        {
            return array(
                // ...
                new Sonata\NewsBundle\SonataNewsBundle(),
                // ...
            );
        }
