<?php
namespace Home\Controller;

use Think\Controller;

class IndexController extends Controller
{

    public function index()
    {

        $this->display('index');
    }
    public function test()
    {
        $obj   = new \Common\Util\HwAi();
        $token = $obj->token;
        $this->assign('token', $token);
        $this->display('index2');
    }

//图片通用文字识别
    public function Ocr()
    {

        $base_img = I('post.image');
        $data     = array(
            "image"            => $base_img,
            "url"              => "",
            "detect_direction" => false,
        );

        $obj = new \Common\Util\HwAi();

        $re = $obj->generalocr($data);

        $result = json_decode($re, true);

        if (isset($result['result'])) {
            foreach ($result['result']['words_block_list'] as $k => $v) {
                $list_str .= ($k + 1) . '、' . $v['words'] . '</br>';
            }
            $this->ajaxReturn(array('msg' => 'success', 'state' => 1, 'data' => $list_str));
        } else {
            $this->ajaxReturn(array('msg' => 'error', 'state' => 0, 'data' => '网络错误!请稍后再试!'));
        }

    }

    //图片身份证识别
    public function Ocr_Idcard()
    {

        $base_img = I('post.image');
        $data     = array(
            "image" => $base_img,
            "url"   => "",
            // "side"  => 'front',
        );

        $obj = new \Common\Util\HwAi();

        $re = $obj->Idcardocr($data);

        $result = json_decode($re, true);

        if (isset($result['result'])) {
            foreach ($result['result'] as $k => $v) {
                $list_str .= $k . ':' . $v . '</br>';
            }
            $this->ajaxReturn(array('msg' => 'success', 'state' => 1, 'data' => $list_str));
        } else {
            $this->ajaxReturn(array('msg' => 'error', 'state' => 0, 'data' => '网络错误!请稍后再试!'));
        }

    }
    //语音合成
    public function Tts()
    {

        $text = I('post.content');
        $data = array(
            'text'       => $text,
            'voice_name' => 'xiaoyan',
            'volume'     => 0,
        );
        $obj = new \Common\Util\HwAi();

        $re = $obj->voice_speech($data);

        $result = json_decode($re, true);

        if (isset($result['result'])) {

            $this->ajaxReturn(array('msg' => 'success', 'state' => 1, 'data' => $result['result']['data']));
        } else {
            $this->ajaxReturn(array('msg' => 'error', 'state' => 0, 'data' => '网络错误!请稍后再试!'));
        }
    }
    //短语音识别
    public function Asr()
    {
        $data = array(
            'data'        => I('post.base_audio'),
            'encode_type' => "wav", //MP3
            "sample_rate" => "8k",
        );
        $obj = new \Common\Util\HwAi();

        $re = $obj->wxasrs($data);

        $result = json_decode($re, true);

        // var_dump($result);die;

        if (isset($result['result'])) {

            $this->ajaxReturn(array('msg' => 'success', 'state' => 1, 'data' => $result['result']['words']));
        } else {
            $this->ajaxReturn(array('msg' => 'error', 'state' => 0, 'data' => '网络错误!请稍后再试!'));
        }
    }

//违规文本检测
    public function moderation_text()
    {
        $data = array(
            "categories" => array("ad", "abuse", "porn", "contraband", "flood"),
            "items"      => array(
                array(
                    "text" => I('post.content'),
                    "type" => "content",
                ),
            ),
        );
        $obj = new \Common\Util\HwAi();

        $re = $obj->checkText($data);

        $result = json_decode($re, true);

        if (isset($result['result'])) {
            if ($result['result']['suggestion'] == 'pass') {
                $item = '通过';
            } else if ($result['result']['suggestion'] == 'block') {
                $item = '不通过';
            } else {
                $item = '需人工重点检查';
            }
            $this->ajaxReturn(array('msg' => 'success', 'state' => 1, 'data' => $result['result']['suggestion']));
        } else {
            $this->ajaxReturn(array('msg' => 'error', 'state' => 0, 'data' => '网络错误!请稍后再试!'));
        }
    }

    //图片标签识别
    public function checkLabelImage()
    {

        $base_img = I('post.image');
        $data     = array(
            "image"     => $base_img, //与url二选一
            "url"       => "", //与image二选一
            "language"  => 'zh', //语言,可选参数,默认en
            "limit"     => 5, //最多返回标签数,可选,默认-1
            "threshold" => 0, //置信度调值,低于此的结果不返回,可选,默认0
        );

        $obj = new \Common\Util\HwAi();

        $re = $obj->labelimages($data);

        $result = json_decode($re, true);

        if (isset($result['result'])) {
            foreach ($result['result']['tags'] as $k => $v) {
                $list_str .= '标签' . ($k + 1) . '&nbsp' . $v['tag'] . '，置信度&nbsp' . $v['confidence'] . '</br>';
            }
            $this->ajaxReturn(array('msg' => 'success', 'state' => 1, 'data' => $list_str));
        } else {
            $this->ajaxReturn(array('msg' => 'error', 'state' => 0, 'data' => '网络错误!请稍后再试!'));
        }

    }

    //图片类型识别
    public function checkTypeImage()
    {

        $base_img = I('post.image');
        $data     = array(
            "image"      => $base_img, //与url二选一
            "url"        => "", //与image二选一
            "categories" => array('terrorism', 'porn'), //检测场景,默认为空,表示检测politics(政治人物),terrorism(暴恐)
            "threshold"  => '', //置信度调值,低于此的结果不返回,可选,默认0
        );

        $obj = new \Common\Util\HwAi();

        $re = $obj->check_img($data);

        $result = json_decode($re, true);

        if (isset($result['result'])) {
            $this->ajaxReturn(array('msg' => 'success', 'state' => 1, 'data' => $result['result']['suggestion']));
        } else {
            $this->ajaxReturn(array('msg' => 'error', 'state' => 0, 'data' => '网络错误!请稍后再试!'));
        }

    }

    // //超分图像重建
    // public function super_resolution()
    // {

    //     $base_img = I('post.image');
    //     $data     = array(
    //         "image" => $base_img, //与url二选一
    //         "url"   => "", //与image二选一
    //         "scale" => 3, //放大倍数,默认3,只能3或者4
    //         "model" => "ESPCN", //图像超分辨率重建采用的算法模式,默认ESPCN
    //     );

    //     $obj = new \Common\Util\HwAi();

    //     $re = $obj->superimg($data);

    //     $result = json_decode($re, true);
    //     var_dump($result);

    //     if (isset($result['result'])) {

    //         $newfile = 'Uploads/imgtemp/' . date('Y-m-d') . '/' . uniqid() . 'jpg';
    //         $re      = file_put_contents($newfile, base64_decode($newfile));
    //         if ($re) {
    //             $this->ajaxReturn(array('msg' => 'success', 'state' => 1, 'data' => ''));
    //         } else {
    //             $this->ajaxReturn(array('msg' => '文件保存失败', 'state' => 1, 'data' => ''));
    //         }

    //     } else {
    //         $this->ajaxReturn(array('msg' => 'error', 'state' => 0, 'data' => '网络错误!请稍后再试!'));
    //     }

    // }

}
