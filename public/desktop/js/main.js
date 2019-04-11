// Call to all jquery ago
$.timeago.settings.cutoff = 86400000;
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
        if (!isLoading && $(window).scrollTop() > homeStreamPanel.find('figure:last').offset().top - $(window).height()) {
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
            if (!query) {
                return false;
            }
            query = query.replace(/\s{2,}/g, ' ');
            if (query.length > 100) {
                return false;
            }
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

$(document).ready(function () {
    $('#pnlStreamPosts').postStreamer();

    $('#btn-read-more').on('click', function () {
        $('.tag-description').hide();
        $('.tag-summary').show();
        $(this).hide();
    });
    // search
    initSearch();
    // load ajax video
    loadMoreVideoAjax.loadMore('#btnLoadMoreVideo', '#dataListVideos');

    var btn = $('.scrollToTop');
    $(window).scroll(function() {
        if ($(window).scrollTop() > 300) {
            btn.fadeIn().addClass('show');
        } else {
            btn.fadeOut().removeClass('show');
        }
    });

    btn.on('click', function(e) {
        e.preventDefault();
        $('html, body').animate({scrollTop:0}, '300');
    });

    // Tracking view if page is detail
    if($("#postId").val()){
        countView.update(1, $("#postId").val());
    }
});