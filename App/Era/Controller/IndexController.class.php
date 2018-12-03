<?php
namespace Era\Controller;

use Think\Controller;

class IndexController extends Controller
{

    public function index()
    {
        header('Access-Control-Allow-Origin:*');
        $openid = I('post.openid');
        $pid    = I('post.pid') ? I('post.pid') : 0;

        if (!$openid || !$this->checkOpenid($openid)) {
            apiRes('0', 'openid有误');
        }

        $re = M('user')->where(array('openid' => $openid))->find();
        if (!$re) {
            $data['openid']     = I('post.openid');
            $data['nickname']   = I('post.nickname');
            $data['imgurl']     = I('post.headimgurl');
            $data['createtime'] = date('Y-m-d H:i:s');
            $uid                = M('user')->add($data);
        } else {
            $uid = $re['id'];
        }

        $arr = M('user u')->join('__PRODUCT__ p ON u.id = p.uid')->field('u.id as uid,u.openid,u.nickname,u.imgurl,u.createtime,p.id as pid,p.img,p.savetime')->select(); //作品数组

        foreach ($arr as $k => $v) {
            $arr[$k]['vote'] = M('vote')->where(array('pid' => $v['pid']))->count();
            $arr[$k]['per']  = round((M('vote')->where(array('pid' => $v['pid']))->count() / M('vote')->count()) * 100, 2) . "%";
        }
        $allData = $this->setData($arr, 'vote', SORT_DESC, 0);
        $_self   = [];
        $p       = 0;
        $count   = M('user')->count();
        foreach ($allData as $key => $value) {
            if ($value['openid'] == $openid) {
                $p     = $key + 1;
                $_self = $value; //当前用户的数据信息
                break;
            }
        }
        if ($p == 0) {
            $over = 0;
            $all  = M('user')->select();
            foreach ($all as $key => $value) {
                if ($value['openid'] == $openid) {
                    $p = $key + 1;
                    break;
                }
            }
        } else {
            $over = (1 - round($p / $count, 4)) * 100 . '%'; //超过%多少的人
        }

        if ($pid != 0) {
            $prr = M('product')->where('id=' . $pid)->find();

            $pur   = M('product')->where('uid=' . $uid)->find();
            $puser = [];
            $puser = M('user u')->join('__PRODUCT__ p ON u.id = p.uid')->field('u.id as uid,u.openid,u.nickname,u.imgurl,p.id as pid,p.img,p.savetime')->where('p.id=' . $pid)->find();

            if (!$prr || !$puser) {
                apiRes("1", "pid有误", "000");
            }
            $puser['vote'] = M('vote')->where(array('pid' => $pid))->count();
            $puser['per']  = round((M('vote')->where(array('pid' => $pid))->count() / M('vote')->count()) * 100, 2) . "%";

            if ($prr == $pur) {
                apiRes("1", "不能给自己投票", '002', array('puser' => $puser, 'self' => $_self, 'over' => $over, 'rank' => $p));
            }

            $uu = M('vote')->where(array('uid' => $uid, 'pid' => $pid, 'votetime' => date('Ymd')))->find();

            if (!$uu) {
                apiRes("1", "可以投票", '001', array('puser' => $puser, 'self' => $_self, 'over' => $over, 'rank' => $p));
            } else {
                apiRes("1", "对当前作品投票机会已用完", '003', array('puser' => $puser, 'self' => $_self, 'over' => $over, 'rank' => $p));
            }
        }

        if (!$_self) {
            apiRes("1", "无pid,初次进入", '000', array('puser' => [], 'self' => $_self, 'over' => "0%", 'rank' => $p));
        } else {
            apiRes("1", "无pid,已有自己作品", '004', array('puser' => [], 'self' => $_self, 'over' => $over, 'rank' => $p));
        }

    }

