Introduction
============

A blog platform based on Doctrine2 and Symfony2.

Permalink
---------

The bundle comes with 2 services to generate a permalink :

* sonata.news.permalink.date : generates an url depends on the publication start date (/2011/12/31/new-year)
* sonata.news.permalink.category : generates and url depends on the related category (/party/new-year)

Text Formatting
---------------

The content of a blog post can be formatted in different format : markdown, raw html or using nl2br. This feature
is handled by the ``SonataFormatterBundle``.

Comments
--------

Comment can be enabled or disabled depends on the policy selected when a Post is created. An email is sent on every
comment on a Post. The email contents the comment information (mail, message, url, etc ...) and a 2 quick moderation
link : ``activate`` or ``disable`` the comment.
