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


* Add this in your admin.yml

        page:
            label:      Page
            group:      CMS
            class:      Sonata\PageBundle\Admin\PageAdmin
            entity:     Application\Sonata\PageBundle\Entity\Page
            controller: SonataPageBundle:PageAdmin
            children:
                block:
                    label:      Block
                    group:      CMS
                    class:      Sonata\PageBundle\Admin\BlockAdmin
                    entity:     Application\Sonata\PageBundle\Entity\Block
                    controller: SonataPageBundle:BlockAdmin

        block:
            label:      Block
            group:      CMS
            class:      Sonata\PageBundle\Admin\BlockAdmin
            entity:     Application\Sonata\PageBundle\Entity\Block
            controller: SonataPageBundle:BlockAdmin

        template:
            label:      Template
            group:      CMS
            class:      Sonata\PageBundle\Admin\TemplateAdmin
            entity:     Application\Sonata\PageBundle\Entity\Template
            controller: SonataPageBundle:TemplateAdmin
            options:
                show_in_dashboard: false

