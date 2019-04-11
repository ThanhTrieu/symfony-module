// Call to all jquery ago
$.timeago.settings.strings = {
    prefixAgo: null,
    prefixFromNow: null,
    suffixAgo: "ago",
    suffixFromNow: "from now",
    inPast: 'any moment now',
    seconds: "%d seconds",
    minute: "a min",
    minutes: "%d mins",
    hour: "an hour",
    hours: "%d hours",
    day: "a day",
    days: "%d days",
    month: "a month",
    months: "%d months",
    year: "a year",
    years: "%d years",
    wordSeparator: " ",
    numbers: []
};
$('.time-ago').timeago();

// Stream data
$.fn.postStreamer = function () {
    if (this.length == 0) return;
    var btnLoadMore = $('#btn-load-more');
    var loadingPanel = $('#loading-panel');
    var hiddenClass = 'hidden';

    var isLoading = false;

    function loadStream() {
        isLoading = true;
        var ajaxUrl = btnLoadMore.data('url'), loadMoreToken = decodeURIComponent(btnLoadMore.attr('data-token'));
        loadingPanel.removeClass(hiddenClass);
        $.get(ajaxUrl, {loadMoreToken: loadMoreToken}, function (data) {
            if (data && data.success) {
                $(data.html).insertBefore(loadingPanel);
                $('.time-ago').timeago();
            }
            if (data.loadMoreToken == null) {
                loadingPanel.remove();
                btnLoadMore.remove();
                $(window).off('scroll', scrollLoadPost);
            } else {
                btnLoadMore.attr('data-token', data.loadMoreToken);
                if (data.showViewMore == true) {
                    $(window).off('scroll', scrollLoadPost);
                    btnLoadMore.removeClass(hiddenClass);
                } else {
                    $(window).on('scroll', scrollLoadPost);
                    btnLoadMore.addClass(hiddenClass);
                }
                loadingPanel.addClass(hiddenClass);
            }
            isLoading = false;
        });
    }

    function scrollLoadPost() {
        if (!isLoading && $(window).scrollTop() > homeStreamPanel.find('li:last').offset().top - $(window).height()) {
            loadStream();
        }
    }

    var homeStreamPanel = $(this);
    // Register scroll event
    $(window).on('scroll', scrollLoadPost);
    // Register load more button
    btnLoadMore.click(function () {
        $(this).addClass(hiddenClass);
        loadStream();
    });
};

// count view - TrieuNT
var countView = (function () {
    function updateView(type, id) {
        var i = new Image(1, 1);
        i.src = "/lg.gif?type=" + type + "&id=" + id;
        i.onload = function () {
            // Do nothing
        };
    }

    return {
        update: updateView
    }
})();

// load more video - trieuNT
var loadMoreVideoAjax = (function () {
    function loadMoreData(element, sourceEl) {
        $(element).click(function () {
            var tokenData = $(element).attr('data-token');
            var urlSendData = $(element).attr('data-url');
            if (tokenData && urlSendData) {
                $.ajax({
                    url: urlSendData,
                    type: "GET",
                    data: {token: tokenData},
                    beforeSend: function () {
                        $(element).hide();
                        $('#imgMoreLoading').removeClass('hidden');
                    },
                    success: function (data) {
                        if (data.success) {
                            $(element).attr('data-token', data.loadMoreToken);
                            $(sourceEl).append(data.html);
                            if (data.loadMoreToken === null) {
                                $(element).hide();
                            } else {
                                $(element).show();
                            }
                        }
                        $('#imgMoreLoading').addClass('hidden');
                        return false;
                    }
                });
            }
        });
    }

    return {
        loadMore: loadMoreData
    }
})();

