<?php
// >= php 5.3.0，低版本的php需手动include SDK文件夹的所有文件
// spl_autoload_register(function ($class) {
//     include("./SDK/{$class}.php");
// });
require_once 'SDK/API.php';
require_once 'SDK/Configer.php';
require_once 'SDK/HttpUtil.php';
require_once 'SDK/Signature.php';
