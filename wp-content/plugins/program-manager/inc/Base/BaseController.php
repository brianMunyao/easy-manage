<?php

/**
 * @package ProgramManager
 */

namespace Inc\Base;

class BaseController
{
    public function get_response_object($code, $message, $data = NULL)
    {
        $res = ["code" => $code];

        if ($message) {
            $res['message'] = $message;
            return $res;
        }

        if ($data) {
            $res['data'] = $data;
            return $res;
        }
        return $res;
    }
}
