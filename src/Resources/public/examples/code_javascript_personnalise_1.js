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
tarteaucitron.user.gtagUa = 'UA-XXXXXXXXX-X';
tarteaucitron.user.gtagMore = function () {
/* add here your optionnal gtag() */ 
};
(tarteaucitron.job = tarteaucitron.job || []).push('gtag');
tarteaucitron.user.googleFonts = [{{config.googleFonts}}];
(tarteaucitron.job = tarteaucitron.job || []).push('googlefonts');
</script>
