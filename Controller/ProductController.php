<?php

namespace Newsletter2Go\Newsletter2Go\Controller;

use Newsletter2Go\Newsletter2Go\Helper\Nl2GoResponseHelper;
use Newsletter2Go\Newsletter2Go\Model\ProductModel;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Request;

class ProductController extends RootController
{
    /**
     * Returns all the languages available in the shop.
     */
    public function getLanguages()
    {
        $languages = [];

        foreach (Registry::getLang()->getLanguageArray() as $lang) {
            $languages[$lang->oxid] = [
                'id' => $lang->oxid,
                'name' => $lang->name,
                'locale' => $lang->abbr,
                'default' => $lang->selected,
            ];
        }

        $this->sendResponse(['languages' => $languages]);
    }

    /**
     * Returns product fields in the database.
     */
    public function getProductFields()
    {
        /** @var ProductModel $model */
        $model = oxNew(ProductModel::class);
        $attributes = $model->getProductAttributes();
        $this->sendResponse(['attributes' => $attributes]);
    }

    /**
     * Returns a specific product for a given id in a given language.
     */
    public function getProduct()
    {
        /** @var Request $request */
        $request = oxNew(Request::class);
        $lang = $request->getRequestParameter('lang') ? $request->getRequestParameter('lang') : 'de';
        $languages = Registry::getLang()->getLanguageArray();
        $currentLang = null;

        foreach ($languages as $language) {
            if ($language->oxid == $lang) {
                $currentLang = $language;
                break;
            }
        }

        if (null == $currentLang) {
            self::delegateError('Invalid language', Nl2GoResponseHelper::ERRNO_PLUGIN_OTHER);
        }

        $id = $request->getRequestParameter('id');
        $itemNumber = $request->getRequestParameter('item_number');
        $attributes = $request->getRequestParameter('attributes', []);

        if (!$id && !$itemNumber) {
            self::delegateError('Parameters id and/or item number not found!', Nl2GoResponseHelper::ERRNO_PLUGIN_OTHER);
        }

        /** @var ProductModel $model */
        $model = oxNew(ProductModel::class);
        $product = $model->getProductInfo(isset($id) ? $id : $itemNumber, $lang, $attributes);
        if (!$product) {
            self::delegateError('Product not found', Nl2GoResponseHelper::ERRNO_PLUGIN_OTHER);
        } else {
            $this->sendResponse(['product' => $product]);
        }
    }
}
