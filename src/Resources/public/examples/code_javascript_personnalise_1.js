<script src="{{config.framway.path}}/build/js/vendor.js"></script>
<script src="{{config.framway.path}}/build/js/framway.js"></script>
<script src="assets/smartgear/social-share-buttons/js/social-share-buttons.js"></script>
<script  type="text/javascript">
//call plugin function after DOM ready
window.addEventListener("load", function(e) {
    SM.socialShareButtons.init();
}); 
</script>

<script type="text/javascript">
// -- GTAG
// tarteaucitron.user.gtagUa = 'UA-XXXXXXXXX-X';
tarteaucitron.user.gtagUa = '{{config.analytics.google.id}}';
tarteaucitron.user.gtagMore = function () {
/* add here your optionnal gtag() */ 
};
(tarteaucitron.job = tarteaucitron.job || []).push('gtag');
// -- /GTAG
// -- MATOMO
tarteaucitron.user.matomoHost = '{{config.analytics.matomo.host}}';
tarteaucitron.user.matomoId = {{config.analytics.matomo.id}};
(tarteaucitron.job = tarteaucitron.job || []).push('matomo');
// -- /MATOMO
// -- GFONT
tarteaucitron.user.googleFonts = [{{config.googleFonts}}];
(tarteaucitron.job = tarteaucitron.job || []).push('googlefonts');
// -- /GFONT
</script>
