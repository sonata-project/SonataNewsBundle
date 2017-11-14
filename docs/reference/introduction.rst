Introduction
============

News Bundle is a blog platform based on Doctrine2 and Symfony2.

Permalink
---------

The bundle comes with 2 services to generate a permalink :

* ``sonata.news.permalink.date``: generates an url depending on the publication start date (/2011/12/31/new-year)
* ``sonata.news.permalink.collection``: generates an url depending on the related collection (/party/new-year)

Text Formatting
---------------

The content of a blog post can be formatted in different format :

* markdown
* raw html
* nl2br

This feature is handled by the ``SonataFormatterBundle``.

Comments
--------

Comments can be enabled or disabled depending on the policy selected while creating a Post.
An email is sent every time a comment is added to a Post. The email contains the comment informations such as:

* mail
* message
* url
* etc

and two moderation links to ``activate`` or ``disable`` the comment.
