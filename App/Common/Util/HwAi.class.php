<?php
namespace Common\Util;

class HwAi
{
    const API = 'https://ais.cn-north-1.myhuaweicloud.com';

    public $token     = "";
    private $app_name = "";
    private $app_pw   = "";

    public function __construct()
    {
        $this->app_name = C('HUAWEIAI_CONFIG')['app_name'];
        $this->app_pw   = C('HUAWEIAI_CONFIG')['app_pw'];

        $this->token = $this->getToken($this->app_name, $this->app_pw);
    }

    // generalocr ：调用通用OCR识别接口
    // 参数说明
    //   - $params：image-待识别图片。（详见http://ai.qq.com/doc/ocrgeneralocr.shtml）
    // 返回数据
    //   - $response: ret-返回码；msg-返回信息；data-返回数据（调用成功时返回）；http_code-Http状态码（Http请求失败时返回）
    public function generalocr($params)
    {
        if (!self::_is_base64($params['image'])) {
            $params['image'] = base64_encode($params['image']);
        }
        $obj = json_encode($params);

        $url = self::API . '/v1.0/ocr/general-text';

        $header = array(
            "Content-type:application/json",
            "X-Auth-Token:{$this->token}",
        );
        $response = $this->_request($url, $obj, $header);

        return $response;
    }
    // 调用身份证识别接口
    public function Idcardocr($params)
    {
        if (!self::_is_base64($params['image'])) {
            $params['image'] = base64_encode($params['image']);
        }
        $obj = json_encode($params);

        $url = self::API . '/v1.0/ocr/id-card';

        $header = array(
            "Content-type:application/json",
            "X-Auth-Token:{$this->token}",
        );
        $response = $this->_request($url, $obj, $header);

        return $response;
    }

    // 语音合成接口
    public function voice_speech($params)
    {
        $obj = json_encode($params);

        $url = self::API . '/v1.0/voice/tts';

        $header = array(
            "Content-type:application/json",
            "X-Auth-Token:{$this->token}",
        );
        $response = $this->_request($url, $obj, $header);

        return $response;
    }

//语音识别接口
    public function wxasrs($params)
    {

        if (!self::_is_base64($params['data'])) {
            $params['data'] = base64_encode($params['data']);
        }
        $obj = json_encode($params);

        $url = self::API . '/v1.0/voice/asr/sentence';

        $header = array(
            "Content-type:application/json",
            "X-Auth-Token:{$this->token}",
        );
        $response = $this->_request($url, $obj, $header);

        return $response;
    }

    //文本内容检测
    public function checkText($params)
    {
        $obj = json_encode($params);

        $url = self::API . '/v1.0/moderation/text';

        $header = array(
            "Content-type:application/json",
            "X-Auth-Token:{$this->token}",
        );
        $response = $this->_request($url, $obj, $header);

        return $response;
    }

    //label_images 标签图片识别
    public function labelimages($params)
    {
        if (!self::_is_base64($params['image'])) {
            $params['image'] = base64_encode($params['image']);
        }
        $obj = json_encode($params);

        $url = self::API . '/v1.0/image/tagging';

        $header = array(
            "Content-type:application/json",
            "X-Auth-Token:$this->token",
        );

        $response = $this->_request($url, $obj, $header);

        return $response;
    }

    //超分图像重建
    public function superimg($params)
    {
        if (!self::_is_base64($params['image'])) {
            $params['image'] = base64_encode($params['image']);
        }
        $obj = json_encode($params);

        $url = self::API . '/v1.0/vision/super-resolution';

        $header = array(
            "Content-type:application/json",
            "X-Auth-Token:$this->token",
        );

        $response = $this->_request($url, $obj, $header);

        return $response;
    }

    //智能鉴图,分析并识别用户上传的图像内容是否有敏感内容（如涉及政治人物、暴恐元素、涉黄内容等），并将识别结果返回给用户。

    public function check_img($params)
    {
        if (!self::_is_base64($params['image'])) {
            $params['image'] = base64_encode($params['image']);
        }
        $obj = json_encode($params);

        $url = self::API . '/v1.0/moderation/image';

        $header = array(
            "Content-type:application/json",
            "X-Auth-Token:$this->token",
        );

        $response = $this->_request($url, $obj, $header);

        return $response;
    }

    // _is_base64 ：判断一个字符串是否经过base64
    // 参数说明
    //   - $str：待判断的字符串
    // 返回数据
    //   - 该字符串是否经过base64（true/false）
    private static function _is_base64($str)
    {
        return $str == base64_encode(base64_decode($str)) ? true : false;
    }

