<script src="https://kit.fontawesome.com/373ba659bc.js" crossorigin="anonymous"></script>
<link rel="stylesheet" href="{{config.framway.path}}/build/css/vendor.css">
<link rel="stylesheet" href="{{config.framway.path}}/build/css/framway.css">
<script type="text/javascript" src="assets/tarteaucitron/tarteaucitron.js"></script>
<script type="text/javascript">
var tarteaucitronForceLanguage = '{{page::language}}';
tarteaucitron.init({
    "privacyUrl": "{{config.core.page.privacy.url}}", /* Privacy policy url */

    "hashtag": "#tarteaucitron", /* Open the panel with this hashtag */
    "cookieName": "tarteaucitron", /* Cookie name */

    "orientation": "bottom", /* Banner position (top - bottom) */
    "showAlertSmall": false, /* Show the small banner on bottom right */
    "cookieslist": true, /* Show the cookie list */

    "adblocker": false, /* Show a Warning if an adblocker is detected */
    "AcceptAllCta" : true, /* Show the accept all button when highPrivacy on */
    "highPrivacy": false, /* Disable auto consent */
    "handleBrowserDNTRequest": false, /* If Do Not Track == 1, disallow all */

    "removeCredit": true, /* Remove credit link */
    "moreInfoLink": true, /* Show more info link */
    "useExternalCss": false, /* If false, the tarteaucitron.css file will be loaded */
});
</script>
