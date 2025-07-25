Blackcube Core - CHANGELOG 
==========================

Release 3.6.0 July, 17st 2025 <pgaultier@gmail.com>
----------------------------------------------------

 * Fix: Resumable Preview
 * Upd: Update dependencies

Release 3.5.5 March, 31st 2025 <pgaultier@gmail.com>
----------------------------------------------------

 * Fix: Remove unicity on `MenuItem.name`
 * Upd: Update dependencies

Release 3.5.4 February, 24th 2025 <pgaultier@gmail.com>
-------------------------------------------------------

 * Fix: Add option `slugSensitive` to force `slugs.path` to be accent sensitive
 * Upd: Update dependencies

Release 3.5.3 January, 16th 2025 <pgaultier@gmail.com>
-------------------------------------------------------

 * Fix: Fix host management
 * Upd: Update dependencies

Release 3.5.2 December, 19th 2024 <pgaultier@gmail.com>
-------------------------------------------------------

 * Fix: Fix license, doc blocks
 * Upd: Update dependencies
 * Upd: Reactivate unit tests
 * Upd: Set up gitlab-ci for automatic testing
 * Upd: Add Sonarqube
 * Fix: Fix tests
 * Upd: Update poedit to 3.5

Release 3.5.1 October, 30th 2024 <pgaultier@gmail.com>
-------------------------------------------------------

 * Upd: Update dependencies
 * Fix: six svg preview

Release 3.5.0 October, 18th 2024 <pgaultier@gmail.com>
-------------------------------------------------------

 * Upd: Update dependencies
 * fix `Node` and `Composite` rules

Release 3.4.1 October, 2nd 2024 <pgaultier@gmail.com>
-------------------------------------------------------

 * fix `BlackcubeControllerEvent`

Release 3.4.0 September, 29th 2024 <pgaultier@gmail.com>
-------------------------------------------------------

 * Upd: Update dependencies
 * Prepare `core` for Passkeys (webauthn)

Release 3.3.1 September, 20th 2024 <pgaultier@gmail.com>
-------------------------------------------------------

 * Fix: Fix `cacheImage()` and `cacheFile()`
 * Upd: Update dependencies

Release 3.3.0 September, 6th 2024 <pgaultier@gmail.com>
-------------------------------------------------------

 * Upd: Update filter query to handle orphan composites
 * Upd: Fix sitemap generation
 * Upd: Update i18n
 * Upd: Update filter query to handle `host`
 * Upd: Update Menus to handle `host`
 * Upd: Update dependencies

Release 3.2.1 February, 28th 2024 pgaultier@gmail.com
----------------------------------------------------

 * Upd: Update filter query to handle orphan composites
 * Upd: Update dependencies

Release 3.2.0 January, 11th 2024 <pgaultier@gmail.com>
------------------------------------------------------

 * Upd: Update dependencies
 * Upd: Update file preview system to get original file

Release 3.1.4 November, 8th 2023 <pgaultier@gmail.com>
------------------------------------------------------

 * Fix: Fix slugs

Release 3.1.3 November, 7th 2023 <pgaultier@gmail.com>
------------------------------------------------------

 * Upd: change DI registration for `RobotsTxtAction` and `SitemapAction` to 'robots.txt' and 'sitemap.xml'
 * Upd: Allow to disable routes
 * Upd: Update dependencies

Release 3.1.2 November, 6th 2023 <pgaultier@gmail.com>
------------------------------------------------------

 * Add: Cached files management
 * Add: Assets file management
 * Add: sitemap.xml generation
 * Add: robots.txt generation
 * Upd: Update dependencies

Release 3.1.1 October, 27th 2023 <pgaultier@gmail.com>
------------------------------------------------------

 * Upd: Add caching during request
 * Upd: Update dependencies

Release 3.1.0 October, 23th 2023 <pgaultier@gmail.com>
------------------------------------------------------

 * Upd: Add type filtering
 * Upd: Update i18n
 * Upd: Fix slug generator to be sure generated slug does not exist
* Upd: Update dependencies

Release 3.0.10 October, 20th 2023 <pgaultier@gmail.com>
-------------------------------------------------------

 * Upd: Allow empty path for home page management
 * Upd: Move `cacheDuration` to `Module` config
 * Upd: Update dependencies

Release 3.0.9 October, 16th 2023 <pgaultier@gmail.com>
------------------------------------------------------

 * Fix: Fix category unique
 * Upd: Update dependencies

Release 3.0.8 October, 16th 2023 <pgaultier@gmail.com>
------------------------------------------------------

 * Fix: Fix doc

Release 3.0.7 October, 16th 2023 <pgaultier@gmail.com>
------------------------------------------------------

 * Upd: Update dependencies
 * Upd: Add virtual columns for filtering in BLoc
 * Upd: Adding nofollow to external links with Quill helper
 * Fix: Fix `radiolist` and `dropdownlist` typo `radioList` and `dropdownList`

Release 3.0.6 October, 9th 2023 <pgaultier@gmail.com>
-----------------------------------------------------

 * Fix: fix image resize