    /**
     * @param $app_name
     * @param $app_pw
     * @return mixed
     * 获取token
     */
    public function getToken($app_name, $app_pw)
    {
        // access_token 应该全局存储与更新，以下代码以写入到文件中做示例
        $jsonurl = 'Uploads/token/token.json';

        $data = json_decode(file_get_contents($jsonurl));

        if ($data->expire_time < time() || !$data) {

            $api_url = 'https://iam.cn-north-1.myhuaweicloud.com/v3/auth/tokens';
            $config  = array(
                'auth' => array(
                    'identity' => array(
                        'methods'  => array('password'),
                        'password' => array(
                            'user' => array(
                                'name'     => $app_name,
                                'password' => $app_pw,
                                'domain'   => array(
                                    'name' => $app_name,
                                ),
                            ),
                        ),
                    ),
                    'scope'    => array(
                        'project' => array(
                            'name' => "cn-north-1",
                        ),
                    ),
                ),
            );

            $result = $this->getApiContents($api_url, json_encode($config));

            $token = $result['header']['X-Subject-Token'];

            if (!$token) {
                $token = $data->token;
            } else {

                $data->expire_time = time() + 7000;
                $data->token       = $token;

                $res = json_encode($data);

                $re = file_put_contents($jsonurl, $res);

            }
        } else {
            $token = $data->token;
        }

        return $token;
    }

    /**
     * 获取接口内容及相应headers详情
     * @param string $url 请求的API地址
     * @param array  $post POST所需提交的数据
     * @param string $token 验证的TOKEN，放header里
     * @param bool   $returnHeader 是否需要查看response header内容
     * @return array
     */
    public function getApiContents($url, $post, $token = '', $returnHeader = true)
    {
        // 初始化 cURL 会话
        $curl = curl_init();

        // curl_setopt — 设置 cURL 传输选项
        curl_setopt($curl, CURLOPT_URL, $url); // 需要获取的 URL 地址，也可以在curl_init() 初始化会话的时候。

        //curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)');// 在HTTP请求中包含一个"User-Agent: "头的字符串。我觉得没啥用就注释了。付个链接供大家了解下User-Agent [用户代理-百度百科](https://baike.baidu.com/item/%E7%94%A8%E6%88%B7%E4%BB%A3%E7%90%86/1471005?fr=aladdin&fromid=10574244&fromtitle=user-agent)

        //  CURLOPT_FOLLOWLOCATION TRUE 时将会根据服务器返回 HTTP 头中的 "Location: " 重定向。（注意：这是递归的，"Location: " 发送几次就重定向几次，除非设置了 CURLOPT_MAXREDIRS，限制最大重定向次数。）。
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);

        //  TRUE 时将根据 Location: 重定向时，自动设置 header 中的Referer:信息。
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);

        //在HTTP请求头中"Referer: "的内容。
        //curl_setopt($curl, CURLOPT_REFERER, "http://XXX");

        // 如果有数据需要存 改 一般使用POST，所以通过这种方式判断是否是POST传输
        if (count($post) > 0) {
            //  CURLOPT_POST TRUE 时会发送 POST 请求，类型为：application/x-www-form-urlencoded，是 HTML 表单提交时最常见的一种。
            curl_setopt($curl, CURLOPT_POST, 1);

            // 如果value是一个数组，Content-Type头将会被设置成multipart/form-data。由于这个影响了接收，我把数组转了下，http_build_query:生成 URL-encode 之后的请求字符串
            curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
        }

        // 我的API请求规则是，已登录会在response header里返回个token，在其他请求的时候需要带上这个token
        if (!empty($token)) {
            $header = ['token: ' . $token]; //设置一个你的浏览器的header
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        }

        /* 如果不设置这个则无法获取response header内容 */
        curl_setopt($curl, CURLOPT_HEADER, $returnHeader);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        // 执行 cURL 会话
        $data     = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if (curl_errno($curl)) {
            return curl_error($curl);
        }
        // 关闭 cURL 会话
        curl_close($curl);

        $info['code'] = $httpCode;
        if ($returnHeader) {
            list($header, $body) = explode("\r\n\r\n", $data, 2);
            /* 本来只打算要取个token的，后来觉得还是把所有的都接收下好一点，也许后来会有新的需要呢 */
            //preg_match("/token\:(.*?)\n/", $header, $matches);
            //$info['token']  = trim($matches[1]);

            $headers  = explode("\r\n", $header);
            $headList = array();
            foreach ($headers as $head) {
                $value               = explode(':', $head);
                $headList[$value[0]] = $value[1];
            }

            $info['header']  = $headList;
            $info['content'] = $body;
        } else {
            $info['content'] = $data;
        }

        return $info;
    }

    /**
     * 获取接口内容及相应headers详情
     * @param string $url 请求的API地址
     * @param array  $post POST所需提交的数据
     * @param array  $headers 请求的header信息
     * @param bool   $https 是否需要服务认证
     * @param string $method 请求方式
     * @return string json
     */
    public function _request($url, $data = null, $headers = array('Content-type:application/json'), $https = true, $method = 'post')
    {
        $ch = curl_init(); //初始化

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_URL, $url); //设置访问的URL
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
}
