<?php

namespace Newsletter2Go\Newsletter2Go\Controller;

use Newsletter2Go\Newsletter2Go\Helper\Nl2GoResponseHelper;
use OxidEsales\Eshop\Application\Controller\FrontendController;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Request;

class CallbackController extends FrontendController
{
    public function getCallback()
    {
        $config = $this->getConfig();
        $request = oxNew(Request::class);
        $companyId = $request->getRequestParameter('company_id');

        if (isset($companyId)) {
            Registry::getConfig()->saveShopConfVar(
                'str',
                'nl2goCompanyId',
                $companyId,
                $config->getShopId(),
                'module:Newsletter2Go'
            );
            $data = Nl2GoResponseHelper::generateSuccessResponse();
        } else {
            $data = Nl2GoResponseHelper::generateErrorResponse('Company ID is missing', Nl2GoResponseHelper::ERRNO_PLUGIN_OTHER);
        }

        Registry::getUtils()->setHeader('Content-Type: application/json;');
        Registry::getUtils()->showMessageAndExit($data);

    }
}