<?php
namespace Api_pc\Controller;

use Think\Controller;

class EmptyController extends Controller
{

    public function _empty()
    {
        header('Access-Control-Allow-Origin:*');
        apiResponse("0", "no controller!");
    }

}
