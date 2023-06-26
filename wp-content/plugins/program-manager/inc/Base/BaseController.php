<?php

/**
 * @package ProgramManager
 */

namespace Inc\Base;

class BaseController
{
    public function get_response_object($code, $message, $data = null)
    {
        $res = ["code" => $code];

        if (isset($message)) {
            $res['message'] = $message;
        }

        if ($data !== null) {
            $res['data'] = $data;
        }
        return $res;
    }
}
