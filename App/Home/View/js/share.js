// 微信分享js v3.0s 通用版
// ====配置内容====
//自动获取域名，用于wx.issmart.com.cn和poster.issmart.com.cn域名自动切换
var wx_host = window.location.origin;
// 调试模式true为开启,开启后微信浏览器打开alert：“ error：ok ”则表示分享功能成功。
var wx_debug = false;
//记录转发的标题，如果不填则默认获取页面title，填写以填写的值为准
var wx_mark = '';
//分享到朋友
var wx_app_title = '在路上，骑行正当时'; //分享标题
var wx_app_desc = '让本AI测一测，骑行时你在想什么'; //分享描述
var wx_app_link = window.location.href; //分享链接地址，绝对路径
var wx_app_img = wx_host + app_path + '/img/share.jpg'; //分享图片
// ===end===
//分享到朋友圈
var wx_circle_title = '在路上，骑行正当时'; //分享标题
var wx_circle_link = window.location.href; //分享链接地址，绝对路径
var wx_circle_img = wx_host + app_path + '/img/share.jpg'; //分享图片
// ===end===
var wx_share_start;
//===========以下内容勿碰===========
var signPackage = [];
// 获取配置信息链接
var url = wx_host + "/jssdk/signpackage.php";
$.ajax({
    url: url,
    data: {
        'requestUrl': location.href.split('#')[0],
        isajaxs: 1
    },
    async: false,
    dataType: 'json',
    success: function(data) {
        data = eval(data);
        if (data != null) {
            signPackage = data;
        }
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {
        console.log(XMLHttpRequest, textStatus, errorThrown);
    }
});
jQuery.getScript(window.location.protocol + "//res.wx.qq.com/open/js/jweixin-1.0.0.js", function(data, status, jqxhr) {
    wx.config({
        debug: wx_debug, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
        appId: signPackage.appId, // 必填，公众号的唯一标识
        timestamp: signPackage.timestamp, // 必填，生成签名的时间戳
        nonceStr: signPackage.nonceStr, // 必填，生成签名的随机串
        signature: signPackage.signature, // 必填，签名，见附录1
        jsApiList: ['onMenuShareTimeline', 'onMenuShareAppMessage', 'scanQRCode'] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
    });
    wx.ready(function() {
        wx_share_start = function() {
            //分享给朋友
            wx.onMenuShareAppMessage({
                title: wx_app_title, //大标题
                desc: wx_app_desc, //小标题
                link: wx_app_link, //分享链接
                imgUrl: wx_app_img, //分享图片
                trigger: function(res) {
                    // 不要尝试在trigger中使用ajax异步请求修改本次分享的内容，因为客户端分享操作是一个同步操作，这时候使用ajax的回包会还没有返回
                    //alert('用户点击发送给朋友');
                },
                success: function(res) { //-start-
                    // alert('已分享');
                    _maq.push(['share', _from, "_openId", '朋友', _userInfo]); //分享给朋友
                    // shareInter();
                }, //-end-
                cancel: function(res) {
                    // alert('已取消');
                },
                fail: function(res) {
                    //alert(JSON.stringify(res));
                }
            });
            //分享到朋友圈
            wx.onMenuShareTimeline({
                title: wx_circle_title, //大标题
                link: wx_circle_link, //分享链接
                imgUrl: wx_circle_img, //分享图片
                trigger: function(res) {
                    // 不要尝试在trigger中使用ajax异步请求修改本次分享的内容，因为客户端分享操作是一个同步操作，这时候使用ajax的回包会还没有返回
                    //alert('用户点击分享到朋友圈');
                },
                success: function(res) { //-start-
                    // shareInter();
                    _maq.push(['share', _from, "_openId", '朋友圈', _userInfo]); //分享给朋友圈
                }, //-end-
                cancel: function(res) {
                    //alert('已取消');
                },
                fail: function(res) {
                    //alert(JSON.stringify(res));
                }
            });
            //alert('已注册获取“分享到朋友圈”状态事件');
        }
        wx_share_start();
    });
});
//===========END===========