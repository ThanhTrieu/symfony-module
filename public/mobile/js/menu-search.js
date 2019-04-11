/** @param {jQuery} $ jQuery Object */
!function($, window, document)
{
    if($('.js-menu-trigger').length){
        $('.js-menu-trigger').on('click', function(e){
            e.preventDefault();
            $(this).toggleClass('active');
            $('.js-menu').toggleClass('show');
            $('.js-search-form').removeClass('show');
            $('.js-search-trigger').removeClass('active');
            $('.js-icon-close').removeClass('cls').addClass('icon-search');
            if($(this).hasClass('active')){
                $(this).removeClass('bt-menu').addClass('cls');
            } else {
                $(this).addClass('bt-menu').removeClass('cls');
            }
            checkPopup($(this));
        });
    }
    if($('.js-search-trigger').length){
        $('.js-search-trigger').on('click', function(e){
            e.preventDefault();
            $(this).toggleClass('active');
            $('.js-search-form').toggleClass('show');
            $('.js-menu').removeClass('show');
            $('.js-menu-trigger').removeClass('active');
            $('.js-menu-trigger').addClass('bt-menu').removeClass('cls');
            if($(this).hasClass('active')){
                $('.js-icon-close').removeClass('icon-search').addClass('cls');
            } else {
                $('.js-icon-close').removeClass('cls').addClass('icon-search');
            }
            checkPopup($(this));
        });
    }

    $('#popupMenuSearch').on('click', function (e) {
        e.preventDefault();
        $('.js-menu').removeClass('show');
        $('.js-menu-trigger').removeClass('active');
        $('.js-menu-trigger').addClass('bt-menu').removeClass('cls');

        $('.js-search-form').removeClass('show');
        $('.js-search-trigger').removeClass('active');
        $('.js-icon-close').removeClass('cls').addClass('icon-search');

        checkPopup($(this));
    });


    var checkPopup = function(obj) {
        $(document).on('click touchstart', function()
        {
            var heightBody =  document.documentElement.clientHeight;
            var widthBody  = document.documentElement.clientWidth;
            if (obj.hasClass('active'))
            {
                $('html, body').css({
                    overflow: 'hidden'
                });
                $('#container-main').css({
                    opacity: 0.4
                });
                $('#popupMenuSearch').css({
                    height: heightBody +'px',
                    width: widthBody+'px'
                }).addClass('active-popup')
            } else {
                $('html, body').css({
                    overflow: 'auto'
                });
                $('#container-main').css({
                    opacity: 1
                });
                $('#popupMenuSearch').css({
                    height: '100%',
                    width: '100%'
                }).removeClass('active-popup');
            }
        });
    };

    var prevScrollpos = window.pageYOffset;
    function showHideScrollTop() {
        var currentScrollPos = window.pageYOffset;
        if (prevScrollpos > currentScrollPos && $(window).scrollTop() > $(window).height()/2) {
            $("#scroll-up").stop(true, true).show();
        } else {
            $("#scroll-up").stop(true, true).hide();
        }
        prevScrollpos = currentScrollPos;
    }
    showHideScrollTop();
    $(window).scroll(function() {
        showHideScrollTop();
    });

    $("#scroll-up").on('click', function(e){
        e.preventDefault();
        $('html,body').animate({scrollTop:0}, 300);
    });
}
(jQuery, window, document);