    public function upload()
    {
        header('Access-Control-Allow-Origin:*');

        if (!empty($_FILES['image'])) {
            $base64_img = base64_encode(file_get_contents($_FILES['image']['tmp_name']));
        } else if (!empty(I('post.image'))) {
            $base64_img = I('post.image'); //接收base64图像数据
        }
        $openid = I('post.openid');
        if (!$openid || !$base64_img) {
            apiRes('0', '参数不对');
        }
        $uid = M('user')->where(array('openid' => $openid))->find()['id'];
        if (!$uid) {
            apiRes('0', 'openid有误');
        }
        $img_path = $this->return_img_url($base64_img);

        if (!$img_path) {
            apiRes("0", "数据保存失败");
        }
        $p = M('product')->where(array('uid' => $uid))->find();
        if (empty($p)) {
            $res = M('product')->add(array('uid' => $uid, 'img' => $img_path, 'savetime' => date('Y-m-d H:i:s')));
            $id  = $res;
        } else {
            $res = M('product')->where(array('uid' => $uid))->save(array('img' => $img_path, 'savetime' => date('Y-m-d H:i:s')));
            $id  = $p['id'];
        }

        if ($res) {
            apiRes("1", "success", 200, $id);
        } else {
            apiRes("0", "数据更新出错");
        }

    }
    public function canVote()
    {
        header('Access-Control-Allow-Origin:*');
        $openid = I('post.openid');
        $pid    = I('post.pid');
        if (!$openid || !$pid) {
            apiRes('0', '参数不对');
        }
        if (!$this->checkOpenid($openid)) {
            apiRes('0', 'openid有误');
        }
        $p = M('product')->where(array('id' => $pid))->find();
        if (!$p) {
            apiRes('0', '作品id有误');
        }
        $re = M('user')->where(array('openid' => $openid))->find();
        if (!$re) {
            apiRes("1", "可以投票");
        } else {
            $uid = $re['id'];
            $uu  = M('vote')->where(array('uid' => $uid, 'pid' => $pid, 'votetime' => date('Ymd')))->find();

            if ($uu) {
                apiRes("0", "当天对该作品已投票!");
            } else {
                apiRes("1", "可以投票");
            }
        }
    }
    public function vote() //投票

    {
        header('Access-Control-Allow-Origin:*');
        $openid = I('post.openid');
        $pid    = I('post.pid');
        if (!$openid || !$pid) {
            apiRes('0', '参数不对');
        }
        if (!$this->checkOpenid($openid)) {
            apiRes('0', 'openid有误');
        }
        $p = M('product')->where(array('id' => $pid))->find();
        if (!$p) {
            apiRes('0', '作品id有误');
        }

        $re = M('user')->where(array('openid' => $openid))->find();
        if (!$re) {

            $re1 = $uid = M('user')->add(array('openid' => $openid, 'createtime' => date('Y-m-d H:i:s')));
            $re2 = M('vote')->add(array('pid' => $pid, 'uid' => $uid, 'votetime' => date('Ymd')));

            apiRes("1", "投票成功");
        } else {
            $uid = $re['id'];
            $uu  = M('vote')->where(array('uid' => $uid, 'pid' => $pid, 'votetime' => date('Ymd')))->find();
            if ($uu) {
                apiRes("0", "每天对一个作品最多只能投一票");
            } else {
                M('vote')->add(array('pid' => $pid, 'uid' => $uid, 'votetime' => date('Ymd')));
                apiRes("1", "投票成功");
            }
        }
    }

    public function ph() //排行榜

    {
        header('Access-Control-Allow-Origin:*');
        $openid = I('post.openid');
        $arr    = M('user u')->join('__PRODUCT__ p ON u.id = p.uid')->field('u.id as uid,u.openid,u.nickname,u.imgurl,u.createtime,p.id as pid,p.img,p.savetime')->select(); //作品数组

        foreach ($arr as $k => $v) {
            $arr[$k]['vote'] = M('vote')->where(array('pid' => $v['pid']))->count();
            $arr[$k]['per']  = round((M('vote')->where(array('pid' => $v['pid']))->count() / M('vote')->count()) * 100, 2) . "%";
        }

        $data = $this->setData($arr, 'vote', SORT_DESC, 20);

        var_export($data);die;

        $allData = $this->setData($arr, 'vote', SORT_DESC, 0);
        $_self   = array();
        $p       = 0;
        $count   = M('user')->count();
        foreach ($allData as $key => $value) {
            if ($value['openid'] == $openid) {
                $_self = $value; //当前用户的数据信息
                $p     = $key + 1;
                break;
            }
        }
        if ($p == 0) {
            $all = M('user')->select();
            foreach ($all as $key => $value) {
                if ($value['openid'] == $openid) {
                    $p = $key + 1;
                    break;
                }
            }
            $over = 0;
        } else {
            $over = (1 - round($p / $count, 4)) * 100 . '%'; //超过%多少的人
        }

        apiRes("1", "success", 200, array('ph' => $data, 'self' => $_self, 'over' => $over, 'rank' => $p));
    }

    public function setData($data, $column, $_sort = SORT_DESC, $limit = 3) //将二维数组按其中某个键进行排序,并返回前N条记录

    {
        $arr = array_column($data, $column);

        array_multisort($arr, $_sort, $data);

        if ($limit != 0) {
            $data = array_slice($data, 0, $limit);
        }

        return $data;
    }

    public function checkOpenid($openid = "ovusAj6QC1ZGweztau_HsBZKx56w")
    {
        $re           = file_get_contents('http://wx.issmart.com.cn/jssdk/accesstoken.php');
        $access_token = json_decode($re, true)['access_token'];
        if (!$access_token) {
            apiRes("0", "网络出错");
        }
        $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=" . $access_token . "&openid=" . $openid . "&lang=zh_CN";
        $res = file_get_contents($url);

        $result = json_decode($res, true);
        if (isset($result['errcode'])) {
            return false; //暂时不做限制
        }
        return true;

    }

    public function expUser()
    {
//以csv格式导出当前站点的筛选数据至Excel
        $actName = '用户信息表';

        $arr = M('user u')->join('__PRODUCT__ p ON u.id = p.uid')->field('u.id as uid,u.openid,u.nickname,u.imgurl,u.createtime,p.id as pid,p.img,p.savetime')->select(); //作品数组

        foreach ($arr as $k => $v) {
            $arr[$k]['vote'] = M('vote')->where(array('pid' => $v['pid']))->count();
            $arr[$k]['per']  = round((M('vote')->where(array('pid' => $v['pid']))->count() / M('vote')->count()) * 100, 2) . "%";
        }

        $data = $this->setData($arr, 'vote', SORT_DESC, 20000);

        $list = $data;

        $filename = $actName . date('YmdHis') . '.csv';
        $exceler  = new \Common\Util\ExcelExport();
        $exceler->charset('UTF-8');
        // 生成excel格式 这里根据后缀名不同而生成不同的格式。jason_excel.csv
        $exceler->setFileName($filename);
        // 设置excel标题行

        foreach ($list[0] as $k => $v) {
            $excel_title[] = $k;
        }
        // 设置excel内容
        $excel_data = array();
        foreach ($list as $key => $value) {
            foreach ($value as $a => $b) {
                $excel_data[$key][$a] = $b;
            }
        }

        $exceler->setTitle($excel_title);
        $exceler->setContent($excel_data);
        // // 生成excel
        $exceler->export();
    }
    public function return_img_url($base64)
    {
        $img_name = uniqid();
        $date     = date('Ymd');
        $path     = "Uploads/era/{$date}/";
        $new_file = $path . "{$img_name}.jpg";

        is_dir($path) || mkdir($path, 0777, true);

        if (base64_encode(base64_decode($base64))) {
            $result = file_put_contents($new_file, base64_decode($base64));
        } else {
            $result = file_put_contents($new_file, $base64);
        }

        if ($result) {
            $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
            $url       = $http_type . $_SERVER['HTTP_HOST'] . __ROOT__ . "/" . $new_file;
            return $url;
        }
        return false;

    }

    public function _empty()
    {
        apiRes("0", "no action ");
    }

}
