<?php
namespace Api_h5\Controller;

/*内容检测接口Moderation*/

use Think\Controller;

class ModerationController extends Controller
{
    public function _initialize()
    {
        header('Access-Control-Allow-Origin:*');

        $openid = I('post.openid') ? I('post.openid') : I('post.userip');

        A('search')->add_api_num2(ACTION_NAME, $openid);
    }
    public function clarity_detect() //3.1  清晰度检测(未使用)

    {
        if (!empty($_FILES['file'])) {
            //通过uploadfile上传的临时文件
            if (!in_array($_FILES['file']['type'], C('IMGTYPE'))) {
                apiResponse("0", "图像格式不支持!");
            }
            $baseimg = base64_encode(file_get_contents($_FILES['file']['tmp_name']));
            $data    = array(
                "image"     => $baseimg,
                "threshold" => 0.8, //默认为0.8
            );
        } else if (!empty(I('post.'))) {
            //接收base64图像数据
            $p = I('post.');
            if (empty($p['image'])) {
                apiResponse("0", "参数有误");
            }
            $data = array(
                "image"     => $p['image'],
                "threshold" => 0.8, //默认为0.8
            );
        } else {
            apiResponse("0", "参数有误");
        }
        $item = I('post.item') ? I('post.item') : "";
        $obj  = new \Common\Util\EI();
        $re   = $obj->clarity_detect($data);

        $result = json_decode($re, true);

        if (isset($result['result'])) {

            apiResponse("1", "success", "200", array("data" => $result['result'], 'item' => $item));
        } else {
            apiResponse("0", $result['error_msg'], $result['error_code']);

        }
    }

    public function distortion_correct() //3.2   扭曲校正

    {

        if (!empty($_FILES['file'])) {
            //通过uploadfile上传的临时文件
            if (!in_array($_FILES['file']['type'], C('IMGTYPE'))) {
                apiResponse("0", "图像格式不支持!");
            }
            $baseimg = base64_encode(file_get_contents($_FILES['file']['tmp_name']));

            $data = array(
                "image"      => $baseimg,
                "correction" => true, //是否要进行图片扭曲校。true：校正。默认校正。false：不进行校正

            );
        } else if (!empty(I('post.'))) {
            //接收base64图像数据
            $p = I('post.');
            if (empty($p['image'])) {
                apiResponse("0", "参数有误");
            }
            $data = array(
                "image"      => $p['image'],
                "correction" => true, //是否要进行图片扭曲校。true：校正。默认校正。false：不进行校正
            );
        } else {
            apiResponse("0", "参数有误");
        }
        $obj = new \Common\Util\EI();
        $re  = $obj->distortion_correct($data);

        $result = json_decode($re, true);
        $item   = I('post.item') ? I('post.item') : "";
        if (isset($result['result'])) {
            if ($result['result']['distortion']) {
                // $res = A('image')->return_img_url($result['result']['data']);
                apiResponse("1", "success", "200", array('data' => $result['result']['data'], 'item' => $item));
            } else {
                apiResponse("0", "图像无需矫正");
            }

        } else {
            apiResponse("0", $result['error_msg'], $result['error_code']);
        }
    }
    public function general_service_review() //  图片文字审核

    {
        if (!empty($_FILES['file'])) {
            //通过uploadfile上传的临时文件
            if (!in_array($_FILES['file']['type'], C('IMGTYPE'))) {
                apiResponse("0", "图像格式不支持!");
            }
            $baseimg = base64_encode(file_get_contents($_FILES['file']['tmp_name']));
            $data    = array(
                "image"      => $baseimg,
                "categories" => ['porn', 'terrorism', 'politics'], //为空则只检测terrorism,politics
                "threshold"  => "",
            );
            $type = 'image';
        } else if (!empty(I('post.'))) {
            $p = I('post.');
            if (!empty($p['image'])) {
                $data = array(
                    "image"      => $p['image'],
                    "categories" => ['porn', 'terrorism', 'politics'], //为空则只检测terrorism,politics
                    "threshold"  => "",
                );
                $type = 'image';
            } else if (!empty($p['text']) || I('post.text') == "0") {
                $data = array(
                    "categories" => [], //为空则只检测terrorism,politics
                    "items"      => array(
                        array(
                            "text" => $p['text'],
                            "type" => "content",
                        ),
                    ),
                );
                $type = 'text';
            } else {
                apiResponse("0", "参数有误");
            }
        } else {
            apiResponse("0", "参数有误");
        }
        $obj = new \Common\Util\EI();

        $re = $obj->general_service_review($data);

        $result = json_decode($re, true);

        if (isset($result['result'])) {
            $item = $this->set_data($result['result'], $type);

            apiResponse("1", "success", "200", $item);
        } else {
            apiResponse("0", $result['error_msg'], $result['error_code']);
        }
    }

