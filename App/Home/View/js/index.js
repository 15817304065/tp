window.onload = function() {
    var winH = window.innerHeight;
    var num = winH;
    var index = 1;
    var size = $("#box>li").size();
    $("main").hide();
    $(".music_btn,.next").show();
    animation()
    //下滑
    function bottom() {
        index--;
        if (index < 1) {
            index = 1;
            return false;
        }
        console.log(index);
        animation();
        $("#box").css({
            "top": -num
        });
        var last = $("#box li").last();
        $("#box li").first().before(last);
        $("#box").stop(false, false).animate({
            "top": 0
        }, 500);
    };
    //上滑
    function top() {
        index++;
        if (index > size) {
            index = size;
            return false;
        }
        console.log(index);
        animation();
        $("#box").stop(false, false).animate({
            "top": -num
        }, 500);
        setTimeout(function() {
            var first = $("#box li").first();
            $("#box").append(first);
            $('#box').css({
                "top": 0
            });
        }, 600);
    };
    var box = document.querySelector('body');
    var a = 0;
    var b = 0;
    var startY;
    var endY;
    var isstart = true;
    //触摸开始
    box.addEventListener("touchstart", function(e) {
        var touch = e.touches[0];
        startY = touch.pageY;
        b = 1;
    })
    //触摸过程
    box.addEventListener("touchmove", function(e) {
        e.preventDefault();
        if (isstart == true) {
            if (b == 1) {
                var touch = e.touches[0];
                endY = touch.pageY;
                if ((startY - endY) > 80) {
                    a = 1;
                } else if ((startY - endY) < -80) {
                    a = -1;
                }
            }
        }
    })
    //触摸结束
    box.addEventListener("touchend", function(e) {
        if (a == 1) {
            isstart = false;
            top();
            a = 0;
            setTimeout(function() {
                isstart = true;
            }, 1000)
        } else if (a == -1) {
            isstart = false;
            bottom();
            a = 0;
            setTimeout(function() {
                isstart = true;
            }, 1000)
        } else if (a == 0) {
            return true;
        }
    });
    $('.again').on('click', function() {
        console.log(index);
        index = 1
        animation();
        $("#box").stop(false, false).animate({
            "top": -num
        }, 500);
        setTimeout(function() {
            var first = $("#box li").first();
            $("#box").append(first);
            $('#box').css({
                "top": 0
            });
        }, 600);
    });
    $('.shareBtn').on('click', function() {
        $('.shareImg').show();
        isstart = false;
    });
    $('.shareImg').on('click', function() {
        $(this).hide();
        isstart = true;
    });
    //动效
    function animation() {
        if (index >= size) {
            $(".next").addClass("ttop");
            $(".next").removeClass("bott");
        } else {
            $(".next").addClass("bott");
            $(".next").removeClass("ttop");
        }
        //动画列表
        if (index == 1) {
            $("#index1 .n1").addClass("animate1");
            $("#index1 .n2").addClass("animate2");
            $("#index1 .n3").addClass("animate3");
            $("#index1 .n4").addClass("animate4");
            $("#index1 .n5").addClass("animate5");
            $("#index1 .n6").addClass("animate6");
            $("#index1 .n7").addClass("animate7");
            $("#index1 .n8").addClass("animate8");
        } else {
            $("#index1 .n1").removeClass("animate1");
            $("#index1 .n2").removeClass("animate2");
            $("#index1 .n3").removeClass("animate3");
            $("#index1 .n4").removeClass("animate4");
            $("#index1 .n5").removeClass("animate5");
            $("#index1 .n6").removeClass("animate6");
            $("#index1 .n7").removeClass("animate7");
            $("#index1 .n8").removeClass("animate8");
        }
        if (index == 2) {
            $("#index2 .n1").addClass("animate1");
            $("#index2 .n2").addClass("animate2");
            $("#index2 .n3").addClass("animate3");
            $("#index2 .n4").addClass("animate4");
        } else {
            $("#index2 .n1").removeClass("animate1");
            $("#index2 .n2").removeClass("animate2");
            $("#index2 .n3").removeClass("animate3");
            $("#index2 .n4").removeClass("animate4");
        }
        if (index == 3) {
            $("#index3 .n1").addClass("animate1");
            $("#index3 .n2").addClass("animate2");
            $("#index3 .n3").addClass("animate3");
            $("#index3 .n4").addClass("animate4");
            $("#index3 .n5").addClass("animate5");
            $("#index3 .n6").addClass("animate6");
        } else {
            $("#index3 .n1").removeClass("animate1");
            $("#index3 .n2").removeClass("animate2");
            $("#index3 .n3").removeClass("animate3");
            $("#index3 .n4").removeClass("animate4");
            $("#index3 .n5").removeClass("animate5");
            $("#index3 .n6").removeClass("animate6");
        }
        if (index == 4) {
            $("#index4 .n1").addClass("animate1");
        } else {
            $("#index4 .n1").removeClass("animate1");
        }
    }
};