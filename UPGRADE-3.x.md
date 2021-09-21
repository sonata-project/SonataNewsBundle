UPGRADE 3.x
===========

UPGRADE FROM 3.x to 3.x
=======================

### Deprecate API

Integration with FOSRest, JMS Serializer and Nelmio Api Docs is deprecated, the ReST API provided with this bundle will be removed on 4.0.

If you are relying on this, consider moving to other solution like [API Platform](https://api-platform.com/) instead.

UPGRADE FROM 3.16 to 3.17
=========================

### Sonata\NewsBundle\Mailer\Mailer

Passing an instance of `\Swift_Mailer` as argument 1  for `Sonata\NewsBundle\Mailer\Mailer::__construct()`
is deprecated. Pass an instance of `Symfony\Component\Mailer\MailerInterface` instead.

UPGRADE FROM 3.13 to 3.14
=========================

### SonataEasyExtends is deprecated

Registering `SonataEasyExtendsBundle` bundle is deprecated, it SHOULD NOT be registered.
Register `SonataDoctrineBundle` bundle instead.

UPGRADE FROM 3.1 to 3.2
=======================

- Doctrine MongoDb metadata `comments_count` has been changed to `commentsCount`. In case of having problems, please update your collections.

UPGRADE FROM 3.0 to 3.1
=======================

### Tests

All files under the ``Tests`` directory are now correctly handled as internal test classes.
You can't extend them anymore, because they are only loaded when running internal tests.
More information can be found in the [composer docs](https://getcomposer.org/doc/04-schema.md#autoload-dev).
