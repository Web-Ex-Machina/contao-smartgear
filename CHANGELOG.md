SMARTGEAR Project for Contao Open Source CMS
========

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
