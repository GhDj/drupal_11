## INTRODUCTION

The BSI Search module adds customizations to improve the search engine of the website.

The primary use case for this module is:

- added paragraphs aggregator processor to search_api

## REQUIREMENTS

- search_api - this module adds a new processor to search_api

## INSTALLATION

Install as you would normally install a contributed Drupal module.
See: https://www.drupal.org/node/895232 for further information.

## CONFIGURATION
- go to /admin/config/search/search-api/index/default/processors and enable "Paragraph Aggregator" Processors
- go to /admin/config/search/search-api/index/default/fields and add the field "Aggregated paragraph text"
- Set type to Fulltext

## MAINTAINERS

Current maintainers for Drupal 10:

- FIRST_NAME LAST_NAME (NICKNAME) - https://www.drupal.org/u/NICKNAME

