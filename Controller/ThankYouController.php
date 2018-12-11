<?php

namespace Newsletter2Go\Newsletter2Go\Controller;

use OxidEsales\Eshop\Application\Model\Category;
use OxidEsales\Eshop\Core\Registry;

class ThankYouController extends ThankYouController_parent
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