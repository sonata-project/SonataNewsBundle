# Change Log
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## [3.1.2](https://github.com/sonata-project/SonataNewsBundle/compare/3.1.1...3.1.2) - 2017-04-07
### Added
- Added missing `RecentPostsBlockService::$adminPool` parameter
- Added missing `NewsExtension::$blog` parameter

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
