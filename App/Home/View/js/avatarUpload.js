var avatarUpload = function(options) {
    //容器的尺寸
    var containerWidth = options.container.width;
    var containerHeight = options.container.height;
    //裁剪区域的尺寸
    var canvasW = options.clip.width;
    var canvasH = options.clip.height;
    var o_width, o_height;
    //控件让用户选择文件
    var $file = $(options.fileId);
    var $camera = $(options.cameraInput);
    var $album = $(options.albumInput);
    // 压缩后转码为base64图片
    var base64_for_inter;
    //用户上传的图片由URL.createObjectURL生成的URL指向
    var imgObjectURL = "";
    //canvas容器 用于:
    //1.生成用户裁剪的图片
    //2.生成二进制并上传
    var canvas = $(options.canvasId)[0];
    //图片操作相关的数据
    var imgData = {
        //图像的尺寸
        origin: {
            width: 0,
            height: 0
        },
        //缩放比例 默认为1
        scale: 1,
        //偏移量
        move: {
            x: 0,
            y: 0
        },
        //临时缩放比例
        tempScale: 0,
        //临时缩放偏移量,用于用户操作相关的计算
        tempMove: {
            x: 0,
            y: 0
        }
    };
    //照相获取图片
    function cameraImg() {
        $camera.click();
        $file = $camera;
    }
    //相册获取图片
    function albumImg() {
        $album.click();
        $file = $album;
    }
    //显示用户选择的图片 获取图片的原始尺寸
    function handleFiles(fn) {
        //如果没有图片就返回
        var files = $file[0].files;
        var src = selectFileImage($file);
        if (files.length === 0) {
            $('#load1').hide();
            return false;
        }
        if (Object.prototype.toString.call(fn) === "[object Function]") {
            // debugger;
            fn(src);
        }
        //取得file对象
        var file = files[0];
        //还原imgData相关的数据
        imgData = {
            //图像的尺寸
            origin: {
                width: 0,
                height: 0
            },
            //缩放比例 默认为1
            scale: 1,
            //偏移量
            move: {
                x: 0,
                y: 0
            },
            //临时缩放比例
            tempScale: 0,
            //临时缩放偏移量,用于用户操作相关的计算
            tempMove: {
                x: 0,
                y: 0
            },
            offsetMove: {
                x: 0,
                y: 0
            }
        };
        // debugger;
    }
    //生成图片
    function createImg(fn) {
        canvas.width = canvasW;
        canvas.height = canvasH;
        var context = canvas.getContext("2d");
        var img = new Image();
        img.src = imgObjectURL
        img.onload = function() {
            //在画布上放置图像的 x 坐标位置。
            var offsetx;
            //在画布上放置图像的 y 坐标位置。
            var offsety;
            //要使用的图像的宽度。（伸展或缩小图像）
            var biliW = containerWidth;
            //要使用的图像的高度。（伸展或缩小图像）
            var biliH = containerHeight;
            if (imgData.origin.width >= imgData.origin.height) {
                biliH = containerWidth / imgData.origin.width * imgData.origin.height
            } else if (imgData.origin.width < imgData.origin.height) {
                biliW = containerHeight / imgData.origin.height * imgData.origin.width
            }
            offsetx = (canvasW - biliW * imgData.scale) / 2 + imgData.offsetMove.x;
            offsety = (canvasH - biliH * imgData.scale) / 2 + imgData.offsetMove.y;
            context.drawImage(img, 0, 0, img.width, img.height, offsetx, offsety, biliW * imgData.scale, biliH * imgData.scale);
            if (Object.prototype.toString.call(fn) === "[object Function]") {
                fn();
            }
        }
    }
    //上传图片
    function submit(fn) {
        createImg(function() {
            var img_base64 = canvas.toDataURL("image/jpeg");
            inter_base64("hw", img_base64);
        });
    }
    //生成本地图片
    function createLocalImg(aIdName, aParentIdName, name) {
        createImg(function() {
            canvas.toBlob(function(blob) {
                var a;
                if (document.getElementById(aIdName)) {
                    a = document.getElementById(aIdName);
                } else {
                    var a = document.createElement("a");
                    a.id = aIdName;
                    document.getElementById(aParentIdName).appendChild(a);
                }
                var nBytes = blob.size;
                var size = nBytes + " bytes";
                for (var aMultiples = ["KiB", "MiB", "GiB", "TiB", "PiB", "EiB", "ZiB", "YiB"], nMultiple = 0, nApprox = nBytes / 1024; nApprox > 1; nApprox /= 1024, nMultiple++) {
                    size = nApprox.toFixed(3) + " " + aMultiples[nMultiple] + " (" + nBytes + " bytes)";
                }
                a.textContent = "Download(" + size + ")";
                a.href = window.URL.createObjectURL(blob);
                a.download = name + '.jpg';
            }, "image/jpeg", options.imgQuality); // JPEG at 100% quality
        })
    }
    // 此块也可以可以独立出来
    // 使用hammer.min.js对触摸滑动、缩放事件进行监听
    // 先要对监听的DOM进行一些初始化
    var mc = new Hammer($(options.containerId)[0]);
    var pan = new Hammer.Pan();
    var pinch = new Hammer.Pinch();
    var $uploadBg = $(options.uploadBgId);
    // add to the Manager
    mc.add([pan, pinch]);
    //旋转
    // mc.add(new Hammer.Rotate());
    // mc.on("rotate", function (ev) {
    //     // alert(11);
    // });
    //缩放
    mc.on("pinchstart", function(ev) {
        imgData.tempScale = ev.scale;
    });
    mc.on("pinchmove", function(ev) {
        imgData.scale = ev.scale - imgData.tempScale + imgData.scale;
        imgData.tempScale = ev.scale
        $uploadBg.css("transform", "scale(" + imgData.scale + "," + imgData.scale + ")")
    });
    //移动
    mc.on("panstart", function(ev) {
        // console.log(ev);
        var x = ev.center.x;
        var y = ev.center.y;
        imgData.tempMove.x = x;
        imgData.tempMove.y = y;
        // console.log('MM:'+x + ',' + y);
    });
    mc.on("panmove", function(ev) {
        var x = ev.center.x;
        var y = ev.center.y;
        var px = x - imgData.tempMove.x;
        var py = y - imgData.tempMove.y;
        imgData.move.x = imgData.move.x + px
        imgData.move.y = imgData.move.y + py
        imgData.tempMove.x = x;
        imgData.tempMove.y = y;
        imgData.offsetMove.x += px;
        imgData.offsetMove.y += py;
        // console.log(imgData.offsetMove.x + ',' + imgData.offsetMove.y);
        $uploadBg.css({
            left: imgData.move.x + "px",
            top: imgData.move.y + "px"
        })
    });

    function selectFileImage(fileObj) {
        var file = fileObj[0].files['0'];
        //图片方向角 added by lzk  
        var Orientation = null;
        if (file) {
            // console.log("正在上传,请稍后...");
            var rFilter = /^(image\/jpeg|image\/png)$/i; // 检查图片格式  
            if (!rFilter.test(file.type)) {
                //showMyTips("请选择jpeg、png格式的图片", false);  
                return;
            }
            // var URL = URL || webkitURL;  
            //获取照片方向角属性，用户旋转控制  
            EXIF.getData(file, function() {
                // alert(EXIF.pretty(this));  
                EXIF.getAllTags(this);
                //alert(EXIF.getTag(this, 'Orientation'));   
                Orientation = EXIF.getTag(this, 'Orientation');
                //return;  
            });
            var oReader = new FileReader();
            oReader.onload = function(e) {
                //var blob = URL.createObjectURL(file);  
                //_compress(blob, file, basePath);  
                var image = new Image();
                image.src = e.target.result;
                image.onload = function() {
                    var expectWidth = this.naturalWidth;
                    var expectHeight = this.naturalHeight;
                    if (this.naturalWidth > this.naturalHeight && this.naturalWidth > 800) {
                        expectWidth = 800;
                        expectHeight = expectWidth * this.naturalHeight / this.naturalWidth;
                    } else if (this.naturalHeight > this.naturalWidth && this.naturalHeight > 1200) {
                        expectHeight = 1200;
                        expectWidth = expectHeight * this.naturalWidth / this.naturalHeight;
                    }
                    var canvas = document.createElement("canvas");
                    var ctx = canvas.getContext("2d");
                    canvas.width = expectWidth;
                    canvas.height = expectHeight;
                    ctx.drawImage(this, 0, 0, expectWidth, expectHeight);
                    var base64 = canvas.toDataURL("image/jpeg", 1);
                    // console.log(base64);
                    // 压缩图片
                    //修复ios  
                    if (navigator.userAgent.match(/iphone/i)) {
                        // alert('苹果' + Orientation);
                        // console.log('iphone');  
                        //alert(expectWidth + ',' + expectHeight);  
                        //如果方向角不为1，都需要进行旋转 added by lzk  
                        if (Orientation != "" && Orientation != 1) {
                            switch (Orientation) {
                                case 6: //需要顺时针（向左）90度旋转  
                                    // alert('需要顺时针（向左）90度旋转');  
                                    rotateImg(this, 'left', canvas);
                                    break;
                                case 8: //需要逆时针（向右）90度旋转  
                                    // alert('需要顺时针（向右）90度旋转');  
                                    rotateImg(this, 'right', canvas);
                                    break;
                                case 3: //需要180度旋转  
                                    // alert('需要180度旋转');  
                                    rotateImg(this, 'right', canvas); //转两次  
                                    rotateImg(this, 'right', canvas);
                                    break;
                            }
                        }
                        /*var mpImg = new MegaPixImage(image); 
                        mpImg.render(canvas, { 
                            maxWidth: 800, 
                            maxHeight: 1200, 
                            quality: 1, 
                            orientation: 8 
                        });*/
                        base64 = canvas.toDataURL("image/jpeg", 1);
                    } else if (navigator.userAgent.match(/Android/i)) { // 修复android  
                        // alert('安卓' + Orientation);
                        if (Orientation != "" && Orientation != 1) {
                            switch (Orientation) {
                                case 6: //需要顺时针（向左）90度旋转  
                                    // alert('需要顺时针（向左）90度旋转');
                                    rotateImg(this, 'left', canvas);
                                    break;
                                case 8: //需要逆时针（向右）90度旋转  
                                    // alert('需要顺时针（向右）90度旋转');
                                    rotateImg(this, 'right', canvas);
                                    break;
                                case 3: //需要180度旋转  
                                    // alert('需要180度旋转');
                                    rotateImg(this, 'right', canvas); //转两次  
                                    rotateImg(this, 'right', canvas);
                                    break;
                            }
                        }
                        base64 = canvas.toDataURL("image/jpeg", 1);
                    } else {
                        // alert('Orientation');
                        //alert(Orientation);  
                        if (Orientation != "" && Orientation != 1) {
                            switch (Orientation) {
                                case 6: //需要顺时针（向左）90度旋转  
                                    // alert('需要顺时针（向左）90度旋转');
                                    rotateImg(this, 'left', canvas);
                                    break;
                                case 8: //需要逆时针（向右）90度旋转  
                                    // alert('需要顺时针（向右）90度旋转');
                                    rotateImg(this, 'right', canvas);
                                    break;
                                case 3: //需要180度旋转  
                                    // alert('需要180度旋转');
                                    rotateImg(this, 'right', canvas); //转两次  
                                    rotateImg(this, 'right', canvas);
                                    break;
                            }
                        }
                        base64 = canvas.toDataURL("image/jpeg", 1);
                    }
                    //uploadImage(base64);
                    //显示图片
                    //储存原图片的尺寸
                    var img = new Image();
                    // 在页面中呈现的图片尺寸（等比例缩小）
                    var board = {};
                    imgObjectURL = img.src = base64
                    img.onload = function() {
                        o_width = imgData.origin.width = img.width;
                        o_height = imgData.origin.height = img.height;
                        inter_base64("tp", base64);
                        board.width = containerWidth;
                        //要使用的图像的高度。（伸展或缩小图像）
                        board.height = containerHeight;
                        if (imgData.origin.width >= imgData.origin.height) {
                            board.height = containerWidth / imgData.origin.width * imgData.origin.height
                        } else if (imgData.origin.width < imgData.origin.height) {
                            board.width = containerHeight / imgData.origin.height * imgData.origin.width
                        }
                        var offsetx = (canvasW - board.width * imgData.scale) / 2;
                        var offsety = (canvasH - board.height * imgData.scale) / 2;
                        imgData.move.x = offsetx;
                        imgData.move.y = offsety;
                        $uploadBg.css({
                            width: board.width + 'px',
                            height: board.height + 'px',
                            left: offsetx + 'px',
                            top: offsety + 'px'
                        });
                        // 为画板填充图像
                        $uploadBg.css("backgroundImage", "url(\"" + base64 + "\")");
                    }
                };
            };
            oReader.readAsDataURL(file);
        }
    }
    //对图片旋转处理 added by lzk  
    function rotateImg(img, direction, canvas) {
        //alert(img);  
        //最小与最大旋转方向，图片旋转4次后回到原方向    
        var min_step = 0;
        var max_step = 3;
        //var img = document.getElementById(pid);    
        if (img == null) return;
        //img的高度和宽度不能在img元素隐藏后获取，否则会出错    
        var height = img.height;
        var width = img.width;
        //var step = img.getAttribute('step');    
        var step = 2;
        if (step == null) {
            step = min_step;
        }
        if (direction == 'right') {
            step++;
            //旋转到原位置，即超过最大值    
            step > max_step && (step = min_step);
        } else {
            step--;
            step < min_step && (step = max_step);
        }
        //img.setAttribute('step', step);    
        /*var canvas = document.getElementById('pic_' + pid);   
        if (canvas == null) {   
            img.style.display = 'none';   
            canvas = document.createElement('canvas');   
            canvas.setAttribute('id', 'pic_' + pid);   
            img.parentNode.appendChild(canvas);   
        }  */
        //旋转角度以弧度值为参数    
        var degree = step * 90 * Math.PI / 180;
        var ctx = canvas.getContext('2d');
        switch (step) {
            case 0:
                canvas.width = width;
                canvas.height = height;
                ctx.drawImage(img, 0, 0);
                break;
            case 1:
                canvas.width = height;
                canvas.height = width;
                ctx.rotate(degree);
                ctx.drawImage(img, 0, -height);
                break;
            case 2:
                canvas.width = width;
                canvas.height = height;
                ctx.rotate(degree);
                ctx.drawImage(img, -width, -height);
                break;
            case 3:
                canvas.width = height;
                canvas.height = width;
                ctx.rotate(degree);
                ctx.drawImage(img, -width, 0);
                break;
        }
    }
    //图片识别接口，图谱以及化华为云
    function inter_base64(type, img_base64) {
        if (type == "tp") {
            var w = 512;
            var h = 512;
        } else {
            var w = 800;
            var h = 800;
        }
        if (o_width >= o_height) {
            h = w / o_width * o_height;
        } else if (o_width < o_height) {
            w = h / o_height * o_width;
        }

        var image = new Image();
        image.src = img_base64;
        image.onload = function() {
            var canvas_zip = document.createElement("canvas");
            var ctx_zip = canvas_zip.getContext("2d");
            canvas_zip.width = w;
            canvas_zip.height = h;
            ctx_zip.drawImage(this, 0, 0, w, h);
            img_base64_min = canvas_zip.toDataURL("image/jpeg", 1);
            var data = {
                "img_base64": img_base64,
                "img_base64_min": img_base64_min,
            }
            // console.log(img_base64_min);
            if (type == 'tp') {
                tp_interface(data);
            } else {
                ai_interface(data);
            }
        }
    }
    return {
        // selectImg: selectImg,
        cameraImg: cameraImg,
        albumImg: albumImg,
        handleFiles: handleFiles,
        createImg: createImg,
        submit: submit,
        imgData: imgData,
        createLocalImg: createLocalImg
    }
};