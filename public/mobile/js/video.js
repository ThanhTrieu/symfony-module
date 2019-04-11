var loadVideoPlay = (function () {
    function initVideos(type, img, playerInstance) {
        playerInstance.setup({
            file: getUrlVideo(),
            image: img,
            width: "100%",
            height: "100%",
            aspectratio: "16:9",
            autostart: type,
            mute: false,
            provider: 'video',
            primary: 'flash'
        });
        // next load video
        //autoLoadPlayVideo(playerInstance, listOrder, positonLoad);
    }
    /*
    function autoLoadPlayVideo(playerInstance, orders, positonLoad){
        playerInstance.onBeforeComplete(function () {
            if(positonLoad){
                orders = positonLoad;
                orders++;
            } else {
                orders++;
            }
            let urlAutoLIst = getUrlVideoListAutoPlay(orders);
            let imgAutoList = getImageVideoListAutoPlay(orders);
            activeCurrentWatchVideo(orders);
            playerInstance.setup({
                file: urlAutoLIst,
                image: imgAutoList,
                width: 770,
                height: 498,
                aspectratio: "16:9",
                autostart: true,
                mute: false,
            });
            if(urlAutoLIst && imgAutoList && orders < 16){
                autoLoadPlayVideo(playerInstance, orders);
            }
        });
    }
    */

    /*
    function activeCurrentWatchVideo(id) {
        $('.iframetrackTitle').find('a').css({color:'#222'});
        if(id){
            $('#sourceTitle_'+id).find('a').css({color:'red'});
        }
    }
    */

    function getUrlVideoListAutoPlay(order) {
        var ulrPlayList = $('#source_'+order).attr('rel');
        return ulrPlayList;
    }
    function getImageVideoListAutoPlay(orders) {
        var imgPlayList = $('#source_'+orders).find('img').attr('src');
        return imgPlayList;
    }

    function getUrlVideo() {
        var url = $('#hddUrlVideos').val().trim();
        var domainOrigin = $('#domainOrigin').val().trim();
        var pos = url.indexOf('?');
        var paramsVideos = (pos == -1) ? "?showinfo=0&iv_load_policy=3&modestbranding=1&nologo=1&autoplay=0&controls=0&showtitle=0&cc_load_policy=1&loop=1&enablejsapi=1&origin="+domainOrigin : "&showinfo=0&iv_load_policy=3&modestbranding=1&nologo=1&autoplay=0&controls=0&showtitle=0&cc_load_policy=1&loop=1&enablejsapi=1&origin="+domainOrigin;
        var fullUrl = url + paramsVideos;
        console.log(fullUrl);
        return fullUrl;
    }
    function fixCssVideo() {
        $('#mainPlayVideo').css({float: 'left', width: '100%', height: '254px'});
    }
    function changeVideoByClick(playerInstance) {
        $('.iframetrack').click(function () {
            var urlDetailLink = $(this).attr('data-url');
            var ulrVideo = $(this).attr('rel');
            var imgaeVideo = $(this).find('img').attr('src');
            $('#hddUrlVideos').val(ulrVideo);
            initVideos(true, imgaeVideo, playerInstance);
            fixCssVideo();
            $('html,body').animate({scrollTop: 0}, 'slow');
            window.history.pushState(null,null, urlDetailLink);
            return false;
        });

        $('.iframetrackTitle').click(function () {
            var urlLinkDetail = $(this).find('a').attr('data-url');
            var ulrVideo = $(this).find('a').attr('rel');
            var imgVideo = $(this).find('a').attr('data-img');
            $('#hddUrlVideos').val(ulrVideo);
            initVideos(true, imgVideo, playerInstance);
            fixCssVideo();
            $('html,body').animate({scrollTop: 0}, 'slow');
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
    //var idVideo = document.getElementById('hddIdVideos').value;
    //var typeVideo = (idVideo) ? true : false;
    if(firstVideoAvatar) {
        var playerInstance = jwplayer("mainPlayVideo");
        imgAvatar = firstVideoAvatar.value;
        loadVideoPlay.loadVideo(false, imgAvatar, playerInstance);
        loadVideoPlay.loadCssVideo();
        loadVideoPlay.videoByClick(playerInstance);
    }
});