/** @param {jQuery} $ jQuery Object */
!function($, window, document)
{
    function initLoadMore() {
        var $loadMore = $('.load-more');
        if($loadMore.length){
            var onLoading = false, targetUrl, token,
                $targetList, $processingBlock, currentLoad = 0, disabled = false;
            targetUrl = $loadMore.attr('data-target-url');
            if(!targetUrl){
                return false;
            }
            $targetList = $loadMore.find('.load-more__target');
            $processingBlock = $loadMore.find('.load-more__processing-block');
            var triggerLoadmore = function(isAutoLoad) {
                if(onLoading || disabled){
                    return;
                }
                if(isAutoLoad){
                    currentLoad++;
                }
                onLoading = true;
                $loadMore.addClass('loading');

                token = $loadMore.attr('data-token');
                if(!token){
                    return false;
                }
                $.ajax({
                    method: "GET",
                    url: targetUrl,
                    data: {
                        token: token,
                        is_auto_load: isAutoLoad
                    }
                }).done(function(data) {
                    onLoading = false;
                    if(data.html){
                        var newContent = $(data.html);
                        $targetList.children().last().removeClass('last');
                        $targetList.append(newContent);
                        $targetList.children().last().addClass('last');
                        $targetList.trigger('loadmore:complete');
                        //$('.timeago').timeago();
                    }
                    if(data.loadMoreToken){
                        $loadMore.attr('data-token', data.loadMoreToken);
                    }else{
                        disabled = true;
                        $processingBlock.css('display','none');
                    }
                    $loadMore.removeClass('loading');
                });
            };

            $loadMore.on('checkLoadMore', function(e, position) {
                var wayPoint = 0;
                if($targetList.children().last().length){
                    var lastChild = $targetList.children().last();
                    if(lastChild.is('ul') && lastChild.children().last().length){
                        lastChild = lastChild.children().last();
                    }
                    wayPoint = lastChild.offset().top;
                }else{
                    wayPoint = $targetList.offset().top;
                }

                if((position+$(window).height()) > wayPoint){
                    triggerLoadmore(true);
                }
            });
        }
    }

    $(window).scroll(function(){
        $('.load-more').trigger('checkLoadMore', [$(window).scrollTop()]);
    });

    initLoadMore();
    $(document).ready(function() {
        $('.load-more').trigger('checkLoadMore', [$(window).scrollTop()]);
    });
}
(jQuery, window, document);