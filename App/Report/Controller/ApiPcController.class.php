<?php
namespace Report\Controller;

use Think\Controller;

class ApiPcController extends Controller
{
    public function index()
    {
        $data = $this->userData(1)[0];

        foreach ($data as $k => $v) {
            $list[] = $k;
        }
        $title = '接口总访问数据';
        $uv    = M('interface_pc')->group('openid')->select();
        $uv    = count($uv);
        $pv    = M('api_info_pc')->field('SUM(pv) as pv')->select()[0]['pv'];

        $assign = [
            'list'  => $list,
            'title' => $title,
            'uv'    => $uv,
            'pv'    => $pv,
        ];
        $this->assign($assign);
        $this->display(':index');
    }
    public function user()
    {
        $data = $this->userData(2)[0];
        foreach ($data as $k => $v) {
            $list[] = $k;
        }
        $title  = '接口日访问数据';
        $assign = [
            'list'  => $list,
            'title' => $title,
        ];
        $this->assign($assign);
        $this->display(':user');
    }
    public function errorcode()
    {
        $data = $this->userData(3)[0];
        foreach ($data as $k => $v) {
            $list[] = $k;
        }
        $title  = '接口错误码数据';
        $assign = [
            'list'  => $list,
            'title' => $title,
        ];
        $this->assign($assign);
        $this->display(':errorcode');
    }
    private function userData($item = 1)
    {
        if ($item == 1) {
            $re  = M('api_info_pc')->field('id as api_id,title as "接口名称",num as "接口调用次数",out_link as "外链跳转次数",pv')->where('pv!=0')->order('pv desc')->select();
            $re2 = M()->query('SELECT api_id,count(openid) uv FROM `ei_interface_pc` GROUP BY api_id');
            foreach ($re as $k => $v) {
                foreach ($re2 as $key => $value) {
                    if ($value['api_id'] == $v['api_id']) {
                        $re[$k]['uv'] = $value['uv'];
                        continue;
                    }
                }
                $re[$k]['api_id'] = "api_" . $v['api_id'];

            }
        } else if ($item == 2) {
            $re  = M()->query('SELECT  u.api_id,u.date_time as "调用日期", i.title as "接口名称",sum(u.num) as "当日调用次数" FROM `ei_interface_pc` as u LEFT JOIN ei_api_info_pc as i on u.api_id =i.id GROUP BY u.date_time,u.api_id');
            $re2 = M()->query('SELECT api_id,date_time,count(openid) uv FROM `ei_interface_pc` GROUP BY api_id,date_time');
            // var_dump($re, $re2);die;
            foreach ($re as $k => $v) {
                foreach ($re2 as $key => $value) {
                    if (($value['api_id'] == $v['api_id']) && ($value['date_time'] == $v['调用日期'])) {
                        $re[$k]['当日uv'] = $value['uv'];
                        continue;
                    }
                }
                $re[$k]['api_id'] = "api_" . $v['api_id'];
            }
            // var_dump($re);
        } else {
            $re = M()->query('SELECT i.title, e.error_code,e.error_msg,e.date_time,e.num FROM `ei_errorcode_pc` as e LEFT JOIN ei_api_info_pc as i on e.api_name=i.api_name where e.error_code!="";');
        }
        return $re;

    }
    public function getUser($item)
    {
        $this->ajaxReturn($this->userData($item));
    }

    public function expUser($item)
    {
        if ($item == 1) {
            $actName = '接口总访问数据';
        } else if ($item == 2) {
            $actName = '接口日访问数据';
        } else {
            $actName = '接口错误码数据';
        }

        $data = $this->userData($item);
        // foreach ($data as $k => $v) {
        //     $list[$k]['name']       = $v['name'];
        //     $list[$k]['comment']    = $v['comment'];
        //     $list[$k]['createtime'] = $v['createtime'];
        // }
        $list     = $data;
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
        // 生成excel
        $exceler->export();
    }

}
