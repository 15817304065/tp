<?php
namespace Api_pc\Controller;

/*人脸识别接口frs*/
use Think\Controller;

class FrsController extends Controller
{
    public $ismobile = false;
    public function _initialize()
    {
        header('Access-Control-Allow-Origin:*');
        $openid         = I('post.openid') ? I('post.openid') : I('post.userip');
        $this->ismobile = I('post.ismobile') ? true : false;
        A('search')->add_api_num(ACTION_NAME, $openid);

    }
    public function face_detect() //5.1   客流识别

    {
        if (!empty($_FILES['file'])) {
            //通过uploadfile上传的临时文件
            if (!in_array($_FILES['file']['type'], C('IMGTYPE'))) {
                apiResponse("0", "图像格式不支持!");
            }
            $baseimg = base64_encode(file_get_contents($_FILES['file']['tmp_name']));

            $data = array(
                "image_base64" => $baseimg,
                "attributes"   => "0,1,2,4,5",
            );
        } else if (!empty(I('post.'))) {
            //接收base64图像数据
            $p = I('post.');
            if (empty($p['image'])) {
                apiResponse("0", "参数有误");
            }
            $data = array(
                "image_base64" => $p['image'],
                "attributes"   => "0,1,2,4,5",
            );
        } else {
            apiResponse("0", "参数有误");
        }
        $obj     = new \Common\Util\EI();
        $imgobj  = new \Common\Util\Gd();
        $re      = $obj->face_detect($data);
        $result  = json_decode($re, true);
        $imgfile = [];
        $img0    = A('image')->return_img_url($data['image_base64']);
        foreach ($result['faces'] as $k => $v) {
            $x                          = (int) $v['bounding_box']['top_left_x'];
            $y                          = (int) $v['bounding_box']['top_left_y'];
            $width                      = (int) $v['bounding_box']['width'];
            $height                     = (int) $v['bounding_box']['height'];
            $result['faces'][$k]['url'] = $imgobj->cutImg($img0, $x, $y, $width, $height); //生成脸部小头像
        }
        if (isset($result['faces'])) {
            apiResponse("1", "success", "200", $result['faces']);
        } else {
            A('search')->add_errorcode($this->ismobile, ACTION_NAME, $result['error_msg'], $result['error_code']);
            apiResponse("0", $result['error_msg'], $result['error_code']);
        }
    }

    public function face_compare() //5.2   身份验证

    {

        if (!empty($_FILES['image1']) && !empty($_FILES['image2'])) {
            //通过uploadfile上传的临时文件
            if (!in_array($_FILES['image1']['type'], C('IMGTYPE')) || !in_array($_FILES['image2']['type'], C('IMGTYPE'))) {
                apiResponse("0", "图像格式不支持!");
            }
            $baseimg1 = base64_encode(file_get_contents($_FILES['image1']['tmp_name']));
            $baseimg2 = base64_encode(file_get_contents($_FILES['image2']['tmp_name']));

            $data = array(
                "image1_base64" => $baseimg1,
                "image2_base64" => $baseimg2,
            );
        } else if (!empty(I('post.'))) {

            //接收base64图像数据
            $p = I('post.');
            if (!empty($p['image1']) && !empty($p['image2'])) {
                $data = array(
                    "image1_base64" => $p['image1'],
                    "image2_base64" => $p['image2'],
                );
            } else if (!empty($p['url1']) && !empty($p['url2'])) {
                $p['url1']   = trim($p['url1']);
                $p['url2']   = trim($p['url2']);
                $base64_img1 = base64_encode(file_get_contents($p['url1']));
                $base64_img2 = base64_encode(file_get_contents($p['url2']));
                $path1       = A('image')->return_img_url($base64_img1);
                $path2       = A('image')->return_img_url($base64_img2);
                $baseimg1    = base64_encode(file_get_contents($path1));
                $baseimg2    = base64_encode(file_get_contents($path2));
                $data        = array(
                    "image1_base64" => $baseimg1,
                    "image2_base64" => $baseimg2,
                );
            }

        } else {
            apiResponse("0", "参数有误");
        }

        $obj = new \Common\Util\EI();
        $re  = $obj->face_compare($data);

        $result = json_decode($re, true);

        if (isset($result['image1_face'])) {

            apiResponse("1", "success", "200", $result);
        } else {
            A('search')->add_errorcode($this->ismobile, ACTION_NAME, $result['error_msg'], $result['error_code']);
            apiResponse("0", $result['error_msg'], $result['error_code']);
        }
    }

    public function _empty()
    {
        echo json_encode(array('state' => '0', 'msg' => 'no action'));
    }

}
