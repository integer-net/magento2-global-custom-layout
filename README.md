# Integer_Net GlobalCustomLayout

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]

Allows you to add global layout update files to be selected from admin, by using `0` instead of a `category_id` / `sku` / `url_path`.

Compatible with Magento 2.3.4 and higher, since **cms-page/product/category specific layouts** where introduced in this version.

## Purpose

In Magento 2.3.4, [xml layout updates were removed from the Magento Admin](https://devdocs.magento.com/guides/v2.3/release-notes/release-notes-2-3-4-open-source.html#highlights), for security reasons.
Previously this textfield allowed you to add XML Layout updates to any given Category, Product or CMS Page.
After the update, this textfield is no longer available, but you can select custom layout updates which are defined in xml layout files in the filesystem.
 
After uploading/deploying _selectable layout files_ onto your project's filesystem, these layouts can be selected from the admin under the **Design** section.
The field is called **Custom Layout Update**.

## Usage:

Replace identifiers in selectable layouts with a 0 (zero).
Add layout file to themes/modules using:
 - catalog_category_view_selectable_0_<Layout Update Name>.xml for Categories
 - catalog_product_view_selectable_0_<Layout Update Name>.xml for Products
 - cms_page_view_selectable_0_<Layout Update Name>.xml for Cms pages
 
These files can go anywhere where you'd normally put layout files. For example:
`app/design/frontend/[Theme_Vendor]/[Theme_Name]/Magento_Theme/layout/catalog_category_view_0_customchanges.xml`

You can now select the layout update at _any_ given Category/Product/Page, under **Custom layout update** field of **Design**.

More info on default behaviour of selectable layouts: 
[Magento DevDocs: Create cms-page/product/category-specific layouts](https://devdocs.magento.com/guides/v2.3/frontend-dev-guide/layouts/xml-manage.html#create-cms-pageproductcategory-specific-layouts)

## Installation

1. Install via composer
    ```
    composer require integer-net/magento2-global-custom-layout
    ```
2. Enable module
    ```
    bin/magento setup:upgrade
    ```
## Configuration

Zero configuration needed.

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

### Magento Integration Tests

0. Configure test database in `dev/tests/integration/etc/install-config-mysql.php`. [Read more in the Magento docs.](https://devdocs.magento.com/guides/v2.3/test/integration/integration_test_execution.html) 

1. Copy `Test/Integration/phpunit.xml.dist` from the package to `dev/tests/integration/phpunit.xml` in your Magento installation.

2. In that directory, run
    ``` bash
    ../../../vendor/bin/phpunit
    ```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Known issues

1. Does not work with the homepage (cms_index_index). But hey, it doesn't in the default Magento implementation either.

## Security

If you discover any security related issues, please email ww@integer-net.de instead of using the issue tracker.

## Credits

- [Willem Wigman][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.txt) for more information.

[ico-version]: https://img.shields.io/packagist/v/integer-net/magento2-global-custom-layout.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/integer-net/magento2-global-custom-layout/master.svg?style=flat-square
[ico-scrutinizer]: https://scrutinizer-ci.com/g/integer-net/magento2-global-custom-layout/badges/coverage.png?b=master
[ico-code-quality]: https://img.shields.io/scrutinizer/g/integer-net/magento2-global-custom-layout.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/integer-net/magento2-global-custom-layout
[link-travis]: https://travis-ci.org/integer-net/magento2-global-custom-layout
[link-scrutinizer]: https://scrutinizer-ci.com/g/integer-net/magento2-global-custom-layout/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/integer-net/magento2-global-custom-layout
[link-author]: https://github.com/wigman
[link-contributors]: ../../contributors
