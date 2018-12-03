<?php
namespace Api\Controller;

/*语音识别接口信息Asr*/

use Think\Controller;

class AsrController extends Controller
{
    public function _initialize()
    {
        $openid = I('post.openid');
        A('search')->add_api_num(ACTION_NAME, $openid);

    }
    public function app_voice() // 语音助手

    {

        $obj = new \Common\Util\EI();
        if (!empty($_FILES['file'])) {

            //接收音频信息//通过uploadfile上传的临时文件
            if (!in_array($_FILES['file']['type'], C('AUDIOTYPE'))) {
                // var_dump($_FILES['file']['type']);
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
                apiResponse("0", $result['error_msg'], $result['error_code']);
            } else {
                apiResponse("1", "success", "200", $result['result']['words']);

            }
        } else if (!empty(I('post.text')) || I('post.text') == "0") {
            //接收文本信息
            $p = I('post.');

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
                $voice_name = uniqid();
                $date       = date('Ymd');
                $path       = "Uploads/audio/{$date}/";
                $new_file   = $path . "{$voice_name}.mp3";

                if (!file_exists($path)) {
                    mkdir($path, 0777);
                }
                $res = file_put_contents($new_file, base64_decode($result['result']['data']));
                if ($res) {

                    $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';

                    $url = $http_type . $_SERVER['HTTP_HOST'] . __ROOT__ . "/" . $new_file;
                    apiResponse("1", "success", "200", $url);
                } else {
                    apiResponse("0", "数据保存失败");
                }

            } else {
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
                apiResponse("0", $result['error_msg'], $result['error_code']);
            } else {
                $search = $result['result']['words'];
                $re     = M('api_info')->field('id,title,keywords,num')->where('keywords like "%' . $search . '%"')->select();
                if (empty($re)) {
                    apiResponse("0", "未搜索到结果");
                } else {
                    apiResponse("1", "success", "200", $re);
                }
            }
        } else if (!empty(I('post.text'))) {
            //接收文本信息
            $search = I('post.text');
            $re     = M('api_info')->field('id,title,num')->where('keywords like "%' . $search . '%"')->select();

            if (empty($re)) {
                apiResponse("0", "未搜索到结果");
            } else {
                apiResponse("1", "success", "200", $re);
            }
        } else {
            apiResponse("0", "参数有误");
        }

    }

    public function video_voice() //  视频字幕

    {

        if (!empty($_FILES['file'])) {
            //通过uploadfile上传的临时文件
            if (!in_array($_FILES['file']['type'], C('AUDIOTYPE'))) {
                apiResponse("0", "音频格式不支持!");
            }
            $base_audio = base64_encode(file_get_contents($_FILES['file']['tmp_name']));

            $data = array(
                "data" => $base_audio,
            );
        } else if (!empty(I('post.'))) {
            //接收base64图像数据

            $p = I('post.');

            if (empty($p['data'])) {
                apiResponse("0", "参数有误");
            }
            $base_audio = base64_encode(file_get_contents($p['data']));
            $data       = array(
                "data" => $base_audio,
            );
        } else {
            apiResponse("0", "参数有误");
        }
        $obj = new \Common\Util\EI();

        $re = $obj->long_sentence($data);

        $result = json_decode($re, true);

        if (isset($result['result'])) {
            $datas = array(
                'job_id' => "e5974add-49de-45d3-bbf3-82c75fc72d12",
                'format' => 2,
            );
            $res = $obj->getLong_sentence($datas);

            $res = json_decode($res, true);

            if (isset($res['result'])) {

                apiResponse("1", "success", "200", $res['result']['words']);
            } else {
                apiResponse("0", $result['error_msg'], $res['error_code']);
            }

        } else {
            apiResponse("0", $result['error_msg'], $result['error_code']);
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

        $data = json_decode(file_get_contents($jsonurl));

        if ($data->expire_time < time() || !$data) {

            $audio_url  = 'https://wx.issmart.com.cn/web/test/lcl/huaweiEI/images/video.mp3';
            $base_audio = base64_encode(file_get_contents($audio_url));
            $res_data   = array(
                "data" => $base_audio,
            );

            $obj = new \Common\Util\EI();

            $re = $obj->long_sentence($res_data);

            $result = json_decode($re, true);

            $job_id = $result['result']['job_id'];

            if (!$job_id) {
                $job_id = $data->job_id;
            } else {

                $data->expire_time = time() + 3600 * 24;
                $data->job_id      = $job_id;

                $res = json_encode($data);

                $re = file_put_contents($jsonurl, $res);

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
