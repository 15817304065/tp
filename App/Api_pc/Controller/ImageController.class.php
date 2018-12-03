<?php
namespace Api_pc\Controller;

/*图像识别接口Image*/
use Think\Controller;

class ImageController extends Controller
{
    public $ismobile = false;
    public function _initialize()
    {
        header('Access-Control-Allow-Origin:*');
        $openid         = I('post.openid') ? I('post.openid') : I('post.userip');
        $this->ismobile = I('post.ismobile') ? true : false;
        A('search')->add_api_num(ACTION_NAME, $openid);

    }
    public function tag_image() //2.1   图像标签

    {

        if (!empty($_FILES['file'])) {
            //通过uploadfile上传的临时文件
            if (!in_array($_FILES['file']['type'], C('IMGTYPE'))) {
                apiResponse("0", "图像格式不支持!");
            }
            $baseimg = base64_encode(file_get_contents($_FILES['file']['tmp_name']));

            $data = array(
                "image"     => $baseimg,
                "limit"     => 15, //默认-1
                "threshold" => 0, //（0~100）默认值：0
                "language"  => 'zh',
            );
        } else if (!empty(I('post.'))) {
            //接收base64图像数据
            $p = I('post.');

            if (!empty($p['image'])) {
                $data = array(
                    "image"     => $p['image'],
                    "limit"     => 15, //默认-1
                    "threshold" => 0, //（0~100）默认值：0
                    "language"  => 'zh',
                );
            } else if (!empty($p['url'])) {
                $p['url'] = trim($p['url']);
                $data     = array(
                    "image"     => base64_encode(file_get_contents($p['url'])),
                    "limit"     => 15, //默认-1
                    "threshold" => 0, //（0~100）默认值：0
                    "language"  => 'zh',
                );
            } else {
                apiResponse("0", "参数有误");
            }

        } else {
            apiResponse("0", "参数有误");
        }

        $obj    = new \Common\Util\EI();
        $re     = $obj->tag_image($data);
        $item   = I('post.item') ? I('post.item') : "";
        $result = json_decode($re, true);

        // var_dump($result);die;

        if (isset($result['result'])) {
            foreach ($result['result']['tags'] as $k => $v) {
                $result['result']['tags'][$k]['confidence'] = round($v['confidence'], 2);
            }
            apiResponse("1", "success", "200", array('data' => $result['result']['tags'], 'item' => $item));
        } else {
            A('search')->add_errorcode($this->ismobile, ACTION_NAME, $result['error_msg'], $result['error_code']);
            apiResponse("0", $result['error_msg'], $result['error_code']);
        }
    }

    public function dark_enhance() //2.2低光照增强

    {

        if (!empty($_FILES['file'])) {
            //通过uploadfile上传的临时文件
            if (!in_array($_FILES['file']['type'], C('IMGTYPE'))) {
                apiResponse("0", "图像格式不支持!");
            }
            $baseimg = base64_encode(file_get_contents($_FILES['file']['tmp_name']));

            $data = array(
                "image"      => $baseimg,
                "brightness" => 0.9, //亮度值，默认值0.9，取值范围：[0,1]
            );
        } else if (!empty(I('post.'))) {
            //接收base64图像数据
            $p = I('post.');
            if (empty($p['image'])) {
                apiResponse("0", "参数有误");
            }

            $data = array(
                "image"      => $p['image'],
                "brightness" => 0.9, //亮度值，默认值0.9，取值范围：[0,1]
            );
        } else {
            apiResponse("0", "参数有误");
        }
        $obj    = new \Common\Util\EI();
        $re     = $obj->dark_enhance($data);
        $item   = I('post.item') ? I('post.item') : "";
        $result = json_decode($re, true);

        if (isset($result['result'])) {
            // $res = $this->return_img_url($result['result']);
            apiResponse("1", "success", "200", array('data' => $result['result'], 'item' => $item));
        } else {
            A('search')->add_errorcode($this->ismobile, ACTION_NAME, $result['error_msg'], $result['error_code']);
            apiResponse("0", $result['error_msg'], $result['error_code']);
        }
    }

