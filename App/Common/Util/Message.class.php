<?php
namespace Common\Util;

class Message
{

    private $appid;
    private $secrect;
    private $accessToken;

    public function __construct($appid = 'wx996bd5d838d5d827', $secrect = 'd3927177ebc315da18681dd9876ed073')
    {
        // var_dump($app_id.'<br>'.$secrect);

        $this->appid   = $appid;
        $this->secrect = $secrect;

        $this->accessToken = $this->getToken2();

    }

    public function checkUser($openid)
    {

        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=' . $this->accessToken . '&openid=' . $openid . '&lang=zh_CN';

        $data = $this->request_get($url);

        $data = json_decode($data, true);

        if (isset($data['subscribe'])) {
            return $data['subscribe'];
        } else {
            return '0';
        }

    }

    public function getUserInfor($openid)
    {

        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=' . $this->accessToken . '&openid=' . $openid . '&lang=zh_CN';

        $data = $this->request_get($url);

        $data = json_decode($data, true);

        return $data;

    }

    /**
     * 发送post请求
     * @param string $url
     * @param string $param
     * @return bool|mixed
     */
    private function request_post($url = '', $param = '')
    {
        if (empty($url) || empty($param)) {
            return false;
        }
        $postUrl  = $url;
        $curlPost = $param;
        $ch       = curl_init(); //初始化curl
        curl_setopt($ch, CURLOPT_URL, $postUrl); //抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0); //设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1); //post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        $data = curl_exec($ch); //运行curl
        curl_close($ch);
        return $data;
    }

    /**
     * 发送get请求
     * @param string $url
     * @return bool|mixed
     */
    private function request_get($url = '')
    {
        if (empty($url)) {
            return false;
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

//获取token
    private function getToken2()
    {
        // $url = "http://isdm-alpha.issmart.com.cn/isdm_wechat_module/accessToken?appId=wx37ee992d9d3a1f7a";

        // $url = C('ASS_HOST');
        $url = 'http://wx.issmart.com.cn/jssdk/accesstoken.php';

        $token_data = json_decode($this->request_get($url), true);

        if ($token_data && $token_data['access_token'] != null) {
            return $token_data['access_token'];
        } else {
            return false;
        }

    }

    /**
     * 发送自定义的模板消息
     * @param $touser
     * @param $template_id
     * @param $url
     * @param $data
     * @param string $topcolor
     * @return bool
     */
    public function doSend($touser, $template_id, $url, $data, $topcolor = '#7B68EE')
    {

        $template = array(
            'touser'      => $touser,
            'template_id' => $template_id,
            'url'         => $url,
            'topcolor'    => $topcolor,
            'data'        => $data,
        );
        $json_template = json_encode($template);
        $url           = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=" . $this->accessToken;
        $dataRes       = $this->request_post($url, urldecode($json_template));
        $dataRes       = json_decode($dataRes);
        if ($dataRes->errcode == 0) {
            return true;
        } else {
            return $dataRes->errcode;
        }
    }

    /**
     * 下载微信服务器的图片到我的服务器
     * @param string $where 下载到本地的地址
     * @param string $media_id  媒体文件上传后，获取时的唯一标识
     */
    public function downloadImg($where, $media_id = '')
    {
        $url = 'http://wx.issmart.com.cn/jssdk/accesstoken.php';
        $re  = $this->http($url);

        if ($re[0] == 200) {
            $res          = json_decode($re[1], true);
            $access_token = $res['access_token'];
        }
        if ($access_token && $media_id) {
            $info_url = 'http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=' . $access_token . '&media_id=' . $media_id;
            $img      = $this->downloadFile($info_url);

            header('Content-Type:image/jpeg');
            var_dump($img);

            $this->saveFile($where, $img['body']);
            return true;
        }
        return false;
    }

    public function http($url, $method = 'GET', $postfields = null, $headers = array(), $debug = false)
    {
        $ci = curl_init();
        /* Curl settings */
        curl_setopt($ci, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ci, CURLOPT_TIMEOUT, 30);
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, true);

        switch ($method) {
            case 'POST':
                curl_setopt($ci, CURLOPT_POST, true);
                if (!empty($postfields)) {
                    curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
                    $this->postdata = $postfields;
                }
                break;
        }
        curl_setopt($ci, CURLOPT_URL, $url);
        curl_setopt($ci, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ci, CURLINFO_HEADER_OUT, true);

        $response  = curl_exec($ci);
        $http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE);

        if ($debug) {
            echo "=====post data======\r\n";
            var_dump($postfields);

            echo '=====info=====' . "\r\n";
            print_r(curl_getinfo($ci));

            echo '=====response=====' . "\r\n";
            print_r($response);
        }
        curl_close($ci);
        return array($http_code, $response);
    }
    // 下载文件
    private function downloadFile($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_NOBODY, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $packahe  = curl_exec($ch);
        $httpinfo = curl_getinfo($ch);
        curl_close($ch);
        $imageAll = array_merge(array('header' => $httpinfo), array('body' => $packahe));
        return $imageAll;
    }
    // 写入下载文件
    private function saveFile($where, $what)
    {

        if ($f = fopen($where, 'w')) {
            if (fwrite($f, $what)) {
                fclose($f);
            }
        }
    }

