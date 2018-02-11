# Change Log
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

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