    public function defog_image() //2.3 图像去雾

    {

        if (!empty($_FILES['file'])) {
            //通过uploadfile上传的临时文件
            if (!in_array($_FILES['file']['type'], C('IMGTYPE'))) {
                apiResponse("0", "图像格式不支持!");
            }
            $baseimg = base64_encode(file_get_contents($_FILES['file']['tmp_name']));

            $data = array(
                "image"        => $baseimg,
                "gamma"        => 1.5, //gamma矫正值，默认为1.5，取值范围：[0.1,10]
                "natural_look" => true, //是否保持自然观感，默认true
            );
        } else if (!empty(I('post.'))) {
            //接收base64图像数据
            $p = I('post.');
            if (empty($p['image'])) {
                apiResponse("0", "参数有误");
            }

            $data = array(
                "image"        => $p['image'],
                "gamma"        => 1.5, //gamma矫正值，默认为1.5，取值范围：[0.1,10]
                "natural_look" => true, //是否保持自然观感，默认true
            );
        } else {
            apiResponse("0", "参数有误");
        }
        $obj = new \Common\Util\EI();
        $re  = $obj->defog_image($data);

        $result = json_decode($re, true);
        $item   = I('post.item') ? I('post.item') : "";
        if (!isset($result['result'])) {
            A('search')->add_errorcode($this->ismobile, ACTION_NAME, $result['error_msg'], $result['error_code']);
            apiResponse("0", $result['error_msg'], $result['error_code']);
        } else {
            apiResponse("1", "success", "200", array('data' => $result['result'], 'item' => $item));
            // $res = $this->return_img_url($result['result']);

            // if ($res) {
            //     apiResponse("1", "success", "200", $res);
            // } else {
            //     apiResponse("0", "数据保存失败");
            // }

        }
    }

    public function return_img_url($base64)
    {
        $img_name = uniqid();
        $date     = date('Ymd');
        $path     = "Uploads/imgtemp/{$date}/";
        $new_file = $path . "{$img_name}.jpg";

        is_dir($path) || mkdir($path, 0777, true);
        if (base64_encode(base64_decode($base64))) {
            $result = file_put_contents($new_file, base64_decode($base64));
        } else {
            $result = file_put_contents($new_file, $base64);
        }

        if ($result) {
            $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
            $url       = $http_type . $_SERVER['HTTP_HOST'] . __ROOT__ . "/" . $new_file;
            return $url;
        }
        return false;

    }

    public function search_copyright() //6.1   图片版权查询

    {
        if (!empty($_FILES['file'])) {
            //通过uploadfile上传的临时文件
            if (!in_array($_FILES['file']['type'], C('IMGTYPE'))) {
                apiResponse("0", "图像格式不支持!");
            }
            $baseimg = base64_encode(file_get_contents($_FILES['file']['tmp_name']));

            $data = array(
                "image"  => $baseimg,
                "offset" => 0, //必要参数,偏移量，指定检索图像返回结果起始位置
                "limit"  => 15, //返回检索结果数量,必要参数
            );
        } else if (!empty(I('post.'))) {
            //接收base64图像数据
            $p = I('post.');
            if (empty($p['image'])) {
                apiResponse("0", "参数有误");
            }
            $data = array(
                "image"  => $p['image'],
                "offset" => 0, //必要参数,偏移量，指定检索图像返回结果起始位置
                "limit"  => 15, //返回检索结果数量,必要参数
            );
        } else {
            apiResponse("0", "参数有误");
        }
        $obj = new \Common\Util\EI();
        $re  = $obj->search_copyright($data);

        $result = json_decode($re, true);

        if (isset($result['result'])) {

            $res = $this->set_image($result, ACTION_NAME);

            apiResponse("1", "success", "200", $res);
        } else {
            A('search')->add_errorcode($this->ismobile, ACTION_NAME, $result['error_msg'], $result['error_code']);
            apiResponse("0", $result['error_msg'], $result['error_code']);
        }
    }
    public function search_material() //6.2    图片素材查找

