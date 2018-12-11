<?php

namespace Newsletter2Go\Newsletter2Go\Core;

use OxidEsales\Eshop\Core\Registry;
use \OxidEsales\EshopCommunity\Application\Controller\Admin\ModuleConfiguration;

class Nl2GoSettings extends ModuleConfiguration
{
    //TODO: Change this URL once the integration for OX6 becomes available on Newsletter2Go
    const N2GO_INTEGRATION_URL = 'https://ui-sandbox.newsletter2go.com/integrations/connect/OX/';

    /**
     * Override parent render view.
     *
     * @return string
     */
    public function render()
    {
        $return = parent::render();
        $queryParams = [];

        $oConfig = $this->getConfig();
        $queryParams['password'] = $oConfig->getConfigParam('nl2goApiKey');
        $queryParams['username'] = $oConfig->getConfigParam('nl2goUserName');
        $queryParams['url'] = $oConfig->getShopUrl();
        $queryParams['callback'] = $oConfig->getShopUrl() . '?cl=nl2go_callback&fnc=getCallback';

        $oLang = Registry::getLang();
        $lang = $oLang->getTplLanguage();
        $queryParams['language'] = $oLang->getLanguageAbbr($lang);

        $this->_aViewData["n2goConnectUrl"] = self::N2GO_INTEGRATION_URL . '?' . http_build_query($queryParams);

        return $return;
    }
}