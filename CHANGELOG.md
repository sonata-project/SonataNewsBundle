# Change Log
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## [3.16.0](https://github.com/sonata-project/SonataNewsBundle/compare/3.15.0...3.16.0) - 2020-12-05
### Added
- [[#678](https://github.com/sonata-project/SonataNewsBundle/pull/678)] Addded support for `doctrine/persistence` 2 ([@core23](https://github.com/core23))

## [3.15.0](https://github.com/sonata-project/SonataNewsBundle/compare/3.14.0...3.15.0) - 2020-11-09
### Added
- [[#639](https://github.com/sonata-project/SonataNewsBundle/pull/639)] Support for sonata-project/datagrid-bundle v3 ([@wbloszyk](https://github.com/wbloszyk))

### Changed
- [[#666](https://github.com/sonata-project/SonataNewsBundle/pull/666)] Updates Dutch translations ([@zghosts](https://github.com/zghosts))

### Fixed
- [[#634](https://github.com/sonata-project/SonataNewsBundle/pull/634)] Fixed support for string model identifiers at Open API definitions ([@wbloszyk](https://github.com/wbloszyk))

### Removed
- [[#650](https://github.com/sonata-project/SonataNewsBundle/pull/650)] Remove translator deprecations ([@core23](https://github.com/core23))
- [[#634](https://github.com/sonata-project/SonataNewsBundle/pull/634)] Removed requirements that were only allowing integers for model identifiers at Open API definitions ([@wbloszyk](https://github.com/wbloszyk))

## [3.14.0](https://github.com/sonata-project/SonataNewsBundle/compare/3.13.0...3.14.0) - 2020-08-22
### Added
- [[#612](https://github.com/sonata-project/SonataNewsBundle/pull/612)] Added
  support for "friendsofsymfony/rest-bundle:^3.0"
([@wbloszyk](https://github.com/wbloszyk))
- [[#610](https://github.com/sonata-project/SonataNewsBundle/pull/610)] Added
  public alias `Sonata\NewsBundle\Controller\Api\CommentController` for
`sonata.news.controller.api.comment` service
([@wbloszyk](https://github.com/wbloszyk))
- [[#610](https://github.com/sonata-project/SonataNewsBundle/pull/610)] Added
  public alias `Sonata\NewsBundle\Controller\Api\PostController` for
`sonata.news.controller.api.post` service
([@wbloszyk](https://github.com/wbloszyk))

### Change
- [[#612](https://github.com/sonata-project/SonataNewsBundle/pull/612)] Support
  for deprecated "rest" routing type in favor for xml
([@wbloszyk](https://github.com/wbloszyk))

### Changed
- [[#601](https://github.com/sonata-project/SonataNewsBundle/pull/601)]
  SonataEasyExtendsBundle is now optional, using SonataDoctrineBundle is
preferred ([@jordisala1991](https://github.com/jordisala1991))

### Deprecated
- [[#601](https://github.com/sonata-project/SonataNewsBundle/pull/601)] Using
  SonataEasyExtendsBundle to add Doctrine mapping information
([@jordisala1991](https://github.com/jordisala1991))

### Fixed
- [[#615](https://github.com/sonata-project/SonataNewsBundle/pull/615)] Fixed
  references to `Application\` namespace at
`BasePostRepository::countCommentsQuery()`.
([@phansys](https://github.com/phansys))
- [[#610](https://github.com/sonata-project/SonataNewsBundle/pull/610)] Fix
  RestFul API - `Class could not be determined for Controller identified` Error
([@wbloszyk](https://github.com/wbloszyk))
- [[#609](https://github.com/sonata-project/SonataNewsBundle/pull/609)] Fix
  missing `u` filter when `SonataNewsBundle` is register after `TwigBundle`
([@wbloszyk](https://github.com/wbloszyk))

### Removed
- [[#611](https://github.com/sonata-project/SonataNewsBundle/pull/611)] Support
  for Symfony < 4.4 ([@wbloszyk](https://github.com/wbloszyk))

## [3.13.0](https://github.com/sonata-project/SonataNewsBundle/compare/3.12.0...3.13.0) - 2020-06-29
### Added
- [[#602](https://github.com/sonata-project/SonataNewsBundle/pull/602)] Added
  `twig/string-extra` dependency. ([@wbloszyk](https://github.com/wbloszyk))

### Changed
- [[#602](https://github.com/sonata-project/SonataNewsBundle/pull/602)] Changed
  use of `truncate` filter with `u` filter.
([@wbloszyk](https://github.com/wbloszyk))

### Fixed
- [[#603](https://github.com/sonata-project/SonataNewsBundle/pull/603)]
  Deprecations for event dispatching ([@wbloszyk](https://github.com/wbloszyk))
- [[#594](https://github.com/sonata-project/SonataNewsBundle/pull/594)] Fixed
  sql to work with mssql ([@wbloszyk](https://github.com/wbloszyk))

### Removed
- [[#603](https://github.com/sonata-project/SonataNewsBundle/pull/603)] Remove
  support for Symfony <4.3 and php <7.2
([@wbloszyk](https://github.com/wbloszyk))
- [[#595](https://github.com/sonata-project/SonataNewsBundle/pull/595)] Remove
  SonataCoreBundle dependencies ([@wbloszyk](https://github.com/wbloszyk))

## [3.12.0](https://github.com/sonata-project/SonataNewsBundle/compare/3.11.0...3.12.0) - 2020-05-02
### Changed
- Make admin bundle optional

### Fixed
- Fix clashing `format_datetime` filter call

## [3.11.0](https://github.com/sonata-project/SonataNewsBundle/compare/3.10.1...3.11.0) - 2020-01-06
### Removed
- Removed tight coupling to user bundle
- Removed `sonata-project/user-bundle` dependency
- Support for Symfony < 3.4
- Support for Symfony >= 4, < 4.2

## [3.10.1](https://github.com/sonata-project/SonataNewsBundle/compare/3.10.0...3.10.1) - 2019-11-11
### Added
- Add missing translation for admin menu

### Fixed
- Fix missing call to isVisible

## [3.10.0](https://github.com/sonata-project/SonataNewsBundle/compare/3.9.0...3.10.0) - 2019-09-20
### Added
- Added conflict for unsupported `nelmio/api-doc-bundle` versions
- Add default context to breadcrumbs

### Changed
- Changed id type to mixed

### Fixed
- Match PHPDoc with doctrine model

## [3.9.0](https://github.com/sonata-project/SonataNewsBundle/compare/3.8.0...3.9.0) - 2019-09-02
### Added
- Add missing `twig/twig` required in `composer.json` with versions `^1.35 || ^2.4`
- Added events to the comment process

### Fixed
- deprecation notice about using namespaced classes from `\Twig\`

### Changed
- Trigger an error if a class declaration is missing in the configuration
- Improved support for different db drivers

## [3.8.0](https://github.com/sonata-project/SonataNewsBundle/compare/3.7.0...3.8.0) - 2019-01-27
### Fixed
- CoreBundle deprecations
- deprecation for symfony/config 4.2+

### Removed
- support for php 5 and php 7.0

## [3.7.0](https://github.com/sonata-project/SonataNewsBundle/compare/3.6.0...3.7.0) - 2018-11-10
### Added
- Added group icon to admin pages

### Changed
- SEO: Append title instead of replacing it

### Fixed
- Added missing seo information to `PostArchiveAction`

## [3.6.0](https://github.com/sonata-project/SonataNewsBundle/compare/3.5.0...3.6.0) - 2018-10-31
### Added
- Added support for latest `formatter-bundle`

## [3.5.0](https://github.com/sonata-project/SonataNewsBundle/compare/3.4.0...3.5.0) - 2018-07-08

### Added
- Compatibility with FOSRest 2.0
- Make posts visible to admins
- Show status message if post is not public
- Added SEO information to archives
- Added a daily archive

### Fixed
- Replaced deprecated `bind` by `handleRequest` on forms
- Commands not working on Symfony 4
- Make services public
- Previous and next links now link to the correct location in the archive page
- Fixed `addChild` deprecations

### Changed
- `Controller\PostController` is now deprecated in favor of `Action\*Action`

## [3.4.0](https://github.com/sonata-project/SonataNewsBundle/compare/3.3.0...3.4.0) - 2018-02-11
### Added
- added block title translation domain option
- added block icon option
- added block class option

### Changed
- replaced box with bootstrap panel layout in blocks

### Fixed
- Fixed wrong translation in blocks
- Fixed calling wrong method when submitting comment
- Check if the comment form was submitted
- Fixed wrong translation key

### Removed
- Removed default title from blocks

## [3.3.0](https://github.com/sonata-project/SonataNewsBundle/compare/3.2.2...3.3.0) - 2018-01-26
### Changed
- Switch all templates references to Twig namespaced syntax
- Switch from templating service to sonata.templating

### Fixed
- Fixed creating new swift message
- Switch Field Type checkbox option to FQCN option for sf 4 compatibility

## [3.2.2](https://github.com/sonata-project/SonataNewsBundle/compare/3.2.1...3.2.2) - 2018-01-07
### Fixed
- Compatibility with Symfony 4
- Missing translation to `CommentStatusType`
- Support for swiftmailer 6
- Fixed calling deprecated setDefaultOptions method
- Don't call the translator in breadcrumbs

## [3.2.1](https://github.com/sonata-project/SonataNewsBundle/compare/3.2.0...3.2.1) - 2017-12-07
### Added
- Added Russian translations
- Added support fos `sonata-project/user-bundle` 4

## [3.2.0](https://github.com/sonata-project/SonataNewsBundle/compare/3.1.2...3.2.0) - 2017-11-29
### Changed
- Removed usage of old form type aliases
- Changed internal folder structure to `src`, `tests` and `docs`

### Fixed
- Fixed hardcoded paths to classes in `.xml.skeleton` files of config
- Fixed `Post` Document mongoDb metadata from `comments_count` to `commentsCount`
- Fixed calling deprecated twig tag

### Removed
- support for old versions of php and Symfony
- Removed deprecated form alias usage

## [3.1.2](https://github.com/sonata-project/SonataNewsBundle/compare/3.1.1...3.1.2) - 2017-04-07
### Changed
- The `sonata-project/block-bundle` is an optional dependency

### Fixed
- Added missing `RecentPostsBlockService::$adminPool` parameter
- Added missing `NewsExtension::$blog` parameter
- Fixed wrong or missing PHPDoc
- Fixed return null value instead of void in `PostController::getSeoPage`
- Fixed return null value instead of void in `PostManager::findOneByPermalink`
- Fixed return null value instead of void in `CommentStatusRenderer::getStatusClass`
- Fixed BlockBundle deprecation messages
- Fixed request service deprecation messages
- Fixed old center html tag
- Missing swiftmailer dependency
- Fixed pager test with DatagridBundle 2.2.1
- Catch exception when finding post by permalink
- deprecated `configureSideMenu` usage was fixed

### Removed
 - Removed dead parameters in method calls

## [3.1.1](https://github.com/sonata-project/SonataNewsBundle/compare/3.1.0...3.1.1) - 2017-02-13
### Changed
- the method `PostManager::findOneBySlug` will return null if the permalink couldn't be found

### Fixed
- Fixed throwing doctrine exception on invalid permalink

## [3.1.0](https://github.com/sonata-project/SonataNewsBundle/compare/3.0.0...3.1.0) - 2017-01-17
### Added
- Added `Document\PostManager::findOneByPermalink` method

### Changed
- Replaced html `form` element with twig function
- Replaced full stack symfony dependencies with only used ones.

### Deprecated
- Deprecate `Document\PostManager::findOneBySlug` method

### Fixed
- Fixed showing only one tag while filtering archive by tag
- Fix deprecated usage of `Admin` class
- Fixed duplicate translation in tab menu
- Add missing configuration to make the CoreBundle's FormHelper works properly

### Removed
- Internal test classes are now excluded from the autoloader
- Removed `symfony/symfony` dependency
