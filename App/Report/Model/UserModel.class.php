<?php
namespace Stra\Model;

use Think\Model;

class UserModel extends Model
{

    public function checkUser($openid, $actid)
    {

        $data = M('user')->where('openid="' . $openid . '" and actid=' . $actid)->find(); //查看该用户是否已注册

        if (empty($data['mobile'])) {
            return false;
        } else {
            return $data;
        }

    }

    public function checkLuckUser($openid, $actid = '') //查看抽奖数据库是否有该用户信息

    {

        $user = M('User', 'luckdraw_', 'mysql://root:Huawei$123#_@117.78.47.100:3306/event#utf8');

        $data = $user->where('openid="' . $openid . '" and actid=' . $actid)->find();

        if (empty($data)) {
            return true;
        } else {
            return false;
        }

    }

    public function ISDMInfo($openid)
    {
        $t_events = M('', '', 'mysql://root:Issmart@123@117.78.43.81:13306/isdm#utf8');
        $info     = $t_events->query("select * from tb_cm_member_wechat, tb_cm_member where tb_cm_member_wechat.member_id = tb_cm_member.id and tb_cm_member_wechat.open_id = '" . $openid . "' and tb_cm_member.member_name is not null");

        return $info;
    }

}
