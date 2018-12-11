<?php

namespace Newsletter2Go\Newsletter2Go\Controller;

use OxidEsales\Eshop\Application\Controller\FrontendController;
use OxidEsales\Eshop\Core\Utils;
use Newsletter2Go\Newsletter2Go\Helper\Nl2GoResponseHelper;
use OxidEsales\EshopCommunity\Core\Request;

class RootController extends FrontendController
{
    /**
     * Override parent init method.
     */
    public function init()
    {
        $res = null;
        $config = $this->getConfig();
        /** @var Request $request */
        $request = oxNew(Request::class);

        $username = $request->getRequestParameter('username') ? $request->getRequestParameter('username') : '';
        $apiKey = $request->getRequestParameter('api_key') ? $request->getRequestParameter('api_key') : '';

        if($_SERVER['REQUEST_METHOD'] !== 'POST'){
            $this->delegateError('Wrong request method',
                Nl2GoResponseHelper::ERRNO_PLUGIN_OTHER);
        } elseif (strlen($username) === 0) {
            $this->delegateError('Username is missing',
                Nl2GoResponseHelper::ERRNO_PLUGIN_CREDENTIALS_MISSING);
        } elseif (strlen($apiKey) === 0) {
            $this->delegateError('API key is missing',
                Nl2GoResponseHelper::ERRNO_PLUGIN_CREDENTIALS_MISSING);
        } elseif ($username !== $config->getConfigParam('nl2goUserName') ||
            $apiKey != $config->getConfigParam('nl2goApiKey')
        ) {
            $this->delegateError('Credentials are invalid',
                Nl2GoResponseHelper::ERRNO_PLUGIN_CREDENTIALS_WRONG);
        }

        parent::init();
    }

    /**
     * Formats response as a JSON object.
     *
     * @param array $data
     */
    protected function sendResponse($data = [])
    {
        $utils = new Utils();
        $utils->setHeader('Content-Type: application/json;');
        $utils->showMessageAndExit(Nl2GoResponseHelper::generateSuccessResponse($data));
    }

    /**
     * Function for generating error response with appropriate error message.
     *
     * @param $message
     * @param $errorCode
     */
    static function delegateError($message, $errorCode)
    {
        $res = Nl2GoResponseHelper::generateErrorResponse($message,
            $errorCode);
        $utils = new Utils();
        $utils->setHeader('Content-Type: application/json;');
        $utils->showMessageAndExit($res);
    }
}