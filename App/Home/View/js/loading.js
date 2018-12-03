$(function() {
    //加载进度
    loadProgress();
});
//加载进度
function loadProgress() {
    $(".tle").hide();
    var guide = $(".guide");
    var imgList = $("img");
    var i = 0;
    var length = imgList.length;
    var per = 0;
    imgList.each(function() {
        var img = new Image();
        img.src = $(this).attr("src");
        img.onload = function() {
            i++;
            // console.log(i);
            per = parseInt(i / length * 100, 10);
            if (guide.hasClass("style_1")) {
                style_1(per); //风格1
            } else if (guide.hasClass("style_2")) {
                style_2(per); //风格2
            }
            if (per == 100) {
                // $("main").hide();
                // $(".music_btn,.next").show();
            }
        }
    });
}
/**
 *风格1，直线加载彩色进度条
 */
function style_1(per) {
    $(".progressCurr").css("width", per + "%");
    $(".progressText").html(per + "%");
}
/**
 *风格2，圆圈加载
 */
var svg;
var ctx;
var svgR;
var oldPer = 0;
var star_flag = true;
$(function() {
    var w = $(".progress").width();
    var h = $(".progress").height();
    $("#progress_circle").attr({
        "width": w,
        "height": h
    });
    svg = $("#progress_circle");
    svgR = parseInt(w / 2, 10) - 1;
    oldPer = 0;
    //style_2(100);
});

function style_2(per) {
    if (oldPer >= per) {
        return;
    }
    for (var i = oldPer; i <= per; i++) {
        var angle = i / 100 * 2 * Math.PI;
        var x = Math.cos(angle) * svgR + svgR + 1;
        var y = Math.sin(angle) * svgR + svgR + 1;
        if (star_flag) {
            svg.find("path").attr("d", "M" + x + " " + y);
            star_flag = false;
        } else {
            var d = svg.find("path").attr("d");
            d = d + " L" + x + " " + y;
            svg.find("path").attr("d", d);
        }
    }
    oldPer = per;
    $(".progress_num").html(per + "%");
}