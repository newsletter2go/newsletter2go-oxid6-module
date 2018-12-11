<?php

namespace Newsletter2Go\Newsletter2Go\Model;

use OxidEsales\Eshop\Application\Model\Category;
use OxidEsales\PayPalModule\Core\ViewConfig;
use OxidEsales\Eshop\Core\Registry;

class FrontendModel extends ViewConfig
{
    /**
     * Returns company id if it's set and tracking is enabled
     *
     * @return mixed|string
     */
    public function getCompanyId()
    {
        $result = '';

        $trackingEnabled = Registry::getConfig()->getConfigParam('nl2goTracking');
        $companyId = Registry::getConfig()->getConfigParam('nl2goCompanyId');

        if (!empty($companyId) && !empty($trackingEnabled)) {
            $result = $companyId;
        }

        return $result;
    }

    /**
     * Returns TRUE if express checkout and displaying it in mini basket is enabled.
     * NOTE: This function is required to be implemented in the class that extends ViewConfig.
     * Otherwise, the shop produces an exception and the layouts on pages are out of order.
     * Since the only other place in this shop where I found this method being implemented
     * was in PayPal module, I used their class and their implementation, given that PayPal module
     * comes with OXID 6, so that dependency will be met on other OXID 6 shops too.
     *
     * @return bool
     */
    public function isExpressCheckoutEnabledInMiniBasket()
    {
        $expressCheckoutEnabledInMiniBasket = false;
        if ($this->isExpressCheckoutEnabled() && $this->getPayPalConfig()->isExpressCheckoutInMiniBasketEnabled()) {
            $expressCheckoutEnabledInMiniBasket = true;
        }

        return $expressCheckoutEnabledInMiniBasket;
    }

    /**
     * Returns last category title based on ids
     *
     * @param $categoryIds
     * @return string
     */
    public function getCategoryName($categoryIds)
    {
        $categoryName = '';

        foreach ($categoryIds as $categoryId) {
            /** @var Category $oxCategory */
            $oxCategory = oxNew(Category::class);
            if ($oxCategory->load($categoryId)) {
                $categoryName = $oxCategory->getTitle();
            }
        }

        return $categoryName;
    }
}