## INTRODUCTION

The Short URL module allows editors to specify short URLs for content.

- Generate shareable short URLs
- Make content accessible using custom short codes
- Uses redirect module for technical implementation

## REQUIREMENTS

- [Redirect](https://www.drupal.org/project/redirect) module

## INSTALLATION

Install as you would normally install a contributed Drupal module.
See: https://www.drupal.org/node/895232 for further information.

## CONFIGURATION
The global configuration is available at `/admin/config/search/redirect/short-url`.

- Enable content types
  These content types will have the `short_url` field added.
- Specify a url prefix
  - Example: `/share/` for urls like `https://drupal.site/share/ky73d`
  - Example: `/doc-` for urls like `https://drupal.site/doc-ky73d`
- Configuration form and display modes to display the field

## MAINTAINERS

Current maintainers for Drupal 10:

- Anne (ckaotik) - https://www.drupal.org/u/ckaotik

