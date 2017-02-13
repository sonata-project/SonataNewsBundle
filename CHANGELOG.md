# Change Log
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

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
