<?php

function apiRes($state = '0', $msg = 'error', $code = '000', $data = null)
{

    $result = array('state' => $state, 'msg' => $msg, 'data' => $data, "code" => $code);
    echo json_encode($result);
    exit;
}
