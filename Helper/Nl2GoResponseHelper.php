<?php

namespace Newsletter2Go\Newsletter2Go\Helper;

class Nl2GoResponseHelper
{

    /**
     * err-number, that should be pulled, whenever credentials are missing
     */
    const ERRNO_PLUGIN_CREDENTIALS_MISSING = 'int-1-404';
    /**
     *err-number, that should be pulled, whenever credentials are wrong
     */
    const ERRNO_PLUGIN_CREDENTIALS_WRONG = 'int-1-403';
    /**
     * err-number for all other (intern) errors. More Details to the failure should be added to error-message
     */
    const ERRNO_PLUGIN_OTHER = 'int-1-600';


    /**
     * Sends back a JSON response with success flag, message and code that corresponds to one of possible errors.
     *
     * @param $message
     * @param $errorCode
     * @param null $context
     * @return string
     */
    static function generateErrorResponse($message, $errorCode, $context = null)
    {
        $res = [
            'success' => false,
            'message' => $message,
            'errorcode' => $errorCode,
        ];
        if ($context !== null) {
            $res['context'] = $context;
        }
        return json_encode($res);
    }

    /**
     * Sends back a success JSON response with success flag, message and data.
     *
     * @param array $data
     * @return string
     */
    static function generateSuccessResponse($data = [])
    {
        $res = ['success' => true, 'message' => 'OK'];
        $res = array_merge($res, $data);
        $json = json_encode($res);
        if ($json === false) {
            if (json_last_error() === JSON_ERROR_UTF8) {
                $res = self::utf8ize($res);
                $json = json_encode($res);
                return $json;
            } else {
                return self::generateErrorResponse('problem on json-encoding: ' . json_last_error_msg(), self::ERRNO_PLUGIN_OTHER);
            }
        }
        return $json;
    }

    /**
     * Function that encodes the parameter in JSON format.
     *
     * @param $d
     * @return array|string
     */
    static function utf8ize($d)
    {
        if (is_array($d)) {
            foreach ($d as $k => $v) {
                $d[$k] = self::utf8ize($v);
            }
        } else if (is_string($d)) {
            return utf8_encode($d);
        }

        return $d;
    }

}