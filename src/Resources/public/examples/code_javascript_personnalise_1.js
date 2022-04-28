<script src="assets/outdatedbrowser/outdatedbrowser.min.js"></script>
<script src="{{config.framway.path}}/build/js/vendor.js"></script>
<script src="{{config.framway.path}}/build/js/framway.js"></script>
<script src="assets/smartgear/social-share-buttons/js/social-share-buttons.js"></script>
<div id="outdated">
    <h6>Your browser is out-of-date!</h6>
    <p>Update your browser to view this website correctly. <a id="btnUpdateBrowser" href="http://outdatedbrowser.com/">Update my browser now </a></p>
    <p class="last"><a href="#" id="btnCloseUpdateBrowser" title="Close">×</a></p>
</div>
<script>
// Plain Javascript
//event listener: DOM ready
function addLoadEvent(func)
{
    var oldonload = window.onload;
    if (typeof window.onload != 'function') {
        window.onload = func;
    } else {
        window.onload = function () {
            if (oldonload) {
                oldonload();
            }
            func();
        }
    }
}
//call plugin function after DOM ready
addLoadEvent(function () {
    outdatedBrowser({
        bgColor: '#f25648',
        color: '#ffffff',
        lowerThan: 'object-fit',
        languagePath: 'assets/outdatedbrowser/lang/{{page::language}}.html'
    });
    SM.socialShareButtons.init();
});

</script>

<script type="text/javascript">
tarteaucitron.user.gtagUa = 'UA-XXXXXXXXX-X';
tarteaucitron.user.gtagMore = function () {
 /* add here your optionnal gtag() */ };
(tarteaucitron.job = tarteaucitron.job || []).push('gtag');
tarteaucitron.user.googleFonts = [{{config.googleFonts}}];
(tarteaucitron.job = tarteaucitron.job || []).push('googlefonts');
</script>
