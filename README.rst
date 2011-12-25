SonataNewsBundle
================

A blog platform based on Doctrine2 (ORM and MongoDB) and Symfony2.

The online documentation of the bundle is in http://sonata-project.org/bundles/news

For contribution to the documentation you cand find it on ``Resources/doc``

**Warning**: documentation files are not rendering correctly in Github (reStructuredText format)
and some content might be broken or hidden, make sure to read raw files.

**Google Groups**: For questions and proposals you can post on this google groups

* `Sonata Users <https://groups.google.com/group/sonata-users>`_: Only for user questions
* `Sonata Devs <https://groups.google.com/group/sonata-devs>`_: Only for devs


With Symfony v2.0.4

Clear cache before !! rm app/cache/* -rf !!

Add to app/deps
<pre>
[symfony]
    git=http://github.com/symfony/symfony.git
    version=v2.0.4
...

[FOSUserBundle]
    git=git://github.com/FriendsOfSymfony/FOSUserBundle.git
    target=/bundles/FOS/UserBundle

[SonataNewsBundle]
    git=git@github.com:sonata-project/SonataNewsBundle.git
    target=/bundles/Sonata/NewsBundle

[SonataMediaBundle]
    git=git@github.com:sonata-project/SonataMediaBundle.git
    target=/bundles/Sonata/MediaBundle

[SonataUserBundle]
    git=https://github.com/sonata-project/SonataUserBundle.git
    target=/bundles/Sonata/UserBundle

[SonataAdminBundle]
    git=git@github.com:sonata-project/SonataAdminBundle.git
    target=/bundles/Sonata/AdminBundle

[SonataFormatterBundle]
    git=http://github.com/sonata-project/SonataFormatterBundle.git
    target=/bundles/Sonata/FormatterBundle

[SonataIntlBundle]
    git=https://github.com/sonata-project/SonataIntlBundle
    target=/bundles/Sonata/IntlBundle	

[KnpMarkdownBundle]
    git=http://github.com/knplabs/KnpMarkdownBundle.git
    target=/bundles/Knp/Bundle/MarkdownBundle

[KnpMenu]
    git=https://github.com/knplabs/KnpMenu.git
    target=/knp/menu

[gaufrette]
    git=https://github.com/sonata-project/Gaufrette.git
    target=/gaufrette

[imagine]
    git=https://github.com/avalanche123/Imagine.git
    target=/imagine
</pre>

Let a empty line to the end of file deps

Make a "php bin/vendors install"

If you have some errors after this action, install vendor folder from http://symfony.com/download

Add this to app/autoload.php
<pre>
.. 
$loader->registerNamespaces(array(
    ...
    'FOS'              => __DIR__.'/../vendor/bundles',    
    'Knp' => array(
        __DIR__.'/../vendor/bundles',
        __DIR__.'/../vendor/knp/menu/src'
    ),
    'Sonata'           => __DIR__.'/../vendor/bundles',
    'Gaufrette' => __DIR__.'/../vendor/gaufrette/src',
    'Imagine' => __DIR__.'/../vendor/imagine/lib',
...
</pre>

Add to app/AppKernel.php
<pre>
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
.../*Vendor Sonata*/
            new Sonata\AdminBundle\SonataAdminBundle(),
            new Sonata\NewsBundle\SonataNewsBundle(),
            new Sonata\UserBundle\SonataUserBundle(),
            new Sonata\MediaBundle\SonataMediaBundle(),
            new Sonata\FormatterBundle\SonataFormatterBundle(),
            
            new FOS\UserBundle\FOSUserBundle(),
            new Knp\Bundle\MarkdownBundle\KnpMarkdownBundle(), 
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
            
            /*Application Sonata*/
            new Application\Sonata\NewsBundle\ApplicationSonataNewsBundle(),
            new Application\Sonata\UserBundle\ApplicationSonataUserBundle(),
            new Application\Sonata\MediaBundle\ApplicationSonataMediaBundle(),
...
)};
</pre>

Add to app/config/config.yml
<pre>
imports:
    - { resource: parameters.ini }
    - { resource: security.yml }
    - { resource: sonata_news.yml }
    - { resource: sonata_media.yml }
    - { resource: fos_user.yml }
</pre>

Create 3 new files:
- app/config/fos_user.yml
- app/config/sonata_media.yml
- app/config/sonata_news.yml

Add to fos_user.yml
<pre>
fos_user:
  db_driver: orm # can be orm or odm
  firewall_name: main
  user_class: Application\Sonata\UserBundle\Entity\User
  group:
    group_class: Application\Sonata\UserBundle\Entity\Group
doctrine:
  orm:
    entity_managers:
      default:
        mappings:
          FOSUserBundle: ~
          ApplicationSonataUserBundle: ~
          SonataUserBundle: ~
</pre>

Add to sonata_media.yml
<pre>
sonata_media:
  db_driver: doctrine_orm
  contexts:
    default: # the default context is mandatory
      providers:
        - sonata.media.provider.dailymotion
        - sonata.media.provider.youtube
        - sonata.media.provider.image
        - sonata.media.provider.file
        - sonata.media.provider.vimeo
      formats:
        small: { width: 100 , quality: 70}
        big: { width: 500 , quality: 70}
  cdn:
    sonata.media.cdn.server:
      path: /uploads/media # http://media.sonata-project.org/
  filesystem:
    sonata.media.adapter.filesystem.local:
      directory: %kernel.root_dir%/../web/uploads/media
      create: true
  providers:
    sonata.media.provider.file:
      resizer: false
doctrine:
  orm:
    entity_managers:
      default:
        mappings:
          ApplicationSonataMediaBundle: ~
          SonataMediaBundle: ~
</pre>

Add to sonata_news.yml
<pre>
sonata_news:
  title:        "Mon titre"    
  link:         http://sonata-project.org
  description:  Cool bundles on top of Symfony2
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
</pre>

Create in src/application/Sonata
../MediaBundle/ApplicationSonataMediaBundle.php
<pre>

namespace Application\Sonata\MediaBundle;
use Symfony\Component\HttpKernel\Bundle\Bundle;
class ApplicationSonataMediaBundle extends Bundle
{
}
</pre>

../NewsBundle
<pre>

namespace Application\Sonata\NewsBundle;
use Symfony\Component\HttpKernel\Bundle\Bundle;
class ApplicationSonataNewsBundle extends Bundle
{
}
</pre>

../UserBundle/
<pre>

namespace Application\Sonata\UserBundle;
use Symfony\Component\HttpKernel\Bundle\Bundle;
class ApplicationSonataUserBundle extends Bundle
{
}
</pre>