    /**
     * @param $serverId jssdk文件上传返回的serverId
     * @return string
     */
    public function savePicToServer($serverId, $savePathFile = "")
    {
        $url = 'http://wx.issmart.com.cn/jssdk/accesstoken.php';
        $re  = $this->http($url);

        if ($re[0] == 200) {
            $res          = json_decode($re[1], true);
            $access_token = $res['access_token'];
        }
        // 要存在你服务器哪个位置？
        $targetName = $savePathFile;
        $ch         = curl_init();
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        $fp = fopen($targetName, 'wb');

        curl_setopt($ch, CURLOPT_URL, "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token={$access_token}&media_id={$serverId}");
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_exec($ch);
        curl_close($ch);
        fclose($fp);

        return $this->saveFile($savePathFile, $fp);
        // return true;
    }

    //获取Token
    public function getAccessToken()
    {
        //  access_token 应该全局存储与更新，以下代码以写入到文件中做示例
        $data = json_decode(file_get_contents("./access_token.json"));
        if ($data->expire_time < time()) {
            $appid        = "youappid"; //自己的appid
            $appsecret    = "youappsecret"; //自己的appsecret
            $url          = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$appsecret}";
            $res          = json_decode(httpGet($url));
            $access_token = $res->access_token;
            if ($access_token) {
                $data->expire_time  = time() + 7000;
                $data->access_token = $access_token;
                $fp                 = fopen("./access_token.json", "w");
                fwrite($fp, json_encode($data));
                fclose($fp);
            }
        } else {
            $access_token = $data->access_token;
        }
        return $access_token;
    }

//HTTP get 请求
    public function httpGet($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        curl_setopt($curl, CURLOPT_URL, $url);

        $res = curl_exec($curl);
        curl_close($curl);

        return $res;
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

    public function upload()
    {
        $media_id     = $_POST["media_id"];
        $access_token = getAccessToken();

        $path    = "./weixinrecord/"; //保存路径，相对当前文件的路径
        $outPath = "./php/weixinrecord/"; //输出路径，给show.php 文件用，上一级

        if (!is_dir($path)) {
            mkdir($path);
        }

        //微 信上传下载媒体文件
        $url = "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token={$access_token}&media_id={$media_id}";

        $filename = "wxupload_" . time() . rand(1111, 9999) . ".amr";
        downAndSaveFile($url, $path . "/" . $filename);

        $data["path"] = $outPath . $filename;
        $data["msg"]  = "download record audio success!";
        // $data["url"] = $url;

        echo json_encode($data);
    }

}
