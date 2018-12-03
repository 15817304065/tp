<?php
namespace Home\Controller;

use Think\Controller;

class TengxunController extends Controller
{

    public function index()
    {
        $this->display('index');
    }
    public function test()
    {

        $this->display('index2');
    }

    public function Ocr() //通用图片识别

    {
        if (!empty($_FILES['file'])) {
            //通过uploadfile上传的临时文件
            if (!in_array($_FILES['file']['type'], C('IMGTYPE'))) {
                apiResponse("0", "图像格式不支持!");
            }
            $baseimg = base64_encode(file_get_contents($_FILES['file']['tmp_name']));

            $data = array(
                "image" => $baseimg,
            );
        } else if (!empty(I('post.'))) {
            //接收base64图像数据
            $p    = I('post.');
            $data = array(
                "image" => $p['image'],
            );
        } else {
            apiResponse("0", "参数有误");
        }
        $this->Teng_init();

        $response = \API::generalocr($data);

        $obj = json_decode($response, true);

        if ($obj['msg'] == "ok") {
            $list_str = "";
            $list_Arr = array();
            foreach ($obj['data']['item_list'] as $k => $v) {
                $list_Arr[] = $v['itemstring'];
            }
            apiResponse("1", "success", "200", $list_Arr);
        } else {
            apiResponse("0", $obj['msg']);
        }
    }
    public function translate() //文本翻译

    {
        $this->Teng_init();

        // 文本翻译（AI Lab）
        $params = array(
            'type' => '0',
            'text' => I('post.translate'),
        );
        $response = \API::texttrans($params);
        $obj      = json_decode($response, true);
        if ($obj) {
            $this->ajaxReturn(array('msg' => $obj['msg'], 'state' => 1, 'data' => $obj['data']['trans_text']));
        } else {
            $this->ajaxReturn(array('msg' => 'error', 'state' => 0, 'data' => null));
        }
    }
    public function Asr() //语音识别

    {
        $this->Teng_init();
        $params = array(
            'speech'    => I('post.base_audio'),
            'format'    => 8, //MP3
            'rate'      => 16000, //16KHz
            'bits'      => 16, //16位采样
            'speech_id' => "{$app_id}_" . md5(time()),
        );

        $response = \API::wxasrs($params);
        $obj      = json_decode($response, true);

        if ($obj) {
            $this->ajaxReturn(array('msg' => $obj['msg'], 'state' => 1, 'data' => $obj['data']['speech_text']));
        } else {
            $this->ajaxReturn(array('msg' => 'error', 'state' => 0, 'data' => null));
        }
    }
    public function Tts() //语音合成

    {
        $this->Teng_init();
        $params = array(
            'model_type' => '0',
            'speed'      => '0',
            'text'       => I('post.content'),
        );

        $response = \API::tts($params);
        $obj      = json_decode($response, true);

        if ($obj) {
            $this->ajaxReturn(array('msg' => $obj['msg'], 'state' => 1, 'data' => $obj['data']['voice']));
        } else {
            $this->ajaxReturn(array('msg' => 'error', 'state' => 0, 'data' => null));
        }
    }
    public function checkLabelImage() //图片标签识别

    {
        $this->Teng_init();
        $params = array(
            'image' => I('post.image'),
        );
        $response = \API::labelimages($params);

        $obj = json_decode($response, true);

        $list_str = "";

        foreach ($obj['data']['tag_list'] as $k => $v) {
            $list_str .= '标签' . ($k + 1) . '&nbsp' . $v['tag_name'] . '，置信度&nbsp' . $v['tag_confidence'] . '</br>';
        }
        if ($obj) {
            $this->ajaxReturn(array('msg' => 'success', 'state' => 1, 'data' => $list_str));
        } else {
            $this->ajaxReturn(array('msg' => 'error', 'state' => 0, 'data' => null));
        }
    }
    public function Ocr_Idcard() //通用图片识别

    {
        $this->Teng_init();
        $params = array(
            'image'     => I('post.image'),
            'card_type' => '0',
        );
        $response = \API::Idcardocr($params);

        $obj = json_decode($response, true);
        // var_dump($obj);die;

        if ($obj['msg'] == "ok") {
            foreach ($obj['data'] as $k => $v) {
                if ($k != 'frontimage') {
                    $list_str .= $k . ':' . $v . '</br>';
                }
            }
            $this->ajaxReturn(array('msg' => $obj['msg'], 'state' => 1, 'data' => $list_str));
        } else {
            $this->ajaxReturn(array('msg' => $obj['msg'], 'state' => 0, 'data' => '网络错误!请稍后再试!'));
        }
    }

    public function Teng_init()
    {
        vendor('TengxunAI.include');
        $app_id  = C('TENGXUNAI_CONFIG')['app_id'];
        $app_key = C('TENGXUNAI_CONFIG')['app_key'];

        $obj = \Configer::setAppInfo($app_id, $app_key);
    }

}
