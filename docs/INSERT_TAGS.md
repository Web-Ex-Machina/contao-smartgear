INSERT TAGS
==================

The purpose of this document is to list all insert tags available.

core
---------

Tags | Value | Description
--- | --- | ---
`sg::installComplete` | string | `1` if the core component is installed, `0` otherwise
`sg::version` | string | The current version
`sg::framwayPath` | string | Path to the framway
`sg::framwayThemes` | string | Comma separated list of all framway themes
`sg::googleFonts` | string | Comma separated list of Google Fonts
`sg::selectedModules` | string | Comma separated list of framway modules
`sg::mode` | string | `dev` or `prod`
`sg::websiteTitle` | string | The website's title
`sg::ownerEmail` | string | The website owner's email
`sg::analytics` | string | The analytics solution selected
`sg::analyticsGoogleId` | string | The Google Analytics ID
`sg::analyticsMatomoHost` | string | The Matomo host
`sg::analyticsMatomoId` | string | The Matomo id
`sg::ownerName` | string | The website owner's name
`sg::ownerDomain` | string | The website owner's domain
`sg::ownerHost` | string | The website owner's host
`sg::ownerLogo` | string | The website owner's logo path
`sg::ownerStatus` | string | The website owner's legal status
`sg::ownerStreet` | string | The website owner's street
`sg::ownerPostal` | string | The website owner's postal code
`sg::ownerCity` | string | The website owner's city
`sg::ownerRegion` | string | The website owner's region
`sg::ownerCountry` | string | The website owner's country
`sg::ownerSiret` | string | The website owner's SIRET
`sg::ownerDpoName` | string | The website owner's Data Protection Officer's name
`sg::ownerDpoEmail` | string | The website owner's Data Protection Officer email
`sg::theme` | int | The Contao's theme ID
`sg::rootPage` | int | The Contao's rootpage ID
`sg::modules` | string | Comma separated list of modules
`sg::apiKey` | string | The API key

blog
---------

Tags | Value | Description
--- | --- | ---
`sg::blog_installComplete` | string | `1` if the blog component is installed, `0` otherwise
`sg::blog_newsArchive` | int | The blog's news archive ID
`sg::blog_page` | int | The blog's page ID
`sg::blog_moduleReader` | int | The blog's module reader ID
`sg::blog_moduleList` | int | The blog's module list ID
`sg::blog_currentPresetIndex` | int | The blog's current preset used
`sg::blog_archived` | string |  `1` if the blog component is archived, `0` otherwise
`sg::blog_archivedAt` | string | The timestamp the blog component has been archived at
`sg::blog_archivedMode` | string | The mode the blog component has been archived with (`archive`,`keep` or `delete`)
`sg::blog_newsFolder` | string | The folder where news' files are stored
`sg::blog_newsArchiveTitle` | string | The blog's news archive title
`sg::blog_newsListPerPage` | int | The blog's number of news per page
`sg::blog_newsPageTitle` | string | The blog's page title


events / calendar
------------------

Tags | Value | Description
--- | --- | ---
`sg::events_installComplete` | string | `1` if the calendar component is installed, `0` otherwise
`sg::events_calendar` | int | The calendar's calendar ID
`sg::events_page` | int | The calendar's page ID
`sg::events_moduleReader` | int | The calendar's module reader ID
`sg::events_moduleList` | int | The calendar's module list ID
`sg::events_moduleCalendar` | int | The calendar's module calendar ID
`sg::events_archived` | string |  `1` if the calendar component is archived, `0` otherwise
`sg::events_archivedAt` | string | The timestamp the calendar component has been archived at
`sg::events_archivedMode` | string | The mode the calendar component has been archived with (`archive`,`keep` or `delete`)
`sg::events_eventsFolder` |  string | The folder where calendar's files are stored
`sg::events_calendarTitle` | string | The calendar's calendar title
`sg::events_eventsListPerPage` | int | The calendar's number of evenst per page
`sg::events_eventsPageTitle` | string | The calendar's page title