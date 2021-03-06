Blackcube Core - CHANGELOG 
==========================

Release 2.2.3 March, 11th 2022 <pgaultier@redcat.io>
----------------------------------------------------

 * Upd: Update dependencies
 * Upd: avoid useless slugs requests
 * Upd: extend DB dependencies helper
 * Upd: hardcode element type to allow inheritance
 * Upd: update QueryCache to handle all CMS tables
 * Upd: remove query cache to let developper handle it
 * Upd: avoid multiple database calls to generate elastic records

Release 2.2.2 March, 9th 2022 <pgaultier@redcat.io>
---------------------------------------------------

 * Fix: Fix blocs active query order by
 * Upd: Update dependencies

Release 2.2.1 February, 19th 2022 <pgaultier@redcat.io>
-------------------------------------------------------

 * Upd: Change blocs active query
 * Upd: Update dependencies

Release 2.2.0 January, 29th 2022 <pgaultier@redcat.io>
------------------------------------------------------

 * Upd: Update command names
 * Upd: Merge migrations with app ones
 * Upd: Update dependencies

Release 2.1.4 September, 9th 2021 <pgaultier@redcat.io>
-------------------------------------------------------

 * Fix: Fix caching images
 * Upd: Update dependencies

Release 2.1.3 July, 29th 2021 <pgaultier@redcat.io>
---------------------------------------------------

 * Fix: Fix caching URLs
 * Upd: Remove files from bucket when deleting bloc

Release 2.1.2 July, 29th 2021 <pgaultier@redcat.io>
---------------------------------------------------

 * Upd: Update dependencies

Release 2.1.1 July, 22th 2021 <pgaultier@redcat.io>
---------------------------------------------------

 * Upd: Update dependencies
 * Fix: Migration error

Release 2.1.0 July, 10th 2021 <pgaultier@redcat.io>
---------------------------------------------------

 * Initial public release on github

Release 2.0.3 June, 18th 2021 <pgaultier@redcat.io>
---------------------------------------------------

 * Fix: SVG management in upload

Release 2.0.4 July, 6th 2021 <pgaultier@redcat.io>
--------------------------------------------------

 * Fix: Erroneous `find()` statements in `ActiveQuery`

Release 2.0.3 June, 18th 2021 <pgaultier@redcat.io>
---------------------------------------------------

 * Fix: SVG updates

Release 2.0.2 June, 14th 2021 <pgaultier@redcat.io>
---------------------------------------------------

 * Fix: SVG management in upload
 * Fix: Elastic model rules for URLs
 * Fix: Elastic model template name  
 * Enh: Adding automatic SEO management
 * Enh: Update query caching
 * Upd: Update dependencies

Release 2.0.1 April, 15th 2020 <pgaultier@redcat.io>
----------------------------------------------------

 * Enh: Adding `SlugGenerator`
 * Upd: Update dependencies

Release 2.0.0 November, 13th 2020 <pgaultier@redcat.io>
-------------------------------------------------------

 * Upd: Update dependencies - !! breaking changes with dotenv
 * Enh: Change migrations to be compatible with Mysql utf8mb4 (string max size 190)
 * Fix: Fix Mysql string size
 * Fix: Fix unicity in `nodes`
 * Fix: Fix migrations
 * Fix: Fix `i18n`
 * Enh: Refactor `plugins` management

Release 1.2.2 September, 5th 2020 <pgaultier@redcat.io>
-------------------------------------------------------

 * Upd: Update dependencies

Release 1.2.1 June, 23th 2020 <pgaultier@redcat.io>
---------------------------------------------------

 * Upd: Update dependencies
 
Release 1.2.0 June, 23th 2020 <pgaultier@redcat.io>
---------------------------------------------------

 * Enh: Adding `plugins` management
 * Fix: Fix `previewManager` in console mode
 
Release 1.1.0 June, 10th 2020 <pgaultier@redcat.io>
---------------------------------------------------

 * Upd: Update code to make routing not mandatory in `Type` 

Release 1.0.0 May, 29th 2020 <pgaultier@redcat.io>
--------------------------------------------------

 * Create core module
