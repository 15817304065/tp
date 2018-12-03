<?php
namespace Api_h5\Controller;

use Think\Controller;

class EmptyController extends Controller
{

    public function _empty()
    {
        header('Access-Control-Allow-Origin:*');
        apiResponse("0", "no controller!");
    }

}
