<?php
namespace Thanksgiving\Controller;

/*语音识别接口信息Asr*/

use Think\Controller;

class AsrController extends Controller
{
    public function _initialize()
    {
        header('Access-Control-Allow-Origin:*');
        // $openid = I('post.openid');
        // A('search')->add_api_num(ACTION_NAME, $openid);

    }
    public function app_voice() // 语音助手

    {

        $obj = new \Common\Util\HwAi();
        if (!empty(I('post.text'))) {
            //接收base64

            $header = array(
                "Content-type:application/json",
                "Authorization:token 550e8400-e29b-41d4-a716-44665566acc5",
            );

            $data = json_encode(array('thank' => I('post.text')));
            $res  = $this->_request("http://114.116.88.59:8089/thank", $data, $header);
            $res  = json_decode($res, true);

            $drink = array("可乐", "枸杞茶", "咖啡", "白开水", "朗姆酒", "青草蜢鸡尾酒", "奇异果汁", "香槟", "特基拉日出", "血腥玛丽", "蜂蜜水", "蛋白粉", "芹菜汁", "葡萄酒", "纯牛奶", "玛格丽特", "长岛冰茶", "性感海滩", "干马天尼", "新加坡司令", "威士忌", "柠檬茶", "啤酒", "奶茶", "酸酸乳", "白酒", "乌龙茶", "乳酸菌酸奶", "金秋", "橙汁");

            $result = [];
            if ($res['output']) {
                $result = $res['output'];
            } else {
                $arr = $arr = array(
                    array(
                        "template" => "你是夏日里滋滋冒泡的冰阔落，让人瞬间满血复活。感谢你在工作中也是活力四射的小太阳，不断上升的气泡就是灵感源泉！",
                        "keywords" => array(),
                        "title"    => "可乐",
                        "topic"    => "工作",
                    ),
                    array(
                        "template" => "emmm…或许你不露锋芒，不爱当出头鸟，但生命之源就是你了！感谢你甘愿身在幕后默默付出，别担心，你的好总会被慧眼识别哦！",
                        "keywords" => array(),
                        "title"    => "白开水",
                        "topic"    => "工作",
                    ),
                    array(
                        "template" => "隔着屏幕都嗅到青春荷尔蒙的气息，入口酸甜，夏日必备。感谢你让年轻力量不浪费，活力充沛的状态要保持！",
                        "keywords" => array(),
                        "title"    => "奇异果汁",
                        "topic"    => "学习",
                    ),
                    array(
                        "template" => "又咸又甜，乍看诡异，实际上……感谢你保持着天马行空的想象力，总是不满足于现成，热衷创造，离新生代鬼才就差一步！",
                        "keywords" => array(),
                        "title"    => "白开水",
                        "topic"    => "学习",
                    ),
                );
                $num    = mt_rand(0, 3);
                $result = $arr[$num];
            }
            $id = array_search($result['title'], $drink) ? array_search($result['title'], $drink) : 0;
            $id++;
            $result['title_id'] = $id;

            apiResponse("1", "success", "200", $result);
        } else {
            apiResponse("0", "参数有误");
        }

    }

    public function _request($curl, $data = null, $headers, $https = true, $method = 'post')
    {
        $ch = curl_init(); //初始化
        if (!$headers) {
            $headers = array(
                "Content-type:application/json", "Accept:application/json", "Cache-Control:no-cache", "Pragma:no-cache",
            );
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_URL, $curl); //设置访问的URL
        curl_setopt($ch, CURLOPT_HEADER, false); //设置不需要头信息
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //只获取页面内容，但不输出
        if ($https) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //不做服务器认证
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); //不做客户端认证
        }
        if ($method == 'post') {
            curl_setopt($ch, CURLOPT_POST, true); //设置请求是POST方式
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data); //设置POST请求的数据
        }
        $str = curl_exec($ch); //执行访问，返回结果
        curl_close($ch); //关闭curl，释放资源
        return $str;
    }

    public function upload_voice()
    {
        $media_id     = I('post.media_id');
        $access_token = $this->getAccessToken();

        $date = date('Ymd');
        $path = "Uploads/audio/{$date}/";

        is_dir($path) || mkdir($path, 0777, true);

        //微 信上传下载媒体文件
        $url = "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token={$access_token}&media_id={$media_id}";

        $filename = uniqid() . ".amr";
        $re       = $this->downAndSaveFile($url, $path . $filename);

        $new_file = $path . $filename;

        $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';

        $url = $http_type . $_SERVER['HTTP_HOST'] . __ROOT__ . "/" . $new_file;

        $base_audio = base64_encode(file_get_contents($url));

        $data = array(
            "data"        => $base_audio,
            "encode_type" => "",
            "sample_rate" => "",
        );
        $obj    = new \Common\Util\HwAi();
        $re     = $obj->wxasrs($data);
        $result = json_decode($re, true);

        // var_dump($result);

        if (isset($result['result'])) {

            $header = array(
                "Content-type:application/json",
                "Authorization:token 550e8400-e29b-41d4-a716-44665566acc5",
            );

            $json_data = json_encode(array('thank' => $result['result']['words']));
            $res       = $this->_request("http://114.116.0.245:8089/thank", $json_data, $header);
            $res       = json_decode($res, true);
            // var_dump($res);die;
            if ($res['output']) {
                apiResponse("1", "success", "200", $res['output']);
            } else {
                apiResponse("1", "success", "201", $result['result']);
            }

        } else {
            apiResponse("0", $result['error_msg'], $result['error_code']);
        }

    }
    public function getAccessToken()
    {
        $url = 'http://wx.issmart.com.cn/jssdk/accesstoken.php';

        $token_data = http($url);
        $token_data = json_decode($token_data[1], true);

        if ($token_data) {
            return $token_data['access_token'];
        } else {
            return false;
        }
    }
//根据URL地址，下载文件
    public function downAndSaveFile($url, $savePath)
    {
        ob_start();
        readfile($url);
        $img = ob_get_contents();
        ob_end_clean();
        $size = strlen($img);
        $fp   = fopen($savePath, 'a');
        fwrite($fp, $img);
        fclose($fp);
    }
    public function _empty()
    {
        apiResponse("0", "no action!");
    }

}
