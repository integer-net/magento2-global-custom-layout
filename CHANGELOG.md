# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## 1.0.0 - 2020-04-27
### Added
- Plugins for category, product and page layouts that allow custom layout files to be loaded with a global identifier
 (`0`). E.g. `catalog_category_view_selectable_0_.xml` for Categories.

## 1.1.0 - 2020-05-02
### Added
- Adds frontend test coverage for global custom layout updates
- Fixes [#7](https://github.com/integer-net/magento2-global-custom-layout/issues/7) where layout handles were not merged in Product and Page Plugins' `afterFetchAvailableFiles()` method.
