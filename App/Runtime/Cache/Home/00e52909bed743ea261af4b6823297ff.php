<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <title>华为云智能识别demo</title>
    <style>

    </style>
    <script>
        (function (doc, win) {
            var docEl = doc.documentElement,
                    resizeEvt = 'orientationchange' in window ? 'orientationchange' : 'resize',
                    recalc = function () {
                        var clientWidth = docEl.clientWidth;
                        if (!clientWidth) return;
                        if (clientWidth >= 640) {
                            docEl.style.fontSize = '100x';
                        } else {
                            docEl.style.fontSize = 100 * (clientWidth / 640) + 'px';
                        }
                    };

            if (!doc.addEventListener) return;
            win.addEventListener(resizeEvt, recalc, false);
            doc.addEventListener('DOMContentLoaded', recalc, false);
        })(document, window);
        (function(){
            if (typeof(WeixinJSBridge) == "undefined") {
                document.addEventListener("WeixinJSBridgeReady", function (e) {
                    setTimeout(function(){
                        WeixinJSBridge.invoke('setFontSizeCallback',{"fontSize":0}, function(res) {
                            // alert(JSON.stringify(res));
                        });
                    },0);
                });
            } else {
                setTimeout(function(){
                    WeixinJSBridge.invoke('setFontSizeCallback',{"fontSize":0}, function(res) {
                        // alert(JSON.stringify(res));
                    });
                },0);
            }
        })();
    </script>
    <link rel="stylesheet" href="/tp/App/Home/View/css/loading.css"/>
    <link rel="stylesheet" href="/tp/App/Home/View/css/index.css"/>
    <link rel="stylesheet" href="/tp/App/Home/View/css/animate.css"/>
    <script src="/tp/App/Home/View/js/jquery-2.1.1.min.js"></script>
    <script src="/tp/App/Home/View/js/index.js"></script>
    <script src="/tp/App/Home/View/js/base64image.js" type="text/javascript"></script>
    <script src="/tp/App/Home/View/js/exif.js" type="text/javascript"></script>
</head>
<body>
    <main class="loaded">
        <div class="loaders">
            <div class="loader">
                <div class="loader-inner ball-spin-fade-loader">
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                </div>
            </div>
        </div>
    </main>
    <div id="content">
        <img class="music_btn play_music" src="/tp/App/Home/View/img/music_btn.png"/>
        <img class="next bott" src="/tp/App/Home/View/img/arrow_v.png" alt=""/>
        <img class="logo" src="/tp/App/Home/View/img/logo.png" alt="">
        <ul id="box">
            <li id="index1" class="index_show">
                <img class="n1" src="/tp/App/Home/View/img/p1-1.png" alt="">
                <h2 class="title">文字识别</h2>
                <p><input type="file" class="image" id="image1" value="上传"  onchange="fileUp1(this)" accept="image/*" capture="camera"></p>
                <p><button id="bt1"  class="blue big button"><span>识别文字</span></button></p>
                <div class="headline" id="show1" name="headline" style="width: 80%;height: 180px"></div>
            </li>
            <li id="index2" class="index_show">
                <img class="n1" src="/tp/App/Home/View/img/p1-1.png" alt="">
                <h2 class="title">语音合成</h2>
                <p style="margin-top: 30px;">
                   <textarea class="headline" id="contents" name="headline" placeholder="输入文字"></textarea>
                </p>
                 <p><button id="bt2"  class="blue big button"><span>合成语音</span></button></p>
            </li>
            <li id="index3" class="index_show">
                <img class="n1" src="/tp/App/Home/View/img/p1-1.png" alt="">
                <h2 class="title">图片标签识别</h2>
                <p><input type="file" class="image" id="image2" value="上传"  onchange="fileUp2(this)" accept="image/*" capture="camera"></p>
                <p><button id="bt3"  class="blue big button"><span>识别文字</span></button></p>
                <div class="headline" id="show2"  name="headline"></div>
            </li>
            <li id="index4" class="index_show">
                <img class="n1" src="/tp/App/Home/View/img/p1-1.png" alt="">
                <h2 class="title">图片违规内容检测</h2>
                <p><input type="file" class="image" id="image3" value="上传"  onchange="fileUp3(this)"></p>
                <p><button id="bt4"  class="blue big button"><span>检测图片</span></button></p>
                <div class="headline" id="show3" name="headline"></div>
            </li>
            <li id="index5" class="index_show">
                 <img class="n1" src="/tp/App/Home/View/img/p1-1.png" alt="">
                <h2 class="title">语音识别</h2>
                <p><input type="file" class="image" id="mp3" value="上传"></p>
                <p><button id="bt5"  class="blue big button"><span>识别语音</span></button></p>
                <div class="headline" id="show4"  name="headline"></div>
            </li>
            <li id="index6" class="index_show">
                <img class="n1" src="/tp/App/Home/View/img/p1-1.png" alt="">
                <h2 class="title">违规文本检测</h2>
                <p style="margin-top: 30px;">
                   <textarea class="headline" id="checkText" class="headline" name="headline" placeholder="输入文字"></textarea>
                </p>
                 <p><button id="bt6"  class="blue big button"><span>检测文本</span></button></p>
                 <div class="headl" id="show5"  name="headline"></div>
            </li>
            <li id="index7" class="index_show">
                <img class="n1" src="/tp/App/Home/View/img/p1-1.png" alt="">
                <h2 class="title">身份证信息识别</h2>
                <p><input type="file" class="image" id="image4" value="上传" accept="image/*" capture="camera" onchange="fileUp4(this)"></p>
                <p><button id="bt7"  class="blue big button"><span>识别证件</span></button></p>
                <div class="headline" id="show6" name="headline" style="width: 80%;height: 180px"></div>
            </li>
            <li id="index8"  class="index_show">>
                <img class="n1" src="/tp/App/Home/View/img/p1-1.png" alt="">
                <h2 class="title">华为云AI接口费用列表</h2>
                  <table border="1">
                         <tr>
                            <td width="50%">名称</td> <td width="50%">价格</td>
                         </tr>
                         <tr>
                            <td>文字识别API(包含证件识别,通用文字识别)</td> 
                            <td>
                                <p class="conut">(0~1千]</span><span class="prize"> 0元</p>
                                <p class="conut">(1千~1万]</span><span class="prize"> 0.2元/次</p>
                                <p class="conut">(1万~10万]</span><span class="prize">0.12元/次</p>
                                <p class="conut">(10万~500万]</span><span class="prize"> 0.08元/次</p>
                                <p class="conut">500万以上 </span><span class="prize"> 请联系我们</p>          
                            </td>
                         </tr>
                         <tr>
                           <td>图片标签识别API</td> 
                           <td> 
                              <p class="conut">(0~1千]</span><span class="prize"> 0元</p>
                              <p class="conut">1千以上</span><span class="prize"> 3.5元/千次</p>
                            </td>
                         </tr>
                         <tr>
                           <td>图片/文本内容检测API</td> 
                           <td><p class="conut">(0~5万]</span><span class="prize"> 0元</p>
                                <p class="conut">(5万~100万]</span><span class="prize"> 1.5元/千次</p>
                                <p class="conut">(100万~500万]</span><span class="prize">1.4元/千次</p>
                                <p class="conut">(500万~1000万]</span><span class="prize"> 1.2元/千次</p>
                                <p class="conut">1000万以上 </span><span class="prize"> 1.0元/千次</p> 
                           </td>
                         </tr>
                          <tr>
                             <td>语音合成API</td> 
                             <td>公测中暂时只能试用</td>
                         </tr>
                         <tr>
                           <td>语音识别API</td> <td>公测中暂时只能试用</td>
                         </tr>

                  </table>
                <div class="btn"></div>
            </li>
        </ul>
    </div>
    <style type="text/css">
             table{
                    width: 90%;
                    height: 50%;
                    margin: 0 auto;
                    border: 1px solid #fff;

             }
             td{
                color: #fff;
                font-size: 12px;
             }
             .count{
                margin: 1px;

             }
             .prize{
                color: #f00;
                margin: 1px;
             }
    </style>
    <!-- <audio  id="music" autoplay="autoplay"  preload="auto" src="/tp/App/Home/View/music/music.mp3" loop="loop"></audio> -->
    <!-- <script src="/tp/App/Home/View/js/audio.js"></script> -->
    <script src="/tp/App/Home/View/js/loading.js"></script>

    <script type="text/javascript">
    var baseimg1,baseimg2,baseimg3,baseimg4,baseimg5;
    var base_audio="";


/*文字识别start*/
    // $("#image1").change(function(){
    //    $('#show1').html("");
    //   var file = this.files[0];
    //     //判断类型是不是图片  
    //     if(!/image\/\w+/.test(file.type)){     
    //             alert("请确保文件为图像类型");   
    //             return false;   
    //     }   
    //     var reader = new FileReader();   
    //     reader.readAsDataURL(file);   
    //     reader.onload = function(e){
    //       image_base64=this.result.split(",")[1];
    //        //就是base64 
    //       baseimg1= image_base64;
         
    //     }
    // });
    var fileUp1 = function (me) {
          base64Image({
              width: 750,                             /*【选填】宽度默认750，如果图片尺寸大于该宽度，图片将被设置为该宽度*/
              ratio: 0.75,                            /*【选填】压缩率默认0.75 */
              file: me,                               /*【必填】对应的上传元素 */
              callback: function (imageUrl){         /*【必填】处理成功后的回调函数 */
                  /*imageUrl为得到的图片base64数据，这里可以进行上传到服务器或者其他逻辑操作 */
                    baseimg1 = imageUrl.replace(/^(data:\s*image\/(\w+);base64,)/g,'' );
                    alert('图片选择成功')
              },
          });
      };
    $('#bt1').click(function(){
             if(!baseimg1){
                 alert('请先上传图片');
                 return;
              }
            $.ajax({
                url: '<?php echo U('Ocr');?>',
                data:{'image':baseimg1},
                type:'post',
                dataType:'json',
                success: function (res) {
                    console.log(res);
                    $('#show1').html(res.data);
                }
            
          });
    })
/*end*/

/*语音合成start*/
       $('#bt2').click(function(){
           var content = $('#contents').val();
             if(!content){
                 alert('请输入文字');
                 return;
              }
            $.ajax({
                url: '<?php echo U('Tts');?>',
                data:{'content':content},
                type:'post',
                dataType:'json',
                success: function (res) {
                     var snd = Sound("data:audio/wav;base64," + res.data);
                }
            
          });
    })

/*语音合成end*/

/*图片标签识别start*/

  // $("#image2").change(function(){
  //        $('#show2').html("");
  //     var file = this.files[0];
  //       //判断类型是不是图片  
  //       if(!/image\/\w+/.test(file.type)){     
  //               alert("请确保文件为图像类型");   
  //               return false;   
  //       }   
  //       var reader = new FileReader();   
  //       reader.readAsDataURL(file);   
  //       reader.onload = function(e){
  //         image_base64=this.result.split(",")[1];
  //          //就是base64 
  //         baseimg2 = image_base64;   
         
  //       }
  //   });
   var fileUp2 = function (me) {
          base64Image({
              width: 750,                             /*【选填】宽度默认750，如果图片尺寸大于该宽度，图片将被设置为该宽度*/
              ratio: 0.75,                            /*【选填】压缩率默认0.75 */
              file: me,                               /*【必填】对应的上传元素 */
              callback: function (imageUrl){         /*【必填】处理成功后的回调函数 */
                  /*imageUrl为得到的图片base64数据，这里可以进行上传到服务器或者其他逻辑操作 */
                    baseimg2 = imageUrl.replace(/^(data:\s*image\/(\w+);base64,)/g,'' );
              },
          });
      };

   $('#bt3').click(function(){
             if(!baseimg2){
                 alert('请先上传图片');
                 return;
              }
            $.ajax({
                url: '<?php echo U('checkLabelImage');?>',
                data:{'image':baseimg2},
                type:'post',
                dataType:'json',
                success: function (res) {
                    console.log(res);
                    $('#show2').html(res.data);
                }
            
          });
    })
/*图片标签识别end*/


/*图片内容检测start*/
    // $("#image3").change(function(){
    //      $('#show3').html("");
    //     var file = this.files[0];
    //     //判断类型是不是图片  
    //     if(!/image\/\w+/.test(file.type)){     
    //             alert("请确保文件为图像类型");   
    //             return false;   
    //     }   
    //     var reader = new FileReader();   
    //     reader.readAsDataURL(file);   
    //     reader.onload = function(e){
    //     image_base64=this.result.split(",")[1];
    //        //就是base64 
    //       baseimg3 = image_base64;   
         
    //     }
    // });
    var fileUp3 = function (me) {
          base64Image({
              width: 750,                             /*【选填】宽度默认750，如果图片尺寸大于该宽度，图片将被设置为该宽度*/
              ratio: 0.75,                            /*【选填】压缩率默认0.75 */
              file: me,                               /*【必填】对应的上传元素 */
              callback: function (imageUrl){         /*【必填】处理成功后的回调函数 */
                  /*imageUrl为得到的图片base64数据，这里可以进行上传到服务器或者其他逻辑操作 */
                    baseimg3 = imageUrl.replace(/^(data:\s*image\/(\w+);base64,)/g,'' );
              },
          });
      };
   $('#bt4').click(function(){
             if(!baseimg3){
                 alert('请先上传图片');
                 return;
              }
            $.ajax({
                url: '<?php echo U('checkTypeImage');?>',
                data:{'image':baseimg3},
                type:'post',
                dataType:'json',
                success: function (res) {
                    console.log(res);
                    $('#show3').html(res.data);
                }
            
          });
    })
/*图片内容检测end*/

/*语音识别start*/
    $("#mp3").change(function(){
       $('#show4').html("");
      var file = this.files[0];

        //判断类型是不是音频  
        if(!/audio\/\w+/.test(file.type)){     
                alert("请确保文件为音频类型");   
                return false;   
        }   
           var reader = new FileReader();   
           reader.readAsDataURL(file);   
          reader.onload = function(e){
          audio_base64=this.result.split(",")[1];
          base_audio = audio_base64;   
        }
    });
  $('#bt5').click(function(){
             if(!base_audio){
                 alert('请先上传音频');
                 return;
              }
            $.ajax({
                url: '<?php echo U('Asr');?>',
                data:{'base_audio':base_audio},
                type:'post',
                dataType:'json',
                success: function (res) {
                   // alert(res.data);
                    $('#show4').html(res.data);
                }
            
          });
    });
  /*语音识别end*/

/*文本检测start*/
  $('#bt6').click(function(){
           var content = $('#checkText').val();
             if(!content){
                 alert('请输入文字');
                 return;
              }
            $.ajax({
                url: '<?php echo U('moderation_text');?>',
                data:{'content':content},
                type:'post',
                dataType:'json',
                success: function (res) {
                      $('#show5').html(res.data);
                }
            
          });
    })
  /*文本检测end*/

  /*身份证识别start*/
  // $("#image4").change(function(){
  //        $('#show6').html("");
  //       var file = this.files[0];
  //       //判断类型是不是图片  
  //       if(!/image\/\w+/.test(file.type)){     
  //               alert("请确保文件为图像类型");   
  //               return false;   
  //       }   
  //       var reader = new FileReader();   
  //       reader.readAsDataURL(file);   
  //       reader.onload = function(e){
  //       image_base64=this.result.split(",")[1];
  //          //就是base64 
  //         baseimg4 = image_base64;   
         
  //       }
  //   });
  var fileUp4 = function (me) {
          base64Image({
              width: 750,                             /*【选填】宽度默认750，如果图片尺寸大于该宽度，图片将被设置为该宽度*/
              ratio: 0.75,                            /*【选填】压缩率默认0.75 */
              file: me,                               /*【必填】对应的上传元素 */
              callback: function (imageUrl){         /*【必填】处理成功后的回调函数 */
                  /*imageUrl为得到的图片base64数据，这里可以进行上传到服务器或者其他逻辑操作 */
                    baseimg4 = imageUrl.replace(/^(data:\s*image\/(\w+);base64,)/g,'' );
              },
          });
      };
   $('#bt7').click(function(){
             if(!baseimg4){
                 alert('请先上传图片');
                 return;
              }
            $.ajax({
                url: '<?php echo U('Ocr_Idcard');?>',
                data:{'image':baseimg4},
                type:'post',
                dataType:'json',
                success: function (res) {
                    console.log(res);
                    $('#show6').html(res.data);
                }
            
          });
    })
   /*身份证识别end*/



$(document).ready(function(){
    $('body').prepend('<div id="PHP_fullMask" style="display: none;width:100%;height:100%;background-color:#FBFBFB;opacity:0.8;-webkit-opacity:0.8;z-index:9999;position:fixed;top:0;left:0;"><div style=" text-align: center ;display: block;margin: 0 auto;top: 23%;position:  relative;"> <img src="/tp/Public/load2.gif" ></div></div>')

}).ajaxStart(function(){

    $("#PHP_fullMask").css({
        "display":"block",
        "top":$(document).scrollTop(),
        "lift":$(document).scrollLeft(),
    });
    $("body").data("style", {
        'oldheight': $("body").css('height'),
        'oldwidth': $("body").css('width'),
        'oldscrollTop': $(document).scrollTop(),
        'oldscrollLeft': $(document).scrollLeft()
    }).css({
        "height":$(window).height(),
        "width":$(window).width(),
        "overflow":"hidden",
        "display":"block"
    });
}).ajaxComplete(function(){
    $("#PHP_fullMask").css({"display":"none"});
    $("body").css({
        "height":$('body').data('style').oldheight,
        "width":$('body').data('style').oldwidth,
        "overflow":"auto"
    });
});
var Sound = (function () {
      var df = document.createDocumentFragment();
      return function Sound(src) {
          var snd = new Audio(src);
          df.appendChild(snd); // keep in fragment until finished playing
          snd.addEventListener('ended', function () {df.removeChild(snd);});
          snd.play();
          return snd;
      }
}());


</script>
</body>
</html>