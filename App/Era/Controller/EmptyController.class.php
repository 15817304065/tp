<?php
namespace Era\Controller;

use Think\Controller;

class EmptyController extends Controller
{

    public function _empty()
    {
        apiResponse("0", "no controller!");
    }

}
