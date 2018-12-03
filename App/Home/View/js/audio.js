// 音乐播放
var play = $(".music_btn");
var startPlay = false;
autoPlayMusic();
function muscStop() {
    document.getElementById('music').pause();
}
function autoPlayMusic() {
    // 自动播放音乐效果，解决浏览器或者APP自动播放问题
    function musicInBrowserHandler() {
        musicPlay(true);
        document.body.removeEventListener('touchstart', musicInBrowserHandler);
    }
    document.body.addEventListener('touchstart', musicInBrowserHandler);

    // 自动播放音乐效果，解决微信自动播放问题
    function musicInWeixinHandler() {
        // alert('微信触发');
        musicPlay(true);
        document.addEventListener("WeixinJSBridgeReady", function () {
            musicPlay(true);
        }, false);
        document.removeEventListener('DOMContentLoaded', musicInWeixinHandler);
    }
    document.addEventListener('DOMContentLoaded', musicInWeixinHandler);
}

function musicPlay(isPlay) {
    var media = document.querySelector('#music');

    if (isPlay && media.paused) {
        media.play();
    }
    if (!isPlay && !media.paused) {
        media.pause();
    }

    play[0].addEventListener("touchstart", function (e) {
        if(startPlay == true){
            media.play();
            play.addClass("play_music");
            startPlay = false;
            console.log(startPlay)

        }else{
            media.pause();
            play.removeClass("play_music");
            startPlay = true;
            console.log(startPlay)

        }
        //console.log(startPlay)
    });

}
