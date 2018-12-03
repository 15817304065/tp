<?php
define('TYPE', false);

$config = array( //常规配置
    'SESSION_AUTO_START' => true,
    'SESSION_PREFIX'     => 'Isession_', // session 前缀 避免冲突
    'COOKIE_PREFIX'      => 'Icookie_', // Cookie前缀 避免冲突

    'DB_FIELDS_CACHE'    => false, // 关闭字段缓存
    'URL_MODEL'          => 0, //Rewrite：URL模式
    'URL_HTML_SUFFIX'    => 'html', //伪静态后缀
    'LOG_RECORD'         => true, //日志记录
    'VAR_MODULE'         => 'm', // 默认模块获取变量
    'VAR_CONTROLLER'     => 'c', // 默认控制器获取变量
    'VAR_ACTION'         => 'a', // 默认操作获取变量

    'TMPL_PARSE_STRING'  => array(
        '__APP__' => __ROOT__ . "/App/", //自定义模板常量
    ),
    'ALIPAY_CONFIG'      => array(
        'partner'          => '', // partner 从支付宝商户版个人中心获取
        'seller_email'     => '', // email 从支付宝商户版个人中心获取
        'key'              => '', // key 从支付宝商户版个人中心获取
        'sign_type'        => strtoupper(trim('MD5')), // 可选md5  和 RSA
        'input_charset'    => 'utf-8', // 编码 (固定值不用改)
        'transport'        => 'http', // 协议  (固定值不用改)
        'cacert'           => VENDOR_PATH . 'Alipay/cacert.pem', // cacert.pem存放的位置 (固定值不用改)
        'notify_url'       => 'http://baijunyao.com/Api/Alipay/alipay_notify', // 异步接收支付状态通知的链接
        'return_url'       => 'http://baijunyao.com/Api/Alipay/alipay_return', // 页面跳转 同步通知 页面路径 支付宝处理完请求后,当前页面自 动跳转到商户网站里指定页面的 http 路径。 (扫码支付专用)
        'show_url'         => 'http://baijunyao.com/User/Order/index', // 商品展示网址,收银台页面上,商品展示的超链接。 (扫码支付专用)
        'private_key_path' => '', //移动端生成的私有key文件存放于服务器的 绝对路径 如果为MD5加密方式；此项可为空 (移动支付专用)
        'public_key_path'  => '', //移动端生成的公共key文件存放于服务器的 绝对路径 如果为MD5加密方式；此项可为空 (移动支付专用)
    ),
    'TENGXUNAI_CONFIG'   => array(
        'app_id'  => '1107058300', // partner 从支付宝商户版个人中心获取
        'app_key' => 'ffFDxIsAwNNBDf9u', // email 从支付宝商户版个人中心获取
    ),
    // 'HUAWEIAI_CONFIG'    => array(
    //     'app_name' => 'qw4500655', //
    //     'app_pw'   => 'qw19911010', //
    // ),
    'HUAWEIAI_CONFIG'    => array(
        'app_name' => 'issmart_shenzhen', //
        'app_pw'   => 'Huawei@123.com', //
    ),
);

$db = array( //数据库配置
    'DB_DEPLOY_TYPE' => 1,
    'DB_TYPE'        => 'mysql', // 数据库类型
    'DB_HOST'        => 'dbserver', // 服务器地址
    'DB_NAME'        => 'offer', // 数据库名
    'DB_USER'        => 'root', // 用户名
    'DB_PWD'         => 'Mysql$Issmart@123', // 密码
    'DB_PORT'        => '3306', // 端口
    'DB_PREFIX'      => 'event_', // 数据库表前缀
);

if ($_SERVER['HTTP_HOST'] == 'localhost') {
    $db = array( //数据库配置
        'DB_DEPLOY_TYPE' => 1,
        'DB_TYPE'        => 'mysql', // 数据库类型
        'DB_HOST'        => 'localhost', // 服务器地址
        'DB_NAME'        => 'test', // 数据库名
        'DB_USER'        => 'root', // 用户名
        'DB_PWD'         => '', // 密码
        'DB_PORT'        => '3306', // 端口
        'DB_PREFIX'      => 'event_', // 数据库表前缀
    );
}

$smtp = array( //smtp邮件设置
    'MAIL_HOST'     => 'smtp.exmail.qq.com', //smtp服务器的名称
    'MAIL_SMTPAUTH' => true, //启用smtp认证
    'MAIL_USERNAME' => 'lianghui@issmart.com.cn', //你的邮箱名
    'MAIL_FROM'     => 'lianghui@issmart.com.cn', //发件人地址
    'MAIL_FROMNAME' => '易思智科技', //发件人姓名
    'MAIL_PASSWORD' => '', //邮箱密码
    'MAIL_CHARSET'  => 'utf-8', //设置邮件编码
    'MAIL_ISHTML'   => true, // 是否HTML格式邮件
);

return array_merge($config, $db, $smtp);
