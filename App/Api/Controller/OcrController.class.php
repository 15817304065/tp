<?php
namespace Api\Controller;

/*文字识别接口Ocr*/

use Think\Controller;

class OcrController extends Controller
{
    public function _initialize()
    {
        $openid = I('post.openid');

        A('search')->add_api_num(ACTION_NAME, $openid);
    }
    public function vat_invoice() //增值税发票识别

    {
        if (!empty($_FILES['file'])) {
            //通过uploadfile上传的临时文件
            if (!in_array($_FILES['file']['type'], C('IMGTYPE'))) {
                apiResponse("0", "图像格式不支持!");
            }
            $baseimg = base64_encode(file_get_contents($_FILES['file']['tmp_name']));
            $data    = array(
                "image" => $baseimg,
            );
        } else if (!empty(I('post.'))) {
            //接收base64图像数据
            $p = I('post.');
            if (empty($p['image'])) {
                apiResponse("0", "参数有误");
            }
            $data = array(
                "image" => $p['image'],
            );
        } else {
            apiResponse("0", "参数有误");
        }
        $obj = new \Common\Util\EI();
        $re  = $obj->vat_invoice($data);

        $result = json_decode($re, true);

        if (isset($result['result'])) {
            apiResponse("1", "success", "200", $result['result']);
        } else {
            apiResponse("0", $result['error_msg'], $result['error_code']);
        }
    }

    public function id_card() //身份证

    {

        if (!empty($_FILES['file'])) {
            if (!in_array($_FILES['file']['type'], C('IMGTYPE'))) {
                apiResponse("0", "图像格式不支持!");
            }
            $baseimg = base64_encode(file_get_contents($_FILES['file']['tmp_name']));

            $data = array(
                "image" => $baseimg,
                // "side"  => "front", //默认front,正面,back反面
            );
        } else if (!empty(I('post.'))) {
            $p = I('post.');
            if (empty($p['image'])) {
                apiResponse("0", "参数有误");
            }
            $obj  = new \Common\Util\EI();
            $data = array(
                "image" => $p['image'],
                // "side"  => "front", //默认front,正面,back反面
            );
        } else {
            apiResponse("0", "参数有误");
        }
        $obj = new \Common\Util\EI();
        $re  = $obj->id_card($data);

        $result = json_decode($re, true);

        if (isset($result['result'])) {
            apiResponse("1", "success", "200", $result['result']);
        } else {
            apiResponse("0", $result['error_msg'], $result['error_code']);
        }
    }

    public function general_table() //表格

    {

        if (!empty($_FILES['file'])) {
            if (!in_array($_FILES['file']['type'], C('IMGTYPE'))) {
                apiResponse("0", "图像格式不支持!");
            }
            $baseimg = base64_encode(file_get_contents($_FILES['file']['tmp_name']));

            $data = array(
                "image" => $baseimg,
            );
        } else if (!empty(I('post.'))) {
            $p = I('post.');
            if (empty($p['image'])) {
                apiResponse("0", "参数有误");
            }
            $data = array(
                "image" => $p['image'],
            );
        } else {
            apiResponse("0", "参数有误");
        }
        $obj = new \Common\Util\EI();
        $re  = $obj->general_table($data);

        $result = json_decode($re, true);

        if (isset($result['result'])) {
            $e = $this->set_table($result['result']['words_region_list']);

            apiResponse("1", "success", "200", $e);
        } else {
            apiResponse("0", $result['error_msg'], $result['error_code']);
        }
    }
    public function set_table($data)
    {
        $e = array();
        foreach ($data as $k => $v) {
            // $e[$v['type']] = $v['words_block_list'];
            foreach ($v['words_block_list'] as $key => $value) {
                $e[] = $value['words'];
            }
        }
        return $e;
    }

    public function driver_license() //驾驶证

    {

        if (!empty($_FILES['file'])) {
            if (!in_array($_FILES['file']['type'], C('IMGTYPE'))) {
                apiResponse("0", "图像格式不支持!");
            }
            $baseimg = base64_encode(file_get_contents($_FILES['file']['tmp_name']));

            $data = array(
                "image" => $baseimg,
            );
        } else if (!empty(I('post.'))) {
            $p = I('post.');
            if (empty($p['image'])) {
                apiResponse("0", "参数有误");
            }
            $data = array(
                "image" => $p['image'],
            );
        } else {
            apiResponse("0", "参数有误");
        }
        $obj = new \Common\Util\EI();
        $re  = $obj->driver_license($data);

        $result = json_decode($re, true);

        if (isset($result['result'])) {
            apiResponse("1", "success", "200", $result['result']);
        } else {
            apiResponse("0", $result['error_msg'], $result['error_code']);
        }
    }

    public function vehicle_license() //行驶证

    {

        if (!empty($_FILES['file'])) {
            if (!in_array($_FILES['file']['type'], C('IMGTYPE'))) {
                apiResponse("0", "图像格式不支持!");
            }
            $baseimg = base64_encode(file_get_contents($_FILES['file']['tmp_name']));

            $data = array(
                "image" => $baseimg,
            );
        } else if (!empty(I('post.'))) {
            $p = I('post.');
            if (empty($p['image'])) {
                apiResponse("0", "参数有误");
            }
            $data = array(
                "image" => $p['image'],
            );
        } else {
            apiResponse("0", "参数有误");
        }
        $obj = new \Common\Util\EI();
        $re  = $obj->vehicle_license($data);

        $result = json_decode($re, true);

        if (isset($result['result'])) {
            apiResponse("1", "success", "200", $result['result']);
        } else {
            apiResponse("0", $result['error_msg'], $result['error_code']);
        }
    }

    public function mvs_invoice() //机动车销售发票

    {

        if (!empty($_FILES['file'])) {
            if (!in_array($_FILES['file']['type'], C('IMGTYPE'))) {
                apiResponse("0", "图像格式不支持!");
            }
            $baseimg = base64_encode(file_get_contents($_FILES['file']['tmp_name']));

            $data = array(
                "image" => $baseimg,
            );
        } else if (!empty(I('post.'))) {
            $p = I('post.');
            if (empty($p['image'])) {
                apiResponse("0", "参数有误");
            }

            $data = array(
                "image" => $p['image'],
            );
        } else {
            apiResponse("0", "参数有误");
        }
        $obj = new \Common\Util\EI();
        $re  = $obj->mvs_invoice($data);

        $result = json_decode($re, true);

        if (isset($result['result'])) {
            apiResponse("1", "success", "200", $result['result']);
        } else {
            apiResponse("0", $result['error_msg'], $result['error_code']);
        }
    }

    public function general_text() //通用文字

    {

        if (!empty($_FILES['file'])) {
            if (!in_array($_FILES['file']['type'], C('IMGTYPE'))) {
                apiResponse("0", "图像格式不支持!");
            }
            $baseimg = base64_encode(file_get_contents($_FILES['file']['tmp_name']));

            $data = array(
                "image"            => $baseimg,
                "detect_direction" => true, //默认为false,不检查朝向,只支持正常角度的图片识别
            );
        } else if (!empty(I('post.'))) {
            $p = I('post.');
            if (empty($p['image'])) {
                apiResponse("0", "参数有误");
            }
            $data = array(
                "image"            => $p['image'],
                "detect_direction" => true, //默认为false,不检查朝向,只支持正常角度的图片识别
            );
        } else {
            apiResponse("0", "参数有误");
        }
        $obj = new \Common\Util\EI();
        $re  = $obj->general_text($data);

        $result = json_decode($re, true);

        if (isset($result['result'])) {
            $list_Arr = array();
            foreach ($result['result']['words_block_list'] as $k => $v) {
                $list_Arr[] = $v['words'];
            }
            apiResponse("1", "success", "200", $list_Arr);
        } else {
            apiResponse("0", $result['error_msg'], $result['error_code']);
        }
    }
    public function screenshot() //截图文字识别

    {

        if (!empty($_FILES['file'])) {
            if (!in_array($_FILES['file']['type'], C('IMGTYPE'))) {
                apiResponse("0", "图像格式不支持!");
            }
            $baseimg = base64_encode(file_get_contents($_FILES['file']['tmp_name']));

            $data = array(
                "image"            => $baseimg,
                "extract_type"     => "", //默认为false,不检查朝向,只支持正常角度的图片识别
                "detect_direction" => true,
            );
        } else if (!empty(I('post.'))) {
            $p = I('post.');
            if (empty($p['image'])) {
                apiResponse("0", "参数有误");
            }
            $data = array(
                "image"            => $p['image'],
                "extract_type"     => "", //默认为false,不检查朝向,只支持正常角度的图片识别
                "detect_direction" => true,
            );
        } else {
            apiResponse("0", "参数有误");
        }
        $obj = new \Common\Util\EI();
        $re  = $obj->general_text($data);

        $result = json_decode($re, true);

        if (isset($result['result'])) {
            $list_Arr = array();
            foreach ($result['result']['words_block_list'] as $k => $v) {
                $list_Arr[] = $v['words'];
            }
            apiResponse("1", "success", "200", $list_Arr);
        } else {
            apiResponse("0", $result['error_msg'], $result['error_code']);
        }
    }

    public function _empty()
    {
        apiResponse("0", "no action ");
    }

}
