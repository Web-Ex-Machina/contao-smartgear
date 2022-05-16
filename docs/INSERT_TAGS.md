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
`sg::theme` | string | The Contao's theme ID
`sg::layoutStandard` | string | The Contao's "standard" layout ID
`sg::layoutFullwidth` | string | The Contao's "fullwidth" layout ID
`sg::pageRoot` | string | The Contao's rootpage ID
`sg::pageHome` | string | The Contao's homepage ID
`sg::page404` | string | The Contao's 404 error page ID
`sg::pageLegalNotice` | string | The Contao's legal notice page ID
`sg::pagePrivacyPolitics` | string | The Contao's privacy politics page ID
`sg::pageSitemap` | string | The Contao's sitemap page ID
`sg::articleHome` | string | The Contao's home page article ID
`sg::article404` | string | The Contao's 404 page article ID
`sg::articleLegalNotice` | string | The Contao's legal notice page article ID
`sg::articlePrivacyPolitics` | string | The Contao's privacy politics page article ID
`sg::articleSitemap` | string | The Contao's sitemap page article ID
`sg::content404Headline` | string | The Contao's 404 page headline content ID
`sg::content404Sitemap` | string | The Contao's 404 page sitemap content ID
`sg::contentLegalNotice` | string | The Contao's legal notice page content ID
`sg::contentPrivacyPolitics` | string | The Contao's privacy politics page content ID
`sg::contentSitemap` | string | The Contao's sitemap page content ID
`sg::notificationGatewayEmail` | int | The Contao's email notification gateway ID
`sg::modules` | string | Comma separated list of modules
`sg::apiKey` | string | The API key
`sg::socialLinks` | string | The social network links buttons


blog
---------

Tags | Value | Description
--- | --- | ---
`sg::blog_installComplete` | string | `1` if the blog component is installed, `0` otherwise
`sg::blog_newsArchive` | string | The blog's news archive ID
`sg::blog_page` | string | The blog's page ID
`sg::blog_article` | string | The blog's page article ID
`sg::blog_contentHeadline` | string | The blog's page article headline content ID
`sg::blog_contentList` | string | The blog's page article list content ID
`sg::blog_moduleReader` | string | The blog's module reader ID
`sg::blog_moduleList` | string | The blog's module list ID
`sg::blog_currentPresetIndex` | string | The blog's current preset used
`sg::blog_archived` | string |  `1` if the blog component is archived, `0` otherwise
`sg::blog_archivedAt` | string | The timestamp the blog component has been archived at
`sg::blog_archivedMode` | string | The mode the blog component has been archived with (`archive`,`keep` or `delete`)
`sg::blog_newsFolder` | string | The folder where news' files are stored
`sg::blog_newsArchiveTitle` | string | The blog's news archive title
`sg::blog_newsListPerPage` | string | The blog's number of news per page
`sg::blog_newsPageTitle` | string | The blog's page title


events / calendar
------------------

Tags | Value | Description
--- | --- | ---
`sg::events_installComplete` | string | `1` if the calendar component is installed, `0` otherwise
`sg::events_calendar` | int | The calendar's calendar ID
`sg::events_page` | int | The calendar's page ID
`sg::events_article` | string | The calendar's page article ID
`sg::events_contentHeadline` | string | The calendar's page article headline content ID
`sg::events_contentList` | string | The calendar's page article list content ID
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

FAQ
------------------

Tags | Value | Description
--- | --- | ---
`sg::faq_installComplete` | string | `1` if the faq component is installed, `0` otherwise
`sg::faq_faqCategory` | int | The faq's category ID
`sg::faq_page` | int | The faq's page ID
`sg::faq_article` | string | The faq's page article ID
`sg::faq_content` | string | The faq's page article content ID
`sg::faq_moduleFaq` | int | The faq's module faq ID
`sg::faq_archived` | string |  `1` if the faq component is archived, `0` otherwise
`sg::faq_archivedAt` | string | The timestamp the faq component has been archived at
`sg::faq_archivedMode` | string | The mode the faq component has been archived with (`archive`,`keep` or `delete`)
`sg::faq_faqFolder` |  string | The folder where faq's files are stored
`sg::faq_faqTitle` | string | The faq's faq title
`sg::faq_faqPageTitle` | string | The faq's page title