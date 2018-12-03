<?php
return [
    'TMPL_PARSE_STRING' => array(
        '__APP__' => __ROOT__ . "/App/Home/View/",
    ),
    'IMGTYPE'           => array(
        'image/jpg',
        'image/jpeg',
        'image/png',
        'image/bmp',
    ),
    'AUDIOTYPE'         => array(
        'audio/wav',
        'audio/mp3',
        'audio/mpeg',
        'audio/wma',
        'audio/amr',
        'audio/ac3',
        'audio/ogg',
        'audio/aac',
        'audio/x-aac',
    ),
    'DB_PREFIX'         => 'ei_', // 数据库表前缀

    'ERROR_CODE'        => array(

        'AIS.0401'  => '输入参数有误',
        'AIS.0402'  => '图片格式不支持',
        'AIS.0403'  => '图片文件已损坏',
        'AIS.0404'  => '内容检测处理错误',
        'AIS.0405'  => '内部错误',
        'AIS.0601'  => '输入参数不合法',
        'AIS.0602'  => '语音格式不支持',
        'AIS.0603'  => '语音受损',
        'AIS.0604'  => '语音文件大小不符合要求',
        'AIS.0605'  => '出现内部错误',
        'AIS.0606'  => '待合成的字符数超出限制',
        'AIS.0607'  => '任务或结果不存在',
        'AIS.0608'  => '识别失败',
        'AIS.0609'  => '从指定URL下载音频文件失败',
        'AIS.0101'  => '输入参数不符合规范',
        'AIS.0102'  => '图片格式不支持',
        'AIS.0103'  => '图片尺寸不满足要求',
        'AIS.0104'  => '非支持的图片类型或图片质量差',
        'AIS.0105'  => '算法计算失败',
        'AIS.0106'  => '图像位深度不支持',
        'AIS.0201'  => '获取输入图像异常',
        'AIS.0202'  => '图片格式不支持',
        'AIS.0203'  => '输入参数不符合规范',
        'AIS.0204'  => '图片位深不支持',
        'AIS.0205'  => '图片分辨率超限',
        'AIS.0206'  => '算法计算失败',
        'AIS.0501'  => '输入参数不合法',
        'AIS.0502'  => '图像格式不支持',
        'AIS.0503'  => '图像受损',
        'AIS.0504'  => '图像大小不符合要求',
        'AIS.0505'  => '算法运行失败',
        'AIS.0506'  => '出现内部错误',
        'FRS.0501'  => '照片未检测到人脸',
        'FRS.0701'  => '视频解析错误',
        'APIG.0101' => 'API不存在',
        'FRS.0015'  => '图片base64解析错误',
        'FRS.0016'  => '上传的文件格式不支持',

    ),

];