$("#again").click(function () { //再玩一遍
    $("#content").show();
    $("#conWrap").hide();
    $(".content2").hide();
    $(this).hide();
});

$("#link").click(function () { //外链
    window.location.href = 'https://activity.huaweicloud.com/cn_offline/index.html';
});

$('.skin_btn').click(function () { //再玩一遍
    $(".homeTitle").show();
    $("#box").hide();
    $(".skin_btn").hide();
});

window.onload = function () {
    $("#load1").hide();
    $(".music_btn,.next,.skin_btn").show();
    if (window.innerWidth > 320) { //适配图片裁剪框   #picture缩放1.25倍
        $("#picture").css({
            "transform": "scale(1.25)",
            "-webkit-transform": "scale(1.25)",
            "transform-origin": "50% 0%",
            "-webkit-transform-origin": "50% 0%",
        });
    }
    // 容器1200px，缩小0.25倍
    // 属性配置
    var options = {
        containerId: "#pictureUpload",
        uploadBgId: "#pictureUpload-bg",
        fileId: ".fileElem",
        cameraInput: "#camera",
        albumInput: "#album",
        canvasId: "#canvas",
        // 底图尺寸
        container: {
            width: 750,
            height: 1206
        },
        // 截取框尺寸
        clip: {
            width: 750,
            height: 1206
        },
        imgQuality: 1
    }
    var txUpload = avatarUpload(options);
    $(".selectCamera").click(txUpload.cameraImg);
    $(".selectAlbum").click(txUpload.albumImg);
    // $("#preview").click(txUpload.createImg);
    $("#confirm").click(function () {
        $("#conWrap").hide();
        $("#loaded").show();
        txUpload.submit();
    })
    //文件改变后触发，即有照片上传时触发
    $(".fileElem").on("change", function () {
        txUpload.handleFiles(function () {
            $("#load2").show();
            $("#conWrap").show();
            $("#content").hide();
            $("#preview, #submit, #createLocalImg, #rotateLocalImg").addClass('active');
        })
    });

    setTimeout(function () {
        $("#index1 .n11").addClass("big_1");
        setTimeout(function () {
            $("#index1").css("display", "none");
            $("#index2").css("display", "block");
            setTimeout(function () {
                $("#index2 .n22").addClass("xuehua");
                $("#index2 .n11").addClass("big_1");
                setTimeout(function () {
                    $("#index2").css("display", "none");
                    $("#index3").css("display", "block");
                    setTimeout(function () {
                        $("#index3 .n11").addClass("big_1");
                        setTimeout(function () {
                            $("#index3").css("display", "none");
                            $("#index4").css("display", "block");
                            setTimeout(function () {
                                $("#index4 .n33").addClass("fadeoutBT");
                                $("#index4 .n22").addClass("xuehua");
                                $("#index4 .n11").addClass("big_1");
                                setTimeout(function () {
                                    $("#index4").css("display", "none");
                                    $("#index5").css("display", "block");
                                    setTimeout(function () {
                                        $("#index5 .n1").addClass("animate1");
                                        $("#index5 .n2").addClass("animate2");
                                        $("#index5 .n3").addClass("animate3");
                                        $("#index5 .n4").addClass("animate4");
                                        $("#index5 .n5").addClass("animate5");
                                        $("#index5 .n6").addClass("animate6");
                                        $("#index5 .n7").addClass("animate7");
                                        setTimeout(function () {
                                            $("#index5 .n11").addClass("big");
                                            setTimeout(function () {
                                                $("#index5").css("display", "none");
                                                $("#index6").css("display", "block");
                                                setTimeout(function () {
                                                    $("#index6 .n11").addClass("big");
                                                    setTimeout(function () {
                                                        $("#index6 .n1").addClass("animate1");
                                                        $("#index6 .n2").addClass("animate2");
                                                        $("#index6 .n3").addClass("animate3");
                                                        $("#index6 .n4").addClass("animate4");
                                                        $("#index6 .n5").addClass("animate5");
                                                        $("#index6 .n6").addClass("animate6");
                                                        $("#index6 .n7").addClass("animate7");
                                                        setTimeout(function () {
                                                            $("#index6").css("display", "none");
                                                            $(".homeTitle").css("display", "block");
                                                            $(".skin_btn").hide();
                                                        }, 1500)
                                                    }, 1000)
                                                }, 100)
                                            }, 1500)
                                        }, 1000)
                                    }, 100)
                                }, 1000)
                            }, 500)
                        }, 1000)
                    }, 500)
                }, 1000)
            }, 500)
        },1000)
    },500);
};

