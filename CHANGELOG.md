Blackcube Core - CHANGELOG 
==========================


 * Upd: Allow empty path for home page management
 * Upd: Move `cacheDuration` to `Module` config

Release 3.0.9 October, 16th 2023 <pgaultier@redcat.io>
----------------------------------------------------

 * Fix: Fix category unique
 * Upd: Update dependencies

Release 3.0.8 October, 16th 2023 <pgaultier@redcat.io>
----------------------------------------------------

 * Fix: Fix doc

Release 3.0.7 October, 16th 2023 <pgaultier@redcat.io>
----------------------------------------------------

 * Upd: Update dependencies
 * Upd: Add virtual columns for filtering in BLoc
 * Upd: Adding nofollow to external links with Quill helper
 * Fix: Fix `radiolist` and `dropdownlist` typo `radioList` and `dropdownList`

Release 3.0.6 October, 9th 2023 <pgaultier@redcat.io>
----------------------------------------------------

 * Fix: fix image resize

Release 3.0.5 October, 8th 2023 <pgaultier@redcat.io>
----------------------------------------------------

 * Fix: fix cache

Release 3.0.4 October, 7th 2023 <pgaultier@redcat.io>
----------------------------------------------------

 * Upd: Update dependencies
 * Upd: Update `Element` to handle bloc extraction
 * Upd: Handle canonical URL in `SeoBehavior`

Release 3.0.3 October, 4th 2023 <pgaultier@redcat.io>
----------------------------------------------------

 * Upd: update quill cleanup regex
 * Fix: Fix elastic model for checkbox and radio

Release 3.0.2 October, 1st 2023 <pgaultier@redcat.io>
----------------------------------------------------

 * Upd: Update dependencies

Release 3.0.1 September, 27th 2023 <pgaultier@redcat.io>
----------------------------------------------------

 * Fix: Fix composite attach
 * Upd: Update dependencies

Release 3.0.0 September, 21st 2023 <pgaultier@redcat.io>
----------------------------------------------------

 * Add: Adding `Quill` helper to clean generated html
 * Add: Adding Element helper to compute `dateCreate` and `dateUpdate`
 * Add: Adding `RobotsTxtAction` to generate `robots.txt`
 * Upd: Update `SitemapAction` to generate `sitemap.xml`
 * Upd: set minimal requirements to PHP 8.0
 * Upd: add password strength validator
 * Fix: Element instanciate for controller routing
 * Upd: Add DI register
 * Upd: Prepare type hints
 * Upd: Update dependencies
 * Chg: Prepare new plugin system
 * Chg: move ```db```, ```cache``` and ```fs``` to components to allow tree traversal
 * Upd: Change ```flysystem``` wrapper
 * Chg: Rewrite ```plugin``` system
 * Fix: Preview was not working when mimetype is incorrect
 * Fix: Fix `cacheImage()` when image is svg

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
