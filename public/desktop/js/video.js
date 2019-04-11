var loadVideoPlay = (function () {
     var listOrder = 0;
     function initVideos(type, img, playerInstance, positonLoad) {
        playerInstance.setup({
            file: getUrlVideo(),
            image: (img) ? img : null,
            width: 770,
            height: 498,
            aspectratio: "16:9",
            autostart: (type) ? true : false,
            mute: false,
        })

        playerInstance.onPlay(function () {
            $('#box-video').removeClass('vd').addClass('ajax-video');
            $('#title-box-video').css({bottom: 0});
            $('#title-box-video').hide();
        });
        playerInstance.onPause(function () {
            $('#box-video').addClass('vd').addClass('ajax-video');
            $('#title-box-video').css({bottom: '40px'});
            $('#title-box-video').show();
        });
        // next load video
         autoLoadPlayVideo(playerInstance, listOrder, positonLoad);
    }

    function autoLoadPlayVideo(playerInstance, orders, positonLoad){
        playerInstance.onBeforeComplete(function () {
            if(positonLoad){
                orders = positonLoad;
            }
            orders++;
            let urlAutoList = getUrlVideoListAutoPlay(orders);
            let imgAutoList = getImageVideoListAutoPlay(orders);
            let titleAutoList = getTitleVideoListAutoPlay(orders);
            activeCurrentWatchVideo(orders);
            addTitleVieoCurrentWatch(titleAutoList[0], titleAutoList[1]);

            playerInstance.setup({
                file: urlAutoList,
                image: imgAutoList,
                width: 770,
                height: 498,
                aspectratio: "16:9",
                autostart: true,
                mute: false,
            });
            playerInstance.onPlay(function () {
                $('#box-video').removeClass('vd');
                $('#title-box-video').css({bottom: 0});
                $('#title-box-video').hide();
            });
            playerInstance.onPause(function () {
                $('#box-video').addClass('vd');
                $('#title-box-video').css({bottom: '40px'});
                $('#title-box-video').show();
            });
            if(urlAutoList && imgAutoList && orders < 16){
                autoLoadPlayVideo(playerInstance, orders);
            }
        });
    }
    
    function activeCurrentWatchVideo(id) {
         $('.iframetrackTitle').find('a').css({color:'#222'});
         if(id){
             $('#sourceTitle_'+id).find('a').css({color:'red'});
         }
    }

    function addTitleVieoCurrentWatch(title, titleHover) {
         $('#title-box-video').find('a').text(title);
         if(titleHover){
             $('#title-box-video').find('a').attr('title', titleHover);
         }
    }

    function getUrlVideoListAutoPlay(order) {
        var ulrPlayList = $('#source_'+order).attr('rel');
        return ulrPlayList;
    }
    function getImageVideoListAutoPlay(orders) {
        var imgPlayList = $('#source_'+orders).find('img').attr('src');
        return imgPlayList;
    }

    function getTitleVideoListAutoPlay(order) {
        var titleAutoPlayList = $('#sourceTitle_'+order).find('a.title-news').text().trim();
        var fullTitlePlayList = $('#sourceTitle_'+order).find('a.title-news').attr('title');
        return [titleAutoPlayList,fullTitlePlayList];
    }


    function getUrlVideo() {
        var url = $('#hddUrlVideos').val().trim();
        var pos = url.indexOf('?');
        var paramsVideos = (pos == -1) ? "?showinfo=0&iv_load_policy=3&modestbranding=1&nologo=1&autoplay=0&controls=0&showtitle=0&cc_load_policy=1&loop=1" : "&showinfo=0&iv_load_policy=3&modestbranding=1&nologo=1&autoplay=0&controls=0&showtitle=0&cc_load_policy=1&loop=1";
        var fullUrl = url + paramsVideos;
        return fullUrl;
    }
    function fixCssVideo() {
        $('#mainPlayVideo').css({/*float: 'left', */width: '100%', maxHeight: '498px'});
    }
    function changeVideoByClick(playerInstance) {
        $('.iframetrack').click(function () {
            var idTopVideo = null;
            if($(this)[0].hasAttribute('data-id')){
                idTopVideo = $(this).attr('data-id');
            }
            var urlDetailLink = $(this).attr('data-url');
            var ulrVideo = $(this).attr('rel');
            var imgaeVideo = $(this).find('img').attr('src');
            var distanceTop = $('section.menu').offset().top;
            var titleVideoCurrent = $(this).attr('data-title');
            $('#hddUrlVideos').val(ulrVideo);
            addTitleVieoCurrentWatch(titleVideoCurrent);
            initVideos(true, imgaeVideo, playerInstance, idTopVideo);
            fixCssVideo();
            activeCurrentWatchVideo(false);
            $('html,body').animate({scrollTop: distanceTop}, 'slow');
            window.history.pushState(null,null, urlDetailLink);
            return false;
        });

        $('.iframetrackTitle').click(function () {
            var idTopVideo = null;
            if($(this)[0].hasAttribute('data-id')){
                idTopVideo = $(this).attr('data-id');
            }

            var urlLinkDetail = $(this).find('a.title-news').attr('data-url');
            var ulrVideo = $(this).find('a.title-news').attr('rel');
            var imgVideo = $(this).find('a.title-news').attr('data-img');
            var distanceTop = $('section.menu').offset().top;
            var titleVideoCurrent = $(this).find('a.title-news').text();
            $('#hddUrlVideos').val(ulrVideo);
            addTitleVieoCurrentWatch(titleVideoCurrent);
            initVideos(true, imgVideo, playerInstance, idTopVideo);
            fixCssVideo();
            activeCurrentWatchVideo(false);
            console.log(urlLinkDetail);
            $('html,body').animate({scrollTop: distanceTop}, 'slow');
            window.history.pushState(null,null, urlLinkDetail);
            return false;
        });
    }
    return {
        loadVideo: initVideos,
        loadCssVideo: fixCssVideo,
        videoByClick: changeVideoByClick
    }
})();

$(document).ready(function() {
    // init js in here
    var imgAvatar;
    var firstVideoAvatar = document.getElementById('avatarUrlVideos');
    var playerInstance = jwplayer("mainPlayVideo");
    if (firstVideoAvatar) {
        imgAvatar = firstVideoAvatar.value;
        if(!$('#box-video').hasClass('ajax-video')){
            loadVideoPlay.loadVideo(false, imgAvatar, playerInstance, false);
        }
        loadVideoPlay.loadCssVideo();
        loadVideoPlay.videoByClick(playerInstance);
    }
});