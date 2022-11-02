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
`sg::blog_newsFolder` | string | The folder where news' files are stored
`sg::blog_newsArchiveTitle` | string | The blog's news archive title
`sg::blog_newsListPerPage` | string | The blog's number of news per page
`sg::blog_newsPageTitle` | string | The blog's page title
`sg::blog_archived` | string |  `1` if the blog component is archived, `0` otherwise
`sg::blog_archivedAt` | string | The timestamp the blog component has been archived at
`sg::blog_archivedMode` | string | The mode the blog component has been archived with (`archive`,`keep` or `delete`)


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
`sg::events_eventsFolder` |  string | The folder where calendar's files are stored
`sg::events_calendarTitle` | string | The calendar's calendar title
`sg::events_eventsListPerPage` | int | The calendar's number of evenst per page
`sg::events_eventsPageTitle` | string | The calendar's page title
`sg::events_archived` | string |  `1` if the calendar component is archived, `0` otherwise
`sg::events_archivedAt` | string | The timestamp the calendar component has been archived at
`sg::events_archivedMode` | string | The mode the calendar component has been archived with (`archive`,`keep` or `delete`)

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
`sg::faq_faqFolder` |  string | The folder where faq's files are stored
`sg::faq_faqTitle` | string | The faq's faq title
`sg::faq_faqPageTitle` | string | The faq's page title
`sg::faq_archived` | string |  `1` if the faq component is archived, `0` otherwise
`sg::faq_archivedAt` | string | The timestamp the faq component has been archived at
`sg::faq_archivedMode` | string | The mode the faq component has been archived with (`archive`,`keep` or `delete`)


Extranet
------------------

Tags | Value | Description
--- | --- | ---
`sg::extranet_installComplete` | string | `1` if the extranet module is installed, `0` otherwise
`sg::extranet_canSubscribe` | string | `1` if users can register, `0` otherwise
`sg::extranet_memberExample` | string | The example member's ID
`sg::extranet_memberGroupMembers` | string | The group members' ID
`sg::extranet_memberGroupMembersTitle` | string | The group members' title
`sg::extranet_pageExtranetTitle` | string | The extranet page's title
`sg::extranet_pageExtranet` | int | The Extranet page's ID 
`sg::extranet_page401` | int | The 401 page's ID 
`sg::extranet_page403` | int | The 403 page's ID 
`sg::extranet_pageContent` | int | The Content page's ID 
`sg::extranet_pageData` | int | The Data page's ID 
`sg::extranet_pageDataConfirm` | int | The Data Confirm page's ID 
`sg::extranet_pagePassword` | int | The Password page's ID 
`sg::extranet_pagePasswordConfirm` | int | The Password Confirm page's ID 
`sg::extranet_pagePasswordValidate` | int | The Password Validate page's ID 
`sg::extranet_pageLogout` | int | The Logout page's ID 
`sg::extranet_pageSubscribe` | int | The Subscribe page's ID (`null` if users can't register)
`sg::extranet_pageSubscribeConfirm` | int | The Subscribe Confirm page's ID (`null` if users can't register)
`sg::extranet_pageSubscribeValidate` | int | The Subscribe Validate page's ID (`null` if users can't register)
`sg::extranet_pageUnsubscribeConfirm` | int | The Unsubscribe Confirm page's ID (`null` if users can't register)
`sg::extranet_articleExtranet` | int | The Extranet article's ID
`sg::extranet_article401` | int | The 401 article's ID
`sg::extranet_article403` | int | The 403 article's ID
`sg::extranet_articleContent` | int | The Content article's ID
`sg::extranet_articleData` | int | The Data article's ID
`sg::extranet_articleDataConfirm` | int | The DataConfirm article's ID
`sg::extranet_articlePassword` | int | The Password article's ID
`sg::extranet_articlePasswordConfirm` | int | The PasswordConfirm article's ID
`sg::extranet_articlePasswordValidate` | int | The PasswordValidate article's ID
`sg::extranet_articleLogout` | int | The Logout article's ID
`sg::extranet_articleSubscribe` | int | The Subscribe article's ID (`null` if users can't register)
`sg::extranet_articleSubscribeConfirm` | int | The SubscribeConfirm article's ID (`null` if users can't register)
`sg::extranet_articleSubscribeValidate` | int | The SubscribeValidate article's ID (`null` if users can't register)
`sg::extranet_articleUnsubscribeConfirm` | int | The UnsubscribeConfirm article's ID (`null` if users can't register)
`sg::extranet_moduleLogin` | int | The Login module's ID
`sg::extranet_moduleLogout` | int | The Logout module's ID
`sg::extranet_moduleData` | int | The Data module's ID
`sg::extranet_modulePassword` | int | The Password module's ID
`sg::extranet_moduleNav` | int | The Nav module's ID
`sg::extranet_moduleSubscribe` | int | The Subscribe module's ID (`null` if users can't register)
`sg::extranet_moduleCloseAccount` | int | The CloseAccount module's ID (`null` if users can't register)
`sg::extranet_notificationChangeData` | int | The ChangeData notification's ID
`sg::extranet_notificationChangeDataMessage` | int | The ChangeData notification Message's ID
`sg::extranet_notificationChangeDataMessageLanguage` | int | The ChangeData notification Message Language's ID
`sg::extranet_notificationPassword` | int | The Password notification's ID
`sg::extranet_notificationPasswordMessage` | int | The Password notification Message's ID
`sg::extranet_notificationPasswordMessageLanguage` | int | The Password notification Message Language's ID
`sg::extranet_notificationSubscription` | int | The Subscription notification's ID (`null` if users can't register)
`sg::extranet_notificationSubscriptionMessage` | int | The Subscription notification Message's ID (`null` if users can't register)
`sg::extranet_notificationSubscriptionMessageLanguage` | int | The Subscription notification Message Language's ID (`null` if users can't register)
`sg::extranet_contentArticleExtranetHeadline` | int | The Article Extranet Headline content ID
`sg::extranet_contentArticleExtranetModuleLoginGuests` | int | The Article Extranet Module Login (Guests) content ID
`sg::extranet_contentArticleExtranetGridStartA` | int | The Article Extranet Grid Start A content ID
`sg::extranet_contentArticleExtranetGridStartB` | int | The Article Extranet Grid Start B content ID
`sg::extranet_contentArticleExtranetModuleLoginLogged` | int | The Article Extranet Module Login (Logged users) content ID
`sg::extranet_contentArticleExtranetModuleNav` | int | The Article Extranet Module Nav content ID
`sg::extranet_contentArticleExtranetGridStopB` | int | The Article Extranet Grid Stop B content ID
`sg::extranet_contentArticleExtranetText` | int | The Article Extranet Text content ID
`sg::extranet_contentArticleExtranetGridStopA` | int | The Article Extranet Grid Stop A content ID
`sg::extranet_contentArticle401Headline` | int | The Article 401 Headline content ID
`sg::extranet_contentArticle401Text` | int | The Article 401 Text content ID
`sg::extranet_contentArticle401ModuleLoginGuests` | int | The Article 401 Module Login (Guests) content ID
`sg::extranet_contentArticle403Headline` | int | The Article 403 Headline content ID
`sg::extranet_contentArticle403Text` | int | The Article 403 Text content ID
`sg::extranet_contentArticle403Hyperlink` | int | The Article 40 3Hyperlink content ID
`sg::extranet_contentArticleContentHeadline` | int | The Article Content Headline content ID
`sg::extranet_contentArticleContentText` | int | The Article Content Text content ID
`sg::extranet_contentArticleDataHeadline` | int | The Article Data Headline content ID
`sg::extranet_contentArticleDataModuleData` | int | The Article Data Module Data content ID
`sg::extranet_contentArticleDataHeadlineCloseAccount` | int | The Article Data Headline Close Account content ID (`null` if users can't register)
`sg::extranet_contentArticleDataTextCloseAccount` | int | The Article Data Text Close Account content ID (`null` if users can't register)
`sg::extranet_contentArticleDataModuleCloseAccount` | int | The Article Data Module Close Account content ID (`null` if users can't register)
`sg::extranet_contentArticleDataConfirmHeadline` | int | The Article Data Confirm Headline content ID
`sg::extranet_contentArticleDataConfirmText` | int | The Article Data Confirm Text content ID
`sg::extranet_contentArticleDataConfirmHyperlink` | int | The Article Data Confirm Hyperlink content ID
`sg::extranet_contentArticlePasswordHeadline` | int | The Article Password Headline content ID
`sg::extranet_contentArticlePasswordModulePassword` | int | The Article Password Module Password content ID
`sg::extranet_contentArticlePasswordConfirmHeadline` | int | The Article Password Confirm Headline content ID
`sg::extranet_contentArticlePasswordConfirmText` | int | The Article Password Confirm Text content ID
`sg::extranet_contentArticlePasswordValidateHeadline` | int | The Article Password Validate Headline content ID
`sg::extranet_contentArticlePasswordValidateModulePassword` | int | The Article Password Validate Module Password content ID
`sg::extranet_contentArticleLogoutModuleLogout` | int | The Article Logout Module Logout content ID
`sg::extranet_contentArticleSubscribeHeadline` | int | The Article Subscribe Headline content ID (`null` if users can't register)
`sg::extranet_contentArticleSubscribeModuleSubscribe` | int | The Article Subscribe Module Subscribe content ID (`null` if users can't register)
`sg::extranet_contentArticleSubscribeConfirmHeadline` | int | The Article Subscribe Confirm Headline content ID (`null` if users can't register)
`sg::extranet_contentArticleSubscribeConfirmText` | int | The Article Subscribe Confirm Text content ID (`null` if users can't register)
`sg::extranet_contentArticleSubscribeValidateHeadline` | int | The Article Subscribe Validate Headline content ID (`null` if users can't register)
`sg::extranet_contentArticleSubscribeValidateText` | int | The Article Subscribe Validate Text content ID (`null` if users can't register)
`sg::extranet_contentArticleSubscribeValidateModuleLoginGuests` | int | The Article Subscribe Validate Module Login (Guests) content ID (`null` if users can't register)
`sg::extranet_contentArticleUnsubscribeHeadline` | int | The Article Unsubscribe Headline content ID (`null` if users can't register)
`sg::extranet_contentArticleUnsubscribeText` | int | The Article Unsubscribe Text content ID (`null` if users can't register)
`sg::extranet_contentArticleUnsubscribeHyperlink` | int | The Article Unsubscribe Hyperlink content ID (`null` if users can't register)
`sg::extranet_archived` | string |  `1` if the extranet module is archived, `0` otherwise
`sg::extranet_archivedAt` | string | The timestamp the extranet module has been archived at
`sg::extranet_archivedMode` | string | The mode the extranet module has been archived with (`archive`,`keep` or `delete`)

FormContact
------------------

Tags | Value | Description
--- | --- | ---
`sg::formContact_installComplete` | string | `1` if the Form Contact component is installed, `0` otherwise
`sg::formContact_formContactTitle` |string | The form contact's title
`sg::formContact_pageTitle` |string | The page containing the form title
`sg::formContact_pageForm` | int | The page containing the form ID
`sg::formContact_pageFormSent` | int | The page to redirect user after form submission ID
`sg::formContact_articleForm` | int | The page containing the form's article ID
`sg::formContact_articleFormSent` | int | The page to redirect user after form submission's article ID
`sg::formContact_contentHeadlineArticleForm` | int | The headline in page's article containing form ID
`sg::formContact_contentFormArticleForm` | int | The form in page's article containing form ID
`sg::formContact_contentHeadlineArticleFormSent` | int | The headline in page's article redirected to after form submission ID
`sg::formContact_contentTextArticleFormSent` | int | The text in page's article redirected to after form submission ID
`sg::formContact_formContact` | int | The form's ID
`sg::formContact_fieldName` | int | The "name" field ID
`sg::formContact_fieldEmail` | int |  The "email" field ID
`sg::formContact_fieldMessage` | int |  The "message" field ID
`sg::formContact_fieldConsentDataTreatment` | int |  The "consent data treatment" field ID
`sg::formContact_fieldConsentDataTreatmentExplanation` | int |  The "consent data treatment explanation" field ID
`sg::formContact_fieldConsentDataSave` | int |  The "consent data save" field ID
`sg::formContact_fieldConsentDataSaveExplanation` | int |  The "consent data save explanation" field ID
`sg::formContact_fieldCaptcha` | int |  The "captcha" field ID
`sg::formContact_fieldSubmit` | int |  The "submit" field ID
`sg::formContact_notification` | int | The form submission notification's ID
`sg::formContact_notificationMessageUser` | int | The form submission notification message to user ID
`sg::formContact_notificationMessageAdmin` | int | The form submission notification message to admin ID
`sg::formContact_notificationMessageUserLanguage` | int | The form submission notification message language to user ID
`sg::formContact_notificationMessageAdminLanguage` | int |  The form submission notification message language to admin ID
`sg::formContact_archived` |string |   `1` if the Form Contact component is archived, `0` otherwise
`sg::formContact_archivedAt` |string |  The timestamp the Form Contact component has been archived at
`sg::formContact_archivedMode` |string |  The mode the Form Contact component has been archived with (`archive`,`keep` or `delete`)

FormDataManager
------------------

Tags | Value | Description
--- | --- | ---
`sg::formDataManager_installComplete` | string | `1` if the Form Data Manager module is installed, `0` otherwise
`sg::formDataManager_archived` |string |   `1` if the Form Data Manager module is archived, `0` otherwise
`sg::formDataManager_archivedAt` |string |  The timestamp the Form Data Manager module has been archived at
`sg::formDataManager_archivedMode` |string |  The mode the Form Data Manager module has been archived with (`delete`)