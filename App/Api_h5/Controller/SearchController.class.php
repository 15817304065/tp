<?php
namespace Api_h5\Controller;

/*api搜索接口frs*/
use Think\Controller;

class SearchController extends Controller
{

    public function keywords() // 体验中心入口

    {
        header('Access-Control-Allow-Origin:*');
        $obj = new \Common\Util\EI();
        if (!empty($_FILES['file'])) {

            //接收音频信息//通过uploadfile上传的临时文件
            if (!in_array($_FILES['file']['type'], C('AUDIOTYPE'))) {
                apiResponse("0", "音频类型有误");
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
                $char   = "。、，！？：；﹑•＂…‘’“”〝〞∕¦‖—　〈〉﹞﹝「」‹›〖〗】【»«』『〕〔》《﹐¸﹕︰﹔！¡？¿﹖﹌﹏﹋＇´ˊˋ―﹫︳︴¯＿￣﹢﹦﹤‐­˜﹟﹩﹠﹪﹡﹨﹍﹉﹎﹊ˇ︵︶︷︸︹︿﹀︺︽︾ˉ﹁﹂﹃﹄︻︼（）";

                $pattern = array(
                    "/[[:punct:]]/i", //英文标点符号
                    '/[' . $char . ']/u', //中文标点符号
                    '/[ ]{2,}/',
                );
                $search = trim(preg_replace($pattern, ' ', $search)); //去掉两边空格和所有标点

                $re = M('api_info_h5')->where('keywords like "%' . $search . '%" or title="' . $search . '"')->select();

                if (empty($re)) {
                    apiResponse("0", "未搜索到结果", "", $search);
                } else {

                    $arr = $this->set_arr($re, $search);

                    apiResponse("1", "success", "200", $arr);
                }

            }
        } else if (!empty(I('post.keywords')) || I('post.keywords') == "0") {
            //接收文本信息
            $search = I('post.keywords');
            $search = urldecode($search);
            $re     = M('api_info_h5')->where('keywords like "%' . $search . '%" or title="' . $search . '"')->select();

            if (empty($re)) {

                apiResponse("0", "未搜索到结果", "", $search);
            } else {

                $arr = $this->set_arr($re, $search);

                apiResponse("1", "success", "200", $arr);
            }
        } else {
            apiResponse("0", "参数有误");
        }
    }

    private function set_arr($arr, $search)
    {
        foreach ($arr as $k => $v) {
            $arrdata = explode("，", $v['scenarios']);
            foreach ($arrdata as $key => $value) {
                if ($value == $search || (strpos($value, $search) !== false)) {
                    unset($arrdata[$key]);
                    $search = $value;
                    break;
                }
            }
            $arr[$k]['scenarios'] = implode($arrdata, '，');
        }
        $arr = array(
            'search' => $search,
            'info'   => $arr,
        );
        return $arr;
    }
    public function add_api_num2($name, $openid = "")
    {

        $api_arr = M('api_info_h5')->where(array('api_name' => $name))->find();

        if (empty($api_arr)) {
            return false;
        } else {
            M('api_info_h5')->where(array('api_name' => $name))->setInc('num');
        }
        if ($openid && $openid != "null") {
            $data['openid']    = $openid;
            $data['api_id']    = $api_arr['id'];
            $data['date_time'] = date('Ymd');
            $re                = M('interface_h5')->where($data)->find();

            if (empty($re)) {
                $data['num']       = 1;
                $data['date_time'] = date('Ymd');
                M('interface_h5')->add($data);
            } else {
                if ($re['num'] < 500) {
                    M('interface_h5')->where($data)->setInc('num');
                } else {
                    apiResponse("0", "当天可调用次数已用完");
                }
            }
        }

    }
    public function add_api_num($name, $openid = "", $max_num = 500)
    {

        $api_arr = M('api_info_h5')->where(array('api_name' => $name))->find();
        if (empty($api_arr)) {
            return false;
        } else {
            M('api_info_h5')->where(array('api_name' => $name))->setInc('num');
        }
        if ($openid && $openid != "null") {
            $re = M('interface_h5')->where(array('openid' => $openid))->find();
            if (empty($re)) {
                $arr = array(
                    array(
                        'api_id'    => $api_arr['id'],
                        'num'       => 1,
                        'date_time' => date('Ymd'),
                    ),
                );
                $jsondata       = json_encode($arr);
                $data['openid'] = $openid;
                $data['info']   = $jsondata;
                M('interface_h5')->add($data);
            } else {
                $info = json_decode($re['info'], true);
                $boll = false;
                foreach ($info as $k => $v) {
                    if ($v['api_id'] == $api_arr['id']) {
                        if ($v['num'] >= $max_num && $v['date_time'] == date('Ymd')) {
                            apiResponse("0", "当天可调用次数已用完");
                        } else {
                            if ($v['date_time'] == date('Ymd')) {
                                $info[$k]['num']++;
                            } else {
                                $info[$k]['num'] = 1;
                            }
                            $info[$k]['date_time'] = date('Ymd');
                        }
                        $boll = true;
                        break;
                    }
                }
                if (!$boll) {
                    $info[] = array(
                        'api_id'    => $api_arr['id'],
                        'num'       => 1,
                        'date_time' => date('Ymd'),
                    );
                }
                $re = M('interface_h5')->where(array('openid' => $openid))->save(array('info' => json_encode($info)));
            }
        }
    }

    public function get_openid()
    {
        $p = I('get.');
        if (empty($p['code'])) {
            apiResponse("0", "参数有误");
        } else {
            $obj    = file_get_contents('https://api.weixin.qq.com/sns/jscode2session?appid=wxcdd674578a1c43d7&secret=dcd287f86b5dc94cb8142f5915120917&js_code=' . $p['code'] . '&grant_type=authorization_code');
            $result = json_decode($obj, true);
            if (isset($result['openid'])) {
                apiResponse("1", "success", "200", $result['openid']);
            } else {
                apiResponse("0", $result['errmsg'], $result['errcode']);
            }
        }
    }

    public function _empty()
    {
        echo json_encode(array('state' => '0', 'msg' => 'no action'));
    }

}
