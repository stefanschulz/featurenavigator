# Feature Navigator Module for PrestaShop

## About

Module allowing for feature-based, alphabetical navigation of products in a store.

## Compatability

Requires Prestashop 8.0 or later.

## Installation

1. Download the file featurenavigator.zip
2. Go to the PrestaShop administration page
3. Go to 'Modules' » 'Module Manager'
4. Use 'Upload a module' and drop or select the downloaded zip-file
5. The module will be installed

## Configuration

Open the module's configuration page via PrestaShop's Module Manager. Here you can set the following options:

- Heading: Defines the text to be displayed as headline for each of the pages shown in front-office.
- Feature: Defines the feature to be used for listing in the navigation and product lists.
- Direction: Defines the sort direction for feature listing.

### Module links

The module provides two routes that can be linked to. These are:

- "featurenavigator/list": Will display a navigation box with the letters A to Z and a #-symbol. The content shows the
  feature entries available for the selected letter, where # denotes any entry not starting with a letter. If no letter
  is selected, A is used as initial letter for listing. The URL may be extended by the respective letter as final path
  segment for direct linking.
- "featurenavigator/products": Will display the products for a selected feature. It requires the feature name to be
  passed as final path segment to the URL.

### Adding a link to the menu

To add a link to the feature navigator to the main menu, it has to be added as custom link, e.g., via the Main Menu
module configuration page. On common sites, the above mentioned page "featurenavigator/list" should be used as url.
If the site is set for multi-language use, the respective language code has to be prepended, 
e.g. "gb/featurenavigator/list" for the british page.

## Multistore compatibility

Untested.

## Reporting issues

You can report issues with this module in the module's repository. [Click here to report an issue][report-issue].

## License

This module is released under the [Apache License 2.0][Apache-2.0]

[report-issue]: https://github.com/stefanschulz/featurenavigator/issues

[Apache-2.0]: https://www.apache.org/licenses/LICENSE-2.0