//华为云识别接口，识别图片获取关键词、描述
function ai_interface(img_data) {
    $("#load2").show();
    $('#codeImg').attr("src", img_data.base64_for_inter);
    $.ajax({
        url: aiurl,
        type: 'post',
        data: {
            'image': img_data.img_base64_min
        },
        dataType: 'json',
        async: true,
        success: function(data) {
            // console.log(data);
            if (data.status == "True") 
            {
                if(!data.result.key)
                {
                    alert(data.result.content);
                    $("#load2").hide();
                    $("#again").click();
                }
                else{
                    poster_page_show(img_data.img_base64, data.result.key, data.result.content);
                }
            } else {
                alert(data.data);
                $("#load2").hide();
                $("#again").click();
            }
        },
        error: function(e) {
            console.log(e);
            alert('error');
            $("#load2").hide();
            $("#again").click();
        }
    });
}

//图谱识别接口，鉴黄、鉴暴、鉴政治人物
function tp_interface(img_data) {
    // $("#load2").hide();return;
    image = img_data.img_base64_min;
    var alert_list = {
        "busy" : "哎呀，我忙不过来了，麻烦稍后再来..",
        "img_error" : "想用此图迷惑我？不可能的，换张图吧~",
        "img_format_error" : "正确的开始是第一步，请上传JPG或PNG格式的文件哦！",

    };

    if (location.host == 'localhost:8089') {
        var url = 'http://' + location.host + '/tupu_api/api.php';
    } else {
        var url = 'http://' + location.host + '/web/public/tupu_img_ai/api.php';
    }

    $.ajax({
        url: url,
        type: 'post',
        data: '{"image":"' + image + '"}',
        dataType: 'json',
        contentType: 'application/json',
        async: true,
        success: function(data) {
            // terror 恐暴 political 政治人物 porn 色情
            var alert_m = false;
            var message = alert_list.img_error

            console.log(data);
            if (data.status) {
                if (data.data.terror_type == true) {
                    alert_m = true;
                    console.log("暴恐元素");
                }
                if (data.data.porn_type == true) {
                    alert_m = true;
                    console.log("色情元素");
                }
                if (data.data.political_type == true) {
                    alert_m = true;
                    console.log("政治人物");
                }
                if (alert_m) {
                    alert(message);
                    $("#again").click();
                }
            } else {
                if (data.msg == "inter error") {
                    alert(alert_list.img_format_error);
                    $("#again").click();
                } else if (data.msg == "image format error") {
                    alert(alert_list.img_format_error+".");
                    $("#again").click();
                } else {
                    alert("api error");
                    $("#again").click();
                }
            }
            $("#load2").hide();
        },
        error: function(e) {
            console.log(e);
            alert(alert_list.busy);
            $("#load2").hide();
            $("#again").click();
        }
    });
}

//海报页生成、显示
function poster_page_show(image, key, content)
{
    $("#load2").hide();
    $("#title").html(key);
    $("#bliss").html(content);
    $('.phlistBox>.phlist').css({
        'background-image': 'url("' + image + '")',
        'background-size': 'cover',
    });
    $('.content2').show();
    _merge();
}

function _merge() {
    $(".er").css("right","0.2rem");
    $('.reText,.again,.play_music,.button').hide(); //===================让不想合成的元素先隐藏
    var main = $('body');//要生成的容器
    //要将 canvas 的宽高设置成容器宽高的 2 倍
    var canvas = document.createElement("canvas");
    canvas.width = 2 * window.innerWidth;
    canvas.height = 2 * window.innerHeight;
    canvas.style.width = 2 * window.innerWidth + "px";
    canvas.style.height = 2 * window.innerHeight + "px";
    var context = canvas.getContext("2d");
    //然后将画布缩放，将图像放大两倍画到画布上
    context.scale(2, 2);
    html2canvas(main, {
        canvas: canvas,
        onrendered: function (canvas) {
            $('.info').css("background", "url("+app_path+"img/rbg2.png) no-repeat");
            $('.info').css("background-size", "contain");
            // 合成后的回调
            var data = canvas.toDataURL("image/png");
            console.log(data)
            $('.img').attr('src', data);
            $('.img').show();
            $(".er").css("right","0.6rem");
            $('.reText,.again,.play_music,.button').show();
        }
    });

};
// ===========================================================================================

