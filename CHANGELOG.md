SMARTGEAR Project for Contao Open Source CMS
========

1.0.45 - 2025-03-14
- Feat : as Form Data Manager is now an external bundle, use it.

1.0.44 - 2025-02-12
- Feat : better management of pages visit

1.0.43 - 2025-01-24
- Fix : `SendNotificationMessageListener::__invoke` - `$arrTokens` keys can be `int`, so cast them in `string` before calling `explode`

1.0.42 - 2025-01-21
- Fix : `tl_form_field.contains_personal_data` & `tl_form_field.is_technical_field` now have the `w50` class applied

1.0.41 - 2025-01-10
- Smartgear : fixed using Personal Data Manager depending on configuration when registrering user in front-end

1.0.40 - 2024-12-17
- Fix : `tl_sm_social_link.icon`'s input allows more than 15 characters

1.0.39 - 2024-11-04
- Dependencies bump
- RSCE : avoiding some PHP warnings

1.0.38 - 2024-08-22
- FIX : Various fixes for dev mod
- UPDATED : Adaptation to new PDM storage

1.0.37 - 2024-08-06
- Feat : Add compatibility with webexmachina/contao-utils 2.0

1.0.36 - 2024-07-31
- Smartgear : fix `breadcrumb` autoplacement

1.0.35 - 2024-07-25
- Smartgear : fix `form_password.html5` regexp

1.0.34 - 2024-07-02
- Smartgear : display core's install form instead of dashboard when SG is not installed

1.0.33 - 2024-06-24
- Smartgear : fixed `websiteTitle` field not present in BE configuration form

1.0.32 - 2024-05-31
- Smartgear : fixed `form_captcha.html5`

1.0.31 - 2024-05-14
- Smartgear : some notifications' tokens updates 
    + `useful_data` now uses `<br />` as carriage return, available in `email_html` & `email_text` (only for compatibility)
    + `useful_data_filled` now uses `<br />` as carriage return, available in `email_html` & `email_text` (only for compatibility)
    + `useful_data_text` (new token) uses `\n` as carriage return, available in `email_text` only
    + `useful_data_filled_text` (new token) uses `\n` as carriage return, available in `email_text` only
- Smartgear : `email` field `mandatory` property is no more required when form is managed by Form Data Manager

1.0.30 - 2024-04-24
- Smartgear : fixed call for Personal Data Manager when displaying personal data in back-end using the GPDR menu
- Smartgear : fixed call for Personal Data Manager when displaying personal data in front-end

1.0.29 - 2024-04-15
- Fix : remove `heimrichhannot/contao-filename-sanitizer-bundle` to avoid problems in PHP > 8.1 (autowiring issue, bundle not updated since 2022)

1.0.28 - 2024-02-09
- Smartgear : Backup Manager better decides when to split large files in smaller files (avoid memory limit overflow, but isn't faster)

1.0.27 - 2024-01-19
- Smartgear : fixed an exception triggers by a Contao 4.13.36 updated (checkPermission removed from tl_theme DCA)

1.0.26 - 2023-11-28
- Smartgear : fixed backend menu alteration which could lead in an error

1.0.25 - 2023-11-20
- Smartgear : added a way to lock an install (only available when editing configuration thanks to dedicated form)
- Smartgear : fixed first sentence in `form_warning_message.html5`
- RSCE : Improved default size attribution in `rsce_slider`
- RSCE : Improved default size attribution in `rsce_blockCard`
- RSCE : Improved default size attribution in `rsce_listIcons`
- RSCE : Improved default size attribution in `rsce_modal`
- RSCE : Improved default size attribution in `rsce_quote`
- RSCE : Improved default size attribution in `rsce_testimonials`
- RSCE : Improved default size attribution in `rsce_timeline`
- StyleManager : Added `image_display_mode` options for `image` & `gallery` elements

1.0.24 - 2023-11-02
- Smartgear : fix root pages robotsTxt values depending on development or production mode enabled
- Blog : authors' filter only features users with at least one post

1.0.23 - 2023-10-24
- Smartgear : fix Framway theme configuration file reading
- Smartgear : fix text plural for reminder
- RSCE : Fix `rsce_quoteWRating` texts & classes for some inputs

1.0.22 - 2023-10-13
- Smartgear : fix ajax requests BE being catched by Smartgear when they should not

1.0.21 - 2023-10-12
- Smartgear : Content deletion unallowed even by admin fix
- Smartgear : Dashboard statistics for visits do not register AJAX requests anymore
- Smartgear : Smartgear's API `version` endpoint now returns PHP, Contao `core-bundle` & Framway's version
- Smartgear : Do not register visits if not pointing on a page
- Smartgear : Dashboard now show informations about all root pages domains instead of the one filled in Smartgear's configuration
- Smartgear : Dashboard ticket's creation now sends a confirmation to user + ticket copy to admins
- Smartgear : Users have to select an image size for contents, unless they are granted the expert role
- Smartgear : FileUsage bundle configuration updated
- Smartgear : Adding `##useful_data##` & `##useful_data_filled##` tokens for forms' notifications
- Smartgear : Form to update configuration file available even if SG is not installed
- Smartgear : When checking Framway's installation, the `assets/framway/build/combined/_config.scss` file existence is now checked
- Smartgear : A warning message is displayed in files management in BE, reminding the user that she/he must own the rights to the file before uploading it
- Smartgear : A system to tag items as "to update before"
- Smartgear : Update Manager can now plays migrations without creating a backup
- Smartgear : Backup Manager now split files > 2,5Go in smaller files (avoid memory limit overflow, but isn't faster)
- Smartgear : `smartgear:udpate:update` now accepts `--nobackup` option
- Smartgear : `smartgear:backup:list` now shows backup's source
- Smartgear : reverted to previous rules for `https` & `www` redirections in `.htaccess`
- FormContact : Sender's address is now `##admin_email##` token instead of Smartgear's Owner' email address
- StyleManager : the `rsce_listLogos` now has the same options as `rsce_listIcons`
- StyleManager : the `headline` now has an `alignement` option
- StyleManager : Fix color translation
- Extranet : Fix password requirements
- Extranet : Fix data lost when editing member in FE
- General : bundle now requires [webexmachina/contao-utils](https://github.com/Web-Ex-Machina/contao-utils) ^1.0

1.0.20 - 2023-08-04
- Smartgear : fixing files at `/templates`'s root being deleted when updating Smartgear.

1.0.19 - 2023-07-31
- Smartgear : if a migration's version equals SG one, do not play it
- Smartgear : installation - configuration is updated after each element is created/updated.
- Smartgear : form to update configuration file.
- Form Data Manager : fix an `illegal offset` error when displaying a contact's details.

1.0.18 - 2023-07-25
- Smartgear : better `framway.config.js` file management
- Components : `header` now has a 3rd option for its "sticky" behaviour : "scroll". It allows header to be hidden when scrolling down, and to be shown when scrolling up

1.0.17 - 2023-07-24
- Smartgear : Airtable API now uses 2 different keys. Current functionning with one unified key still works. Support for this will be dropped in Smartgear 2.0

1.0.16 - 2023-07-19
- Smartgear : Fixing some DCA using translation keys not yet defined when loaded
- RSCE : Fixing some RSCE using translation keys not yet defined when loaded
- RSCE : Adding `rsce_quoteWRating`, a component to show quotes with integrated ratings
- RSCE : Updated `rsce_counter`
    + add fields prefix, decimal, separator, icon and color
    + reorganize backend fields placement
    + add translations for new fields
    + adapt template to new fields
- RSCE : Updated `rsce_pricecard`

1.0.15 - 2023-07-17
- Smartgear : Fixing some missing english translation keys

1.0.14 - 2023-07-06
- FormDataManager : The function returning the referer page's ID now has the correct return type

1.0.13 - 2023-06-27
- FormDataManager : The field storing the form field's value is now a `TEXT`

1.0.12 - 2023-06-23
- Smartgear : Updated `rsce_hero`

1.0.11 - 2023-06-15
- Global : PHP 8.2 compatibility

1.0.10 - 2023-05-31
- Smartgear : limitations to some `tl_content` fields are only applied if Smartgear is installed and user isn't a super admin or doesn't have the SG's Core Expert role

1.0.9 - 2023-05-01
- Smartgear : fixes for smartgear reset
- Smartgear : custom language file support in `assets/smartgear/languages/{lang}/custom.json`

1.0.8 - 2023-03-29
- Smartgear : added a logo gallery RSCE
- Smartgear : updated quote RSCE - add an option to make the picture rounded
- Smartgear : updated hero RSCE - allow to have a video as background
- Smartgear : various fix for forms errors

1.0.7 - 2023-03-29
- Smartgear : UpdateManager now synchronize "templates/rsce" & "templates/smartgear" directories

1.0.6 - 2023-03-01
- RSCE : Logo Gallery
- RSCE : Quote - Add an option to make the picture rounded
- RSCE : Hero - Video background support
- Components : Improve form errors

1.0.5 - 2023-01-13
- Smartgear : fix breadcrumb auto-placement
- Smartgear : updated embedded Framway's version
- Dashboard : internal analytics now features most viewed pages without URL parameters
- Dashboard : internal analytics now features most common referers without URL parameters
- Events : do not override list `cal_format` if no filters enabled (fix)
- Events : do override list `cal_format` according to active filters (fix)
- News : translation keys for date filters (fix)
- News : entries for date year filters (fix)

1.0.4 - 2023-01-11
- News : headline in reader page have been removed, replaced by a headline in the event reader module (fixed - same problem as Events before version 1.0.1)
- Smartgear : user id hash sometimes empty (fixed)

1.0.3 - 2023-01-10
- Dashboard : internal links do not opent in a "\_blank" target anymore
- Extranet : updated test member's default password to 12345678
- Events : filters for year & month (fixed)
- Events : geographic coordinates correctly updates when an event's address is changed (fixed)
- Smartgear : do not redirect user to Smartgear's dashboard after a backend login if a Contao's redirection is already happening 
- Smartgear : do not reset layouts' modules on Smartgear reconfigure
- Smartgear : do not use visitor IP address for pages visits statistics
- Smartgear : property "guests" made available again for content element (fixed)
- Smartgear : backend menu "Smartgear" now named "General"
- Smartgear : backend menu entry "undo" now under "General" menu
- Smartgear : backend menu "System" now at the bottom of the list
- Smartgear : backend menu "Personal data" now named "GDPR"

1.0.2 - 2023-01-05
- Smartgear : added missing translation key
- Smartgear : super admins can now delete content elements used by Smartgear (fixed)

1.0.1 - 2023-01-05
- Events : items in event list now have a link in their picture
- Events : year & month filters in list
- Events : day filter in list has been removed
- Events : headline in reader page have been removed, replaced by a headline in the event reader module
- Smartgear : FormContactSent page now has FormContact page as PID
- Smartgear : replacing placeholder images by lorempicsum ones
- Extranet : removed the "random" text
- Extranet : DataModified page removed from navigation
- FormDataManager : warning text added to forms now have a margin top
- Smartgear : Sitemap page now has an headline
- Smartgear : super admins can now delete elements used by Smartgear
- StyleManager : default grid column size implements `d-grid` CSS class
- Smartgear : If the first element of a page is a "hero", Breadcrumb module is now automatically moved under it (this behavious is customizable) 
- Smartgear : some unit tests were fixed
- Smartgear : login actions in backend are logged in a specific table
- Dashboard : statistics do not take into accounts actions done by users logged in backend
- Dashboard : page visits statistics graph now use chartjs library
- Smartgear : added logo for Smartgear, Extranet & Newsletter backend menu entries
- Smartgear : backend menu entries hidden if it contains no item


1.0.0 - 2022-12-14
- 1.0.0-rc2 version is considered stable, releasing 1.0.0

1.0.0-rc2 - 2022-12-14
- Composer: now only have stable dependencies

1.0.0-rc1 - 2022-12-14
- Contao: Contao 4.13 is the only supported version
- Global: Complete rework of internal functionning
- Smartgear: Add API to get various smartgear informations
- Smartgear: Add API to manage updates
- Smartgear: Add API to manage backups
- Smartgear: Reworked template priority (Root > Client > Smartgear + RSCE)

0.8 - 2020-07-x
- Global: Allow Smartgear to execute Contao Commands through the backend
- Global: List templates/smartgear templates in the customTpl options

0.7 - 2020-05-17
- Global: Add a PHPCSFixer set of rules
- Global: Use Symfony bundle architecture
- Global: Update Smartgear Readme
- Global: Add an automatic redirection to https in the htaccess
- Global: Add manager fields to the Smartgear setup (useful to generate automatic licences, etc...)
- Updater: Retrieve ther current version through the system instead of a constant
- Dependancies: Use contao-grid version 0.4

0.6 - 2020-02-15
- Contao: Add Contao 4.8 compatibility
- Contao: Use Framway ModalFW component instead of colorbox plugin
- Smartgear: Add a real updater system
- Smartgear: Add a default templates folder as fallback before using Contao one
- Smartgear: Add a default page for legal notices
- Smartgear: Add a default page for privacy policy
- Smartgear: Add a layout without header and footer
- Smartgear: Improve core setup settings and ergonomy
- Smartgear: Improve automatic generation for news-bundle setup
- Smartgear: Improve 404 page layout
- Smartgear: Increase default timeout values for trash, versions and logs
- RSCE: Add HeroFW wrappers, in order to add several Contao elements into a HeroFW component

0.5 - 2019-09-13
- Smartgear: Add breadcrumb in the setup
- Smartgear: Add API functions
- Smartgear: Adjust permissions
- Smartgear: Override Contao Exceptions (Maintenance)
- Smartgear: Improve connection with the Framway
- Smartgear: Add OutdatedBrowser script 
- Smartgear: Add TarteAuCitron script (RGPD compliance)
- Smartgear: Improve footer generation
- Smartgear: Add legal data to the contact form
- RSCE: Use Framway theme colors instead of fixed ones
- RSCE: Add fallbacks to image thumbnails generations
- RSCE: Add timeline
- RSCE: Add quote
- RSCE: Add listIcons
- RSCE: Add a variant to block-img

0.4 - 2019-02-23
- Routing: Use "/" to define homepage and catch empty requests to generate correct urls
- Install tool: Force "/" alias in homepage generation instead of title alias
- Remove the "Disallow" option added in robots when an install is done
- RSCE: Add option to force sliderFW full-with
- RSCE: Add option to add CSS classes to author field in testimonialsFW
- RSCE: Fix a testimonialsFW issue with the "-" char
- Header: Rename the module name, according to Contao rules
- Header: Update the default module nav in header module to use the new name
- MSC: Update global headers
- MSC: Apply PHPCS rules
- MSC: Fix backend helpers

0.3 - 2019-02-18
- i18nl10n fixes

0.2 - 2019-02-09
- RSCE: Add priceCards
- RSCE: Add gridGallery
- RSCE: Add notations
- RSCE: Fix a bug in heroFW
- Contao Element: Override hyperlink
- Smartgear: Add blog configuration
- Improve user default generation

0.1 - 2018-04-19
- Init git repository
- Adjust FR translations