    private function set_data($data, $type = "text")
    {
        // var_dump($data);
        $tt = $data['detail'];
        if ($type == "text") {
            $arr = ['suggestion' => $data['suggestion'], 'item' => []];
            if (empty($data['detail'])) {
                return $arr;
            }
            if (isset($tt['politics'])) {
                $arr['item'][] = "涉政";
            }
            if (isset($tt['porn'])) {
                $arr['item'][] = "涉黄";
            }
            if (isset($tt['ad'])) {
                $arr['item'][] = "广告";
            }
            if (isset($tt['abuse'])) {
                $arr['item'][] = "辱骂";
            }
            if (isset($tt['contraband'])) {
                $arr['item'][] = "违禁品";
            }
            if (isset($tt['flood'])) {
                $arr['item'][] = "灌水";
            }
        } else {
            $arr = ['suggestion' => $data['suggestion'], 'item' => []];
            if (!empty($tt['politics'])) {
                $arr['item'][] = "涉政";
                return $arr;
            }
            if ($tt['porn'][1]['confidence'] > 0.5) {

                $arr['item'][] = "涉黄"; //色情值
            }
            if ($tt['terrorism'][9]['confidence'] < 0.5) {

                $arr['item'][] = "暴恐"; //正常值
            }
        }
        return $arr;

    }
    public function game_avatar() //3.4    游戏头像违规(未使用)

    {
        if (!empty($_FILES['file'])) {
            //通过uploadfile上传的临时文件
            if (!in_array($_FILES['file']['type'], C('IMGTYPE'))) {
                apiResponse("0", "图像格式不支持!");
            }
            $baseimg = base64_encode(file_get_contents($_FILES['file']['tmp_name']));

            $data = array(
                "image"      => $baseimg,
                "categories" => ['porn', 'terrorism', 'politics'], //为空则只检测terrorism,politics
                "threshold"  => "",
            );
        } else if (!empty(I('post.'))) {
            //接收base64图像数据
            $p = I('post.');
            if (empty($p['image'])) {
                apiResponse("0", "参数有误");
            }

            $data = array(
                "image"      => $p['image'],
                "categories" => ['porn', 'terrorism', 'politics'], //为空则只检测terrorism,politics
                "threshold"  => "",
            );
        } else {
            apiResponse("0", "参数有误");
        }
        $obj = new \Common\Util\EI();
        $re  = $obj->game_avatar($data);

        $result = json_decode($re, true);

        if (isset($result['result'])) {
            apiResponse("1", "success", "200", $result['result']);
        } else {
            apiResponse("0", $result['error_msg'], $result['error_code']);
        }
    }

    public function content_review() //3.5政暴黄审核

    {
        if (!empty($_FILES['file'])) {
            //通过uploadfile上传的临时文件
            if (!in_array($_FILES['file']['type'], C('IMGTYPE'))) {
                apiResponse("0", "图像格式不支持!");
            }
            $baseimg    = base64_encode(file_get_contents($_FILES['file']['tmp_name']));
            $categories = I('post.categories');
            $data       = array(
                "image"      => $baseimg,
                "categories" => array($categories),
                "threshold"  => "",
            );
        } else if (!empty(I('post.'))) {
            //接收base64图像数据
            $p = I('post.');
            if (empty($p['image']) || empty($p['categories'])) {
                apiResponse("0", "参数不完整");
            }
            $data = array(
                "image"      => $p['image'],
                "categories" => array($p['categories']),
                "threshold"  => "",
            );
        } else {
            apiResponse("0", "参数有误");
        }
        $obj = new \Common\Util\EI();

        $re = $obj->social_content_review($data);

        $result = json_decode($re, true);

        if (isset($result['result'])) {
            apiResponse("1", "success", "200", $result['result']);
        } else {
            apiResponse("0", $result['error_msg'], $result['error_code']);
        }

    }
    public function ecommerce_review() //3.6   电商评论

    {
        $p = I('post.');

        if (!empty($p['text']) || $p['text'] == "0") {
            $data = array(
                "categories" => [],
                "items"      => [
                    [
                        "text" => $p['text'],
                        "type" => "content",
                    ],
                ],
            );
        } else {
            apiResponse("0", "参数有误");
        }
        $obj = new \Common\Util\EI();
        $re  = $obj->ecommerce_review($data);

        $result = json_decode($re, true);

        if (isset($result['result'])) {
            $item         = $this->set_data($result['result']);
            $item['text'] = $p['text'];

            apiResponse("1", "success", "200", $item);
        } else {
            apiResponse("0", $result['error_msg'], $result['error_code']);
        }
    }

    public function _empty()
    {
        apiResponse("0", "no action");
    }

}
