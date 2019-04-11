var swiper_container = $('.swiper-container');
$(document).ready(function () {
    var swiper_wrapper = $('#swiper-wrapper');
    var widthHotTrending = swiper_wrapper.width();
    var widthDevice = $(window).width();
    swiperHotTrendingByDevice(widthHotTrending, widthDevice);
    // su kien chuyen kich thuoc man hinh
    window.addEventListener("orientationchange", function () {
        var widthPhone = screen.width;
        swiperHotTrendingChangeScreen(widthHotTrending, widthPhone);
    }, false);

    function swiperHotTrendingByDevice(wTrending, wMobile) {
        if (wTrending > wMobile) {
            swiper_wrapper.lightSlider({
                autoWidth: false,
                loop: false,
                enableDrag: true,
                enableTouch: true,
                slideMargin: 10,
                adaptiveHeight: true,
                controls: false,
                pager: false,
                onSliderLoad: function (el) {
                    swiper_wrapper.removeClass('cs-hidden');
                    CheckonBeforeSlide(el);
                },
                onBeforeSlide: function (el) {
                    CheckonBeforeSlide(el);
                }
            });
        } else {
            swiper_wrapper.removeClass('cs-hidden');
            swiper_container.removeClass('active');
        }
    }

    function swiperHotTrendingChangeScreen(wTrending, wMobile) {
        if (wTrending > wMobile) {
            swiper_wrapper.lightSlider({
                autoWidth: false,
                loop: false,
                enableDrag: true,
                enableTouch: true,
                slideMargin: 10,
                adaptiveHeight: true,
                controls: false,
                pager: false,
                onSliderLoad: function (el) {
                    swiper_wrapper.removeClass('cs-hidden');
                    CheckonBeforeSlide(el);
                },
                onBeforeSlide: function (el) {
                    CheckonBeforeSlide(el);
                }
            });
        } else {
            swiper_wrapper.lightSlider({
                autoWidth: true,
                loop: false,
                enableDrag: true,
                enableTouch: true,
                slideMargin: 10,
                adaptiveHeight: true,
                controls: false,
                pager: false,
                onSliderLoad: function (el) {
                    swiper_wrapper.removeClass('cs-hidden');
                    CheckonBeforeSlide(el);
                },
                onBeforeSlide: function (el) {
                    CheckonBeforeSlide(el);
                }
            });
        }
    }
});

function CheckonBeforeSlide(el) {
    var currentCount = el.getCurrentSlideCount();
    var total = el.getTotalSlideCount();
    if ((currentCount + 3) >= total) {
        swiper_container.removeClass('active');
    } else {
        swiper_container.addClass('active');
    }
}