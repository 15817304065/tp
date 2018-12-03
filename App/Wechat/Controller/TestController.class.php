<?php
namespace Wechat\Controller;

use Think\Controller;

class TestController extends Controller
{
    public function index()
    {
        $wechat_info = $this->wechatoauth2(array("id" => 2,"cache" => "F","scope" => "F"));
        var_dump($wechat_info);
    }
    public function wechatoauth2($config = array("id" => 1,"cache" => "F","scope" => "F"))
    {
        if($config['cache'] == T)
        {
            $wechat_data = $this->getCache($config['id'], $config['scope']);
        }
        else
        {
            $wechat_data = false;
        }

        if(!$wechat_data)
        {
            $url_is_id = ((strpos($_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'], '?') !== false) ? '&' : '?')."is_id=".$config['id'];
            $current_url = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'].$url_is_id;
            $code = I("get.is_code");
            $is_user = "";
            if(!$code)
            {
                if($config['scope'] == "T")
                {
                    $is_user = "&is_user=T";
                }
                $url = "https://wx.issmart.com.cn/web/frame/Wechat/index.php?m=Inter&c=OAuth2&a=code&is_id=".$config['id']."&is_rurl=".urlencode($current_url).$is_user;
                header("location:$url");
                die();
            }
            else
            {
                // 清除url上is_code参数
                $current_url = $this->replace_var($current_url, "is_code");
                $current_url = $this->replace_var($current_url, "is_id");
                echo "<script>history.replaceState(null, document.title, '".$current_url."')</script>";
                $is_id = I("get.is_id");
                $url = "https://wx.issmart.com.cn/web/frame/Wechat/index.php?m=Inter&c=OAuth2&a=getuserinfo&is_id=".$is_id."&is_code=".$code;
                $result = $this->http($url);
                if($result["code"] == 200){
                    $wechat_data = json_decode( $result['data'] );
                    if($wechat_data->error == 0)
                    {
                        $openid = $wechat_data->data->openid;
                        $wechat_data = $wechat_data->data->info;
                        $wechat_data->openid = $openid;
                        $wechat_data->scope = $config['scope'];
                        $this->setCache($is_id, $wechat_data);
                        return $wechat_data;
                    }
                    else
                    {
                        die($wechat_data->error_msg);
                    }
                }
            }
        }
        else
        {
            return $wechat_data;
        }

    }

    public function getCache($id, $scope)
    {
        $cache = json_decode(cookie("wechat_oauth2_$id"));
        if($cache)
        {
            if($cache->scope == $scope)
            {
                return $cache;
            }
            else
                return false;
        }
        else
        {
            return false;
        }
    }

    public function setCache($id = 1, $data=array("a"=>1))
    {
        $cache = json_encode($data);
        $result = cookie("wechat_oauth2_".$id, $cache, 3600 * 24);
        $cache = json_encode(cookie("wechat_oauth2_$id"));
    }

    public function replace_var($url, $string)  
    {  
            while(substr($url,0,1)=="&")  
            {  
                    $url=substr($url,1);  
            }  
            if($url!="")  
            {  
                    $url_array=explode("&",$url);  
                    $new_url="";  
                    $string_len=strlen($string)+1;  
                    $i=0;  
                    while($url_array[$i])  
                    {  
                        if(substr($url_array[$i],0,$string_len)==$string."=")  
                        {  
                                $url_array[$i]=""; 
                        } 
                        else
                        {
                            if($i>0) $url_array[$i]="&".$url_array[$i];  
                        }
                        $new_url=$new_url.$url_array[$i];  
                        $i++;  
                    }  
            }  
            return $new_url;  
    }

    /**
     * 发送get请求
     * @param string $url
     * @return bool|mixed
     */
    public function request_get($url = '')
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

    public function http($url, $method, $postfields = null, $headers = array(), $debug = false)
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
        curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, false);//这个是重点。
 
        $response = curl_exec($ci);
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
        return array("code"=>$http_code, "data"=>$response);
    }
}
