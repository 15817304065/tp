<?php
namespace Common\Util;

class EI
{
    const API = 'http://114.115.142.50:8080/agent';

    /*1.OCR文字识别*///+-------------------------------------------------------------------------------------------
    //1.1 vat_invoice:增值税发票识别
    public function vat_invoice($params)
    {
        if (!self::_is_base64($params['image'])) {
            $params['image'] = base64_encode($params['image']);
        }
        $obj      = json_encode($params);
        $api_url  = self::API . "/v1.0/ocr/vat-invoice";
        $response = $this->_request($api_url, $obj);
        return $response;
    }
    //1.2身份证识别:id_card
    public function id_card($params)
    {
        if (!self::_is_base64($params['image'])) {
            $params['image'] = base64_encode($params['image']);
        }
        $obj     = json_encode($params);
        $api_url = self::API . "/v1.0/ocr/id-card";

        $response = $this->_request($api_url, $obj);

        return $response;
    }
    //1.3表格识别:general_table
    public function general_table($params)
    {
        if (!self::_is_base64($params['image'])) {
            $params['image'] = base64_encode($params['image']);
        }
        $obj      = json_encode($params);
        $api_url  = self::API . "/v1.0/ocr/general-table";
        $response = $this->_request($api_url, $obj);
        return $response;
    }
    //1.4驾驶证识别: driver-license
    public function driver_license($params)
    {
        if (!self::_is_base64($params['image'])) {
            $params['image'] = base64_encode($params['image']);
        }
        $obj      = json_encode($params);
        $api_url  = self::API . "/v1.0/ocr/driver-license";
        $response = $this->_request($api_url, $obj);
        return $response;
    }
    //1.5行驶证识别: vehicle-license
    public function vehicle_license($params)
    {
        if (!self::_is_base64($params['image'])) {
            $params['image'] = base64_encode($params['image']);
        }
        $obj      = json_encode($params);
        $api_url  = self::API . "/v1.0/ocr/vehicle-license";
        $response = $this->_request($api_url, $obj);
        return $response;
    }
    //1.6行驶证识别: mvs_invoice
    public function mvs_invoice($params)
    {
        if (!self::_is_base64($params['image'])) {
            $params['image'] = base64_encode($params['image']);
        }
        $obj      = json_encode($params);
        $api_url  = self::API . "/v1.0/ocr/mvs-invoice";
        $response = $this->_request($api_url, $obj);
        return $response;
    }
    //1.7通用文字识别:general_text
    public function general_text($params)
    {
        if (!self::_is_base64($params['image'])) {
            $params['image'] = base64_encode($params['image']);
        }
        $obj      = json_encode($params);
        $api_url  = self::API . "/v1.0/ocr/general-text";
        $response = $this->_request($api_url, $obj);
        return $response;
    }
    //1.8截图文字识别:screenshot
    public function screenshot($params)
    {
        if (!self::_is_base64($params['image'])) {
            $params['image'] = base64_encode($params['image']);
        }
        $obj      = json_encode($params);
        $api_url  = self::API . "/v1.0/ocr/screenshot";
        $response = $this->_request($api_url, $obj);
        return $response;
    }

    /*2.image图像识别*/// +-------------------------------------------------------------------------------------------

    //2.1图像标签识别:tag_image
    public function tag_image($params)
    {
        if (!self::_is_base64($params['image'])) {
            $params['image'] = base64_encode($params['image']);
        }

        $obj     = json_encode($params);
        $api_url = self::API . "/v1.0/image/tagging";

        $response = $this->_request($api_url, $obj);

        return $response;
    }

    //2.2低光照增强:dark_enhance
    public function dark_enhance($params)
    {
        if (!self::_is_base64($params['image'])) {
            $params['image'] = base64_encode($params['image']);
        }
        $obj      = json_encode($params);
        $api_url  = self::API . "/v1.0/image/dark-enhance";
        $response = $this->_request($api_url, $obj);
        return $response;
    }
    //2.3  图像去雾defog_image
    public function defog_image($params)
    {
        if (!self::_is_base64($params['image'])) {
            $params['image'] = base64_encode($params['image']);
        }
        $obj      = json_encode($params);
        $api_url  = self::API . "/v1.0/image/defog";
        $response = $this->_request($api_url, $obj);
        return $response;
    }
    //6.1  图像搜索search_copyright
    public function search_copyright($params)
    {
        if (!self::_is_base64($params['image'])) {
            $params['image'] = base64_encode($params['image']);
        }
        $obj      = json_encode($params);
        $api_url  = self::API . "/v1.0/image/search-copyright";
        $response = $this->_request($api_url, $obj);
        return $response;
    }
    //6.2  图片素材查找search_material
    public function search_material($params)
    {
        if (!self::_is_base64($params['image'])) {
            $params['image'] = base64_encode($params['image']);
        }
        $obj      = json_encode($params);
        $api_url  = self::API . "/v1.0/image/search-material";
        $response = $this->_request($api_url, $obj);
        return $response;
    }
    //6.3  以图搜图search-commodity
    public function search_commodity($params)
    {
        if (!self::_is_base64($params['image'])) {
            $params['image'] = base64_encode($params['image']);
        }
        $obj      = json_encode($params);
        $api_url  = self::API . "/v1.0/image/search-commodity";
        $response = $this->_request($api_url, $obj);
        return $response;
    }

    /*3.内容检测接口信息*/// +-------------------------------------------------------------------------------------------
    //3.1    清晰度检测clarity_detect
    public function clarity_detect($params)
    {
        if (!self::_is_base64($params['image'])) {
            $params['image'] = base64_encode($params['image']);
        }
        $obj      = json_encode($params);
        $api_url  = self::API . "/v1.0/moderation/image/clarity-detect";
        $response = $this->_request($api_url, $obj);
        return $response;
    }
    //3.2    扭曲校正distortion_correct
    public function distortion_correct($params)
    {
        if (!self::_is_base64($params['image'])) {
            $params['image'] = base64_encode($params['image']);
        }
        $obj      = json_encode($params);
        $api_url  = self::API . "/v1.0/moderation/image/distortion-correct";
        $response = $this->_request($api_url, $obj);
        return $response;
    }
    //3.3  通用服务审核 general_service_review(参数图片或者文本)
    public function general_service_review($params)
    {
        if (!empty($params['image'])) {
            if (!self::_is_base64($params['image'])) {
                $params['image'] = base64_encode($params['image']);
            }
        }
        $obj      = json_encode($params);
        $api_url  = self::API . "/v1.0/moderation/image/general-service-review";
        $response = $this->_request($api_url, $obj);
        return $response;
    }
    //3.4    游戏头像违规game-avatar
    public function game_avatar($params)
    {
        if (!self::_is_base64($params['image'])) {
            $params['image'] = base64_encode($params['image']);
        }
        $obj      = json_encode($params);
        $api_url  = self::API . "/v1.0/moderation/image/game-avatar";
        $response = $this->_request($api_url, $obj);
        return $response;
    }
    //3.5    社交内容审核social_content_review
    public function social_content_review($params)
    {
        if (!self::_is_base64($params['image'])) {
            $params['image'] = base64_encode($params['image']);
        }
        $obj      = json_encode($params);
        $api_url  = self::API . "/v1.0/moderation/image/social-content-review";
        $response = $this->_request($api_url, $obj);
        return $response;
    }
    //3.3  电商评论Ecommerce_revieww
    public function ecommerce_review($params)
    {
        if (!empty($params['image'])) {
            if (!self::_is_base64($params['image'])) {
                $params['image'] = base64_encode($params['image']);
            }
        }

        $obj      = json_encode($params);
        $api_url  = self::API . "/v1.0/moderation/Ecommerce-review";
        $response = $this->_request($api_url, $obj);
        return $response;
    }

    /*4.语音识别接口信息*/// +-------------------------------------------------------------------------------------------
    //4.1    APP语音助手app_voice_assistant
    public function app_voice_assistant($params)
    {

        if (!empty($params['data'])) {
            if (!self::_is_base64($params['data'])) {
                $params['data'] = base64_encode($params['data']);
            }
        }
        $obj      = json_encode($params);
        $api_url  = self::API . "/v1.0/voice/asr/APP-voice-assistant";
        $response = $this->_request($api_url, $obj);
        return $response;
    }
    //4.2    视频字幕video_subtitle
    public function video_subtitle($params)
    {

        if (!empty($params['data'])) {
            if (!self::_is_base64($params['data'])) {
                $params['data'] = base64_encode($params['data']);
            }
        }
        $obj      = json_encode($params);
        $api_url  = self::API . "/v1.0/voice/asr/video-subtitle";
        $response = $this->_request($api_url, $obj);
        return $response;
    }
    //4.3.1    体验中心-   提交任务
    public function long_sentence($params)
    {

        if (!empty($params['data'])) {
            if (!self::_is_base64($params['data'])) {
                $params['data'] = base64_encode($params['data']);
            }
        }
        $obj      = json_encode($params);
        $api_url  = self::API . "/v1.0/voice/asr/long-sentence";
        $response = $this->_request($api_url, $obj);
        return $response;
    }
    //4.3.2    体验中心-  获取识别结果
    public function getLong_sentence($params)
    {
        $http     = http_build_query($params);
        $api_url  = self::API . "/v1.0/voice/asr/getLong-sentence?" . $http;
        $obj      = json_encode($params);
        $response = $this->_request($api_url, array(), "get");
        return $response;
    }

    //4.4    语音合成
    public function tts($params)
    {
        $obj      = json_encode($params);
        $api_url  = self::API . "/v1.0/voice/asr/tts";
        $response = $this->_request($api_url, $obj);
        return $response;
    }

    /*5.人脸识别接口信息*/// +-------------------------------------------------------------------------------------------
    //5.1   客户分析
    public function face_detect($params)
    {
        if (!self::_is_base64($params['image_base64'])) {
            $params['image_base64'] = base64_encode($params['image_base64']);
        }

        $obj     = json_encode($params);
        $api_url = self::API . "/v1.0/frs/face-detect";

        $response = $this->_request($api_url, $obj);

        return $response;
    }
    //5.2   身份验证
    public function face_compare($params)
    {
        // if (!self::_is_base64($params['image_base64'])) {
        //     $params['image_base64'] = base64_encode($params['image_base64']);
        // }
        $obj      = json_encode($params);
        $api_url  = self::API . "/v1.0/frs/face-compare";
        $response = $this->_request($api_url, $obj);
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
     * 获取接口内容及相应headers详情
     * @param string $url 请求的API地址
     * @param array  $post POST所需提交的数据
     * @param array  $headers 请求的header信息
     * @param bool   $https 是否需要服务认证
     * @param string $method 请求方式
     * @return string json
     */
    private function _request($url, $data = null, $method = 'post', $headers = array('Content-type:application/json'), $https = true)
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