Release 3.0.5 October, 8th 2023 <pgaultier@gmail.com>
-----------------------------------------------------

 * Fix: fix cache

Release 3.0.4 October, 7th 2023 <pgaultier@gmail.com>
-----------------------------------------------------

 * Upd: Update dependencies
 * Upd: Update `Element` to handle bloc extraction
 * Upd: Handle canonical URL in `SeoBehavior`

Release 3.0.3 October, 4th 2023 <pgaultier@gmail.com>
-----------------------------------------------------

 * Upd: update quill cleanup regex
 * Fix: Fix elastic model for checkbox and radio

Release 3.0.2 October, 1st 2023 <pgaultier@gmail.com>
-----------------------------------------------------

 * Upd: Update dependencies

Release 3.0.1 September, 27th 2023 <pgaultier@gmail.com>
--------------------------------------------------------

 * Fix: Fix composite attach
 * Upd: Update dependencies

Release 3.0.0 September, 21st 2023 <pgaultier@gmail.com>
--------------------------------------------------------

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

Release 2.2.3 March, 11th 2022 <pgaultier@gmail.com>
----------------------------------------------------

 * Upd: Update dependencies
 * Upd: avoid useless slugs requests
 * Upd: extend DB dependencies helper
 * Upd: hardcode element type to allow inheritance
 * Upd: update QueryCache to handle all CMS tables
 * Upd: remove query cache to let developper handle it
 * Upd: avoid multiple database calls to generate elastic records

Release 2.2.2 March, 9th 2022 <pgaultier@gmail.com>
---------------------------------------------------

 * Fix: Fix blocs active query order by
 * Upd: Update dependencies

Release 2.2.1 February, 19th 2022 <pgaultier@gmail.com>
-------------------------------------------------------

 * Upd: Change blocs active query
 * Upd: Update dependencies

Release 2.2.0 January, 29th 2022 <pgaultier@gmail.com>
------------------------------------------------------

 * Upd: Update command names
 * Upd: Merge migrations with app ones
 * Upd: Update dependencies

Release 2.1.4 September, 9th 2021 <pgaultier@gmail.com>
-------------------------------------------------------

 * Fix: Fix caching images
 * Upd: Update dependencies

Release 2.1.3 July, 29th 2021 <pgaultier@gmail.com>
---------------------------------------------------

 * Fix: Fix caching URLs
 * Upd: Remove files from bucket when deleting bloc

Release 2.1.2 July, 29th 2021 <pgaultier@gmail.com>
---------------------------------------------------

 * Upd: Update dependencies

Release 2.1.1 July, 22th 2021 <pgaultier@gmail.com>
---------------------------------------------------

 * Upd: Update dependencies
 * Fix: Migration error

Release 2.1.0 July, 10th 2021 <pgaultier@gmail.com>
---------------------------------------------------

 * Initial public release on github

Release 2.0.3 June, 18th 2021 <pgaultier@gmail.com>
---------------------------------------------------

 * Fix: SVG management in upload

Release 2.0.4 July, 6th 2021 <pgaultier@gmail.com>
--------------------------------------------------

 * Fix: Erroneous `find()` statements in `ActiveQuery`

Release 2.0.3 June, 18th 2021 <pgaultier@gmail.com>
---------------------------------------------------

 * Fix: SVG updates

Release 2.0.2 June, 14th 2021 <pgaultier@gmail.com>
---------------------------------------------------

 * Fix: SVG management in upload
 * Fix: Elastic model rules for URLs
 * Fix: Elastic model template name  
 * Enh: Adding automatic SEO management
 * Enh: Update query caching
 * Upd: Update dependencies

Release 2.0.1 April, 15th 2020 <pgaultier@gmail.com>
----------------------------------------------------

 * Enh: Adding `SlugGenerator`
 * Upd: Update dependencies

Release 2.0.0 November, 13th 2020 <pgaultier@gmail.com>
-------------------------------------------------------

 * Upd: Update dependencies - !! breaking changes with dotenv
 * Enh: Change migrations to be compatible with Mysql utf8mb4 (string max size 190)
 * Fix: Fix Mysql string size
 * Fix: Fix unicity in `nodes`
 * Fix: Fix migrations
 * Fix: Fix `i18n`
 * Enh: Refactor `plugins` management

Release 1.2.2 September, 5th 2020 <pgaultier@gmail.com>
-------------------------------------------------------

 * Upd: Update dependencies

Release 1.2.1 June, 23th 2020 <pgaultier@gmail.com>
---------------------------------------------------

 * Upd: Update dependencies
 
Release 1.2.0 June, 23th 2020 <pgaultier@gmail.com>
---------------------------------------------------

 * Enh: Adding `plugins` management
 * Fix: Fix `previewManager` in console mode
 
Release 1.1.0 June, 10th 2020 <pgaultier@gmail.com>
---------------------------------------------------

 * Upd: Update code to make routing not mandatory in `Type` 

Release 1.0.0 May, 29th 2020 <pgaultier@gmail.com>
--------------------------------------------------

 * Create core module