    {

        if (!empty($_FILES['file'])) {
            //通过uploadfile上传的临时文件
            if (!in_array($_FILES['file']['type'], C('IMGTYPE'))) {
                apiResponse("0", "图像格式不支持!");
            }
            $baseimg = base64_encode(file_get_contents($_FILES['file']['tmp_name']));

            $data = array(
                "image"  => $baseimg,
                "offset" => 0, //必要参数,偏移量，指定检索图像返回结果起始位置
                "limit"  => 15, //返回检索结果数量,必要参数
            );
        } else if (!empty(I('post.'))) {
            //接收base64图像数据
            $p = I('post.');
            if (empty($p['image'])) {
                apiResponse("0", "参数有误");
            }

            $data = array(
                "image"  => $p['image'],
                "offset" => 0, //必要参数,偏移量，指定检索图像返回结果起始位置
                "limit"  => 15, //返回检索结果数量,必要参数
            );
        } else {
            apiResponse("0", "参数有误");
        }
        $obj = new \Common\Util\EI();
        $re  = $obj->search_material($data);

        $result = json_decode($re, true);

        if (isset($result['result'])) {
            $res = $this->set_image($result, ACTION_NAME);

            apiResponse("1", "success", "200", $res);
        } else {
            A('search')->add_errorcode($this->ismobile, ACTION_NAME, $result['error_msg'], $result['error_code']);
            apiResponse("0", $result['error_msg'], $result['error_code']);
        }
    }
    public function search_commodity() //6.3 以图搜图

    {

        if (!empty($_FILES['file'])) {
            //通过uploadfile上传的临时文件
            if (!in_array($_FILES['file']['type'], C('IMGTYPE'))) {
                apiResponse("0", "图像格式不支持!");
            }
            $baseimg = base64_encode(file_get_contents($_FILES['file']['tmp_name']));

            $data = array(
                "image"  => $baseimg,
                "offset" => 0, //必要参数,偏移量，指定检索图像返回结果起始位置
                "limit"  => 15, //返回检索结果数量,必要参数
            );
        } else if (!empty(I('post.'))) {
            //接收base64图像数据
            $p = I('post.');
            if (empty($p['image'])) {
                apiResponse("0", "参数有误");
            }

            $data = array(
                "image"  => $p['image'],
                "offset" => 0, //必要参数,偏移量，指定检索图像返回结果起始位置
                "limit"  => 15, //返回检索结果数量,必要参数
            );
        } else {
            apiResponse("0", "参数有误");
        }
        $obj = new \Common\Util\EI();
        $re  = $obj->search_commodity($data);

        $result = json_decode($re, true);

        if (isset($result['result'])) {
            $res = $this->set_image($result, ACTION_NAME);

            apiResponse("1", "success", "200", $res);
        } else {
            A('search')->add_errorcode($this->ismobile, ACTION_NAME, $result['error_msg'], $result['error_code']);
            apiResponse("0", $result['error_msg'], $result['error_code']);
        }
    }

    private function set_image($data, $name)
    {
        $img_path = "";
        if ($name == "search_copyright") {
            $img_path = "img_copyright/";
        } else if ($name == "search_commodity") {
            $img_path = "img_makeup/";
        }
        foreach ($data['result'] as $k => $v) {
            $data['result'][$k]['path'] = 'http://114.116.81.119:8060/' . $img_path . $v['path'];
        }
        return $data;

    }

    public function image_to_base64()
    {
        if (!empty($_FILES['file'])) {
            //通过uploadfile上传的临时文件
            if (!in_array($_FILES['file']['type'], C('IMGTYPE'))) {
                apiResponse("0", "图片类型有误");
            }
            // zoom($file, 300, $new_w = 600);
            $baseimg = base64_encode(file_get_contents($_FILES['file']['tmp_name']));
            apiResponse("1", "success", "200", $baseimg);

        } else {
            apiResponse("0", "参数有误");
        }
    }

    public function _empty()
    {
        echo json_encode(array('state' => '0', 'msg' => 'no action'));
    }

}
