// var images = document.querySelectorAll('#container img');
// console.log(images);
// for (var i = 0; i < images.length; i++) {
//   // images[i].src="";
// }

$(function () {
  // INIT
    $(window).resize(function () {
        $('body.home .heroFW').css('height',viewport.height - $('#header').outerHeight());

        if ($('.mod_faqpage .accordionFW').length) {
            resizeFAQ();
        }
        if ($('table.calendar').length) {
            toggleCalendarEventView();
        }
    }).trigger('resize');

    $('.mod_wem_locations_map').attr('id','mapWrapper');

    $('table.calendar').each(function (index,$calendar) {
        $calendar = $($calendar);
        $calendar.find('.event').each(function (index,$event) {
            $event = $($event);
            setTimeout(function () {
                $event.addClass('active');
            },100*index);
        });
        $calendar.prev('.loader').remove();
    });
    $('.calendar__event').each(function () {
        $(this).css('background-color',utils.stringToColor($(this).text()));
    });



    $('body').on('click', 'a[data-lightbox]', function(e) {
      e.preventDefault();
      if (!$('.modalFW[data-name='+$(this).attr('data-lightbox')+']').length) {
        $(this).attr('data-modal', $(this).attr('data-lightbox'));
        new app.ModalFW({
          name: $(this).attr('data-lightbox'),
          url: $(this).attr('href'),
        }).open();
      }
    });
});

var toggleCalendarEventView = function toggleCalendarEventView()
{
    $('table.calendar').find('.days.active').each(function (index,$day) {
        $day = $($day);
        var offseted = false;
        $day.find('.event').unwrap('.reduced').each(function () {
            if ($(this).position().top <= 0) {
                offseted = true;
            }
        });
        if (offseted) {
            $day.find('.event').wrapAll('<div class="reduced"></div>');
        }
    });
};

var resizeFAQ = function resizeFAQ()
{
    var $titles = $('.mod_faqpage .accordionFW__item .accordionFW__title').css('width','auto');
    var maxWidth = 0;
    $titles.each(function () {
        if (this.scrollWidth+10 > maxWidth) {
            maxWidth = this.scrollWidth+10;
        }
    });
    $titles.outerWidth(maxWidth);
}
