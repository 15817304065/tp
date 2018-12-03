<?php
namespace Api_pc\Controller;

/*语音识别接口信息Asr*/

use Think\Controller;

class AsrController extends Controller
{
    public $ismobile = false;
    public function _initialize()
    {
        header('Access-Control-Allow-Origin:*');
        $openid         = I('post.openid') ? I('post.openid') : I('post.userip');
        $this->ismobile = I('post.ismobile') ? true : false;
        A('search')->add_api_num(ACTION_NAME, $openid);

    }
    public function app_voice() // 语音助手

    {
        $obj = new \Common\Util\EI();
        if (!empty($_FILES['file'])) {
            //接收音频信息//通过uploadfile上传的临时文件
            if (!in_array($_FILES['file']['type'], C('AUDIOTYPE'))) {
                apiResponse("0", "音频格式不支持!");
            }
            $base_audio = base64_encode(file_get_contents($_FILES['file']['tmp_name']));
            $data       = array(
                "data"        => $base_audio,
                "encode_type" => "",
                "sample_rate" => "",
            );
            $re     = $obj->app_voice_assistant($data);
            $result = json_decode($re, true);
            if (!isset($result['result'])) {
                A('search')->add_errorcode($this->ismobile, ACTION_NAME, $result['error_msg'], $result['error_code']);
                apiResponse("0", $result['error_msg'], $result['error_code']);
            } else {
                apiResponse("1", "success", "200", $result['result']['words']);

            }
        } else if (!empty(I('post.data'))) {
            //接收base64音频信息
            $data = array(
                "data"        => I('post.data'),
                "encode_type" => "",
                "sample_rate" => "",
            );
            $re     = $obj->app_voice_assistant($data);
            $result = json_decode($re, true);
            if (!isset($result['result'])) {
                A('search')->add_errorcode($this->ismobile, ACTION_NAME, $result['error_msg'], $result['error_code']);
                apiResponse("0", $result['error_msg'], $result['error_code']);
            } else {
                apiResponse("1", "success", "200", $result['result']['words']);
            }
        } else if (!empty(I('post.text')) || I('post.text') == "0") {
            //接收文本信息
            $p    = I('post.');
            $data = array(
                "text"         => $p['text'],
                "voice_name"   => "xiaoyan",
                "volume"       => "0",
                "sample_rate"  => "16k",
                "speech_speed" => "0",
                "pitch_rate"   => "0",
            );
            $re     = $obj->tts($data);
            $result = json_decode($re, true);
            if (isset($result['result'])) {
                apiResponse("1", "success", "200", $result['result']['data']);
            } else {
                A('search')->add_errorcode($this->ismobile, ACTION_NAME, $result['error_msg'], $result['error_code']);
                apiResponse("0", $result['error_msg'], $result['error_code']);
            }
        } else {
            apiResponse("0", "参数有误");
        }
    }

    public function experience_center() //  体验中心入口

    {
        $obj = new \Common\Util\EI();
        if (!empty($_FILES['file'])) {

            //接收音频信息//通过uploadfile上传的临时文件
            if (!in_array($_FILES['file']['type'], C('AUDIOTYPE'))) {
                apiResponse("0", "音频格式不支持!");
            }
            $base_audio = base64_encode(file_get_contents($_FILES['file']['tmp_name']));

            $data = array(
                "data"        => $base_audio,
                "encode_type" => "",
                "sample_rate" => "",
            );
            $re = $obj->app_voice_assistant($data);

            $result = json_decode($re, true);

            if (!isset($result['result'])) {
                A('search')->add_errorcode($this->ismobile, ACTION_NAME, $result['error_msg'], $result['error_code']);
                apiResponse("0", $result['error_msg'], $result['error_code']);
            } else {
                $search = $result['result']['words'];
                $re     = M('api_info_h5')->field('id,title,keywords,num')->where('keywords like "%' . $search . '%"')->select();
                if (empty($re)) {
                    apiResponse("0", "未搜索到结果");
                } else {
                    apiResponse("1", "success", "200", $re);
                }
            }
        } else if (!empty(I('post.text'))) {
            //接收文本信息
            $search = I('post.text');
            $re     = M('api_info_h5')->field('id,title,num')->where('keywords like "%' . $search . '%"')->select();

            if (empty($re)) {
                apiResponse("0", "未搜索到结果");
            } else {
                apiResponse("1", "success", "200", $re);
            }
        } else {
            apiResponse("0", "参数有误");
        }

    }

    public function video_subtitle() // 视频字幕

    {
        $job_id = $this->get_job_id();

        if (!empty(I('post.'))) {
            $datas = array(
                'job_id' => $job_id,
                'format' => 2,
            );
            $obj = new \Common\Util\EI();
            $res = $obj->getLong_sentence($datas);

            $res = json_decode($res, true);

            if (isset($res['result'])) {
                $arr = explode("\n", $res['result']['words']);
                apiResponse("1", "success", "200", $arr);
            } else {
                apiResponse("0", $result['error_msg'], $res['error_code']);
            }
        } else {
            apiResponse("0", "参数有误");
        }
    }

    public function get_job_id()
    {
        $jsonurl = 'Uploads/token/job_id.json';
        $data    = json_decode(file_get_contents($jsonurl));
        if ($data->expire_time < time() || !$data) {
            $audio_url  = 'https://wx.issmart.com.cn/web/test/lcl/huaweiEI/images/video.mp3';
            $base_audio = base64_encode(file_get_contents($audio_url));
            $res_data   = array(
                "data" => $base_audio,
            );
            $obj    = new \Common\Util\EI();
            $re     = $obj->long_sentence($res_data);
            $result = json_decode($re, true);
            $job_id = $result['result']['job_id'];
            if (!$job_id) {
                $job_id = $data->job_id;
            } else {
                $data->expire_time = time() + 3600 * 24;
                $data->job_id      = $job_id;
                $res               = json_encode($data);
                $re                = file_put_contents($jsonurl, $res);
            }
        } else {
            $job_id = $data->job_id;
        }
        return $job_id;
    }
    public function _empty()
    {
        apiResponse("0", "no action!");
    }

}