// search
var initSearch = function () {
    var searchForm = $('.js-search-form'), searchInput, searchSubmit;
    if (searchForm.length) {
        searchInput = searchForm.find('.js-search-text');
        searchSubmit = searchForm.find('.js-search-submit').on('click', function (e) {
            e.preventDefault();
            var query = searchInput.val();
            query = query.trim();
            if (!query || query.length > 100) {
                return false;
            }
            query = query.replace(/\s{2,}/g, ' ');
            window.location = '/search?q=' + query;
        });

        searchInput.on('keyup', function (e) {
            if (e.keyCode === 13) {
                e.preventDefault();
                searchSubmit.click();
            }
        })
    }
};

var cssHeightNotFoundSearch = (function () {
    var height = document.documentElement.clientHeight;

    function initCss() {
        if (!$('#pnlStreamPosts').length) {
            $('#content-search').css('height', height);
        } else {
            $('#content-search').css('height', 'auto');
        }
    }

    return {
        cssSearch: initCss
    }
})();

// Trim words
(function ($) {
    $.fn.trimWords = function (options) {
        options = $.extend({}, $.fn.trimWords.defaultOptions, options);
        console.time("TrimWords");
        var $me = $(this).each(function () {
            var $item = $(this);
            var $child = $item.children(':first');
            var outerHeight = $item.height();
            while ($child.outerHeight() > outerHeight) {
                $child.text(function (index, text) {
                    return text.replace(/\W*\s(\S)*$/, options.ellipsis);
                });
            }
        });
        console.timeEnd("TrimWords");
        return $me;
    };
    $.fn.trimWords.defaultOptions = {
        ellipsis: '..'
    };
})(jQuery);

// Do trim words
$('.trim-words').trimWords();


// $(document).on('touchstart', function() {
//     documentClick = true;
// });
// $(document).on('touchmove', function() {
//     documentClick = false;
// });
// $(document).on('click touchend', function(event) {
//     if (event.type == "click") documentClick = true;
//     if (documentClick){
//         console.log('AA');
//     }
// });

$(document).ready(function () {
    $('#pnlStreamPosts').postStreamer();

    $('#btn-read-more').on('click', function () {
        $('.tag-description').hide();
        $('.tag-summary').show();
        $(this).hide();
    });
    // search
    initSearch();
    cssHeightNotFoundSearch.cssSearch();

    var btn = $('.scrollToTop');
    $(window).scroll(function () {
        if ($(window).scrollTop() > 300) {
            btn.fadeIn().addClass('show');
        } else {
            btn.fadeOut().removeClass('show');
        }
    });

    btn.on('click', function (e) {
        e.preventDefault();
        $('html, body').animate({scrollTop: 0}, '300');
    });

    var prevScrollpos = window.pageYOffset;
    window.onscroll = function () {
        var currentScrollPos = window.pageYOffset;
        if (prevScrollpos > currentScrollPos) {
            // document.getElementById("menu-header").style.top = "0";
            // document.getElementById("menu-header").style.position = "relative";
            $('#menu-header').css({top: 0, position: 'relative'});
        } else if (currentScrollPos > 200) {
            /*document.getElementById("socical-header").style.top = "-42px";
            document.getElementById("socical-header").style.position = "fixed";*/
            $('#socical-header').css({top: '-42px', position: 'fixed'});

            /*document.getElementById("menu-header").style.top = "-50px";
            document.getElementById("menu-header").style.position = "fixed";*/
            $('#menu-header').css({top: '-50px', position: 'fixed'});
        } else {
            /*document.getElementById("socical-header").style.top = "0";
            document.getElementById("socical-header").style.position = "relative";*/
            $('#socical-header').css({top: 0, position: 'relative'});

            /*document.getElementById("menu-header").style.top = "0";
            document.getElementById("menu-header").style.position = "relative";*/
        }

        if (currentScrollPos <= 200) {
            /*document.getElementById("socical-header").style.top = "0";
            document.getElementById("socical-header").style.position = "relative";*/
            $('#socical-header').css({top: 0, position: 'relative'});
        }
        prevScrollpos = currentScrollPos;
    }

    // Tracking view if page is detail
    if($("#postId").val()){
        countView.update(1, $("#postId").val());
    }
});