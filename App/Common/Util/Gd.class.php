<?php
namespace Common\Util;

class Gd
{

    //获取图片信息宽、高、类型
    public function getinfo($path)
    {
        $info = getimagesize($path);
        $info = array(
            'width'  => $info[0],
            'height' => $info[1],
            'type'   => image_type_to_extension($info['2'], false),
            'mime'   => $info['mime'],
        );
        return $info;
    }

    //在内存中创建图像
    public function createimg($path, $type)
    {
        $fun   = "imagecreatefrom{$type}";
        $image = $fun($path);
        return $image;
    }

    //销毁内存中的图像
    public function destroyimg($image)
    {
        return imagedestroy($image);
    }

    /**
     *$img  图像资源createimg()创建得到
     *$type 图像类型jpg/png/gif
     *$mime 表头类型image/png
     */
    //打印到网页中
    public function showimg($image, $type, $mime)
    {
        header("Content-type:" . $mime);
        $fun = "image{$type}";
        return $fun($image);
    }
    //保存图片
    public function saveimg($image, $type, $savepath)
    {
        $fun = "image{$type}";
        return $fun($image, $savepath);
    }

    public function copyimg($image_1, $image_2)
    {
        //创建一个和黄色图片一样大小的真彩色画布（ps：只有这样才能保证后面copy红色图片的时候不会失真）
        $image_3 = imageCreatetruecolor(imagesx($image_1), imagesy($image_1));
        //为真彩色画布创建白色背景，再设置为透明
        $color = imagecolorallocatealpha($image_3, 0, 0, 0, 127);
        imagefill($image_3, 0, 0, $color);

        //首先将黄色画布采样copy到真彩色画布中，不会失真
        imagecopyresampled($image_3, $image_1, 0, 0, 0, 0, imagesx($image_1), imagesy($image_1), imagesx($image_1), imagesy($image_1));
        //首先将红色画布采样copy到真彩色画布中，不会失真
        imagecopyresampled($image_3, $image_2, 0, 0, 0, 0, imagesx($image_2), imagesy($image_2), imagesx($image_2), imagesy($image_2));

        //销毁其他图片
        $this->destroyimg($image_1);
        $this->destroyimg($image_2);
        // $this->destroyimg($image_4);

        imagesavealpha($image_3, true);
        return $image_3;
    }

    /*
     * @parm $dst_image 底图对象
     * @parm $src_image顶图对象
     * @parm $dst_x 底图x坐标
     * @parm $dst_y 底图y坐标
     * @parm $src_x 顶图x坐标
     * @parm $src_y 顶图x坐标
     */
    public function copyimgmax($dst_image, $src_image, $dst_x = 0, $dst_y = 0, $src_x = 0, $src_y = 0)
    {
        //创建一个和黄色图片一样大小的真彩色画布（ps：只有这样才能保证后面copy红色图片的时候不会失真）
        $temp_image = imageCreatetruecolor(imagesx($dst_image), imagesy($dst_image));
        //为真彩色画布创建白色背景，再设置为透明
        $color = imagecolorallocatealpha($temp_image, 0, 0, 0, 127);
        imagefill($temp_image, 0, 0, $color);

        //首先将黄色画布采样copy到真彩色画布中，不会失真
        imagecopyresampled($temp_image, $dst_image, $dst_x, $dst_y, 0, 0, imagesx($dst_image), imagesy($dst_image), imagesx($dst_image), imagesy($dst_image));
        //首先将红色画布采样copy到真彩色画布中，不会失真
        imagecopyresampled($temp_image, $src_image, $src_x, $src_y, 0, 0, imagesx($src_image), imagesy($src_image), imagesx($src_image), imagesy($src_image));

        //销毁其他图片
        $this->destroyimg($dst_image);
        $this->destroyimg($src_image);

        imagesavealpha($temp_image, true);
        return $temp_image;
        // $this->destroyimg($temp_image);
    }

    //裁剪图片
    public function tailoring($image)
    {
        $pic   = imageCreatetruecolor(495, 495);
        $color = imagecolorallocate($pic, 255, 255, 255);
        imagefill($pic, 0, 0, $color);
        imageColorTransparent($pic, $color);
        imagecopyresampled($pic, $image, 0, 0, 80, 15, 495, 495, 495, 495);
        $this->destroyimg($image);
        return $pic;
    }

    //修改图片尺寸
    public function thumbimg($image, $width, $height)
    {
        $pic   = imageCreatetruecolor($width, $height);
        $color = imagecolorallocate($pic, 255, 255, 255);
        imagefill($pic, 0, 0, $color);
        imageColorTransparent($pic, $color);
        imagecopyresized($pic, $image, 0, 0, 0, 0, $width, $height, imagesx($image), imagesy($image));
        $this->destroyimg($image);
        return $pic;
    }

    //图像裁剪
    public function cutImg($imgfile, $x = 0, $y = 0, $width = 100, $height = 100)
    {

        $imginfo = getimagesize($imgfile);

        if (!$imginfo) {
            return false;
        }

        list($src_w, $src_h) = $imginfo;
        $mime                = $imginfo['mime'];
        $arr                 = explode('/', $mime);

        list($type, $subtype) = $arr;

        $open_img = 'imagecreatefrom' . $subtype;
        $save_img = 'image' . $subtype;

        $img = $open_img($imgfile);

        $cut_img = imagecreatetruecolor($width, $height);

        imagecopyresampled($cut_img, $img, 0, 0, $x, $y, $width, $height, $width, $height);

        $ext = pathinfo($imgfile, PATHINFO_EXTENSION);

        $date = date('Ymd');
        $path = "Uploads/imgtemp/{$date}/";
        is_dir($path) || mkdir($path, 0777, true);
        $save_img_name = $path . uniqid() . '.' . $ext;

        $save_img($cut_img, $save_img_name);

        imagedestroy($img);
        imagedestroy($cut_img);

        $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
        $url       = $http_type . $_SERVER['HTTP_HOST'] . __ROOT__ . "/" . $save_img_name;
        return $url;
    }

}
