<?php

namespace Newsletter2Go\Newsletter2Go\Model;

use \OxidEsales\Eshop\Core\Registry;
use \OxidEsales\Eshop\Core\DatabaseProvider;
use \OxidEsales\Eshop\Application\Model\Article;
use \OxidEsales\Eshop\Core\Config;

class ProductModel
{
    public function getProductAttributes($lang = null)
    {

        if($lang === null){
            $lang = Registry::getLang()->getLanguageAbbr();
        }

        $descriptions = [
            'OXID' => 'Product Id.',
            'OXSHOPID' => 'Shop id(oxshops)',
            'OXVENDORID' => 'Vendor id(oxvendor)',
            'OXREMINDAMOUNT' => 'Defines the amount, below which notification email will be sent if oxremindactive is set to 1',
            'OXREMINDACTIVE' => 'Enables sending of notification email when oxstock field value falls below oxremindamount value',
            'OXFREESHIPPING' => 'Free shipping(variants inherits parent setting',
            'OXNONMATERIAL' => 'Intangible article, free shipping is used(variants inherits parent setting)',
            'OXSOLDAMOUNT' => 'Amount of sold articles including variants(used only for parent articles)',
            'OXSORT' => 'Sorting',
            'OXSUBCLASS' => 'Subclass',
            'OXFOLDER' => 'Folder',
            'OXBUNDLEID' => 'Bundled article id',
            'OXVARMAXPRICE' => '>Highest price in active article variants',
            'OXVARMINPRICE' => 'Lowest price in active article variants',
            'OXVARSELECT' => 'Variant article selections(separated by | )',
            'OXVARCOUNT' => 'Total number of variants that article has(active and inactive)',
            'OXVARSTOCK' => 'Sum of active article variants stock quantity',
            'OXVARNAME' => 'Name of variants selection lists(different lists are separated by | )',
            'OXISCONFIGURABLE' => 'Can article be customized',
            'OXISSEARCH' => 'Should article be shown in search',
            'OXQUESTIONEMAIL' => 'E - mail for question',
            'OXSEARCHKEYS' => 'Search terms',
            'OXFILE' => 'File, shown in article media list',
            'OXHEIGHT' => 'Article dimensions: Height',
            'OXWIDTH' => 'Article dimensions: Width',
            'OXLENGTH' => 'Article dimensions: Length',
            'OXTIMESTAMP' => 'Timestamp of last modification',
            'OXINSERT' => 'Insert time',
            'OXDELIVERY' => 'Date, when the product will be available again if it is sold out',
            'OXSTOCKTEXT' => 'Message, which is shown if the article is in stock',
            'OXSTOCKFLAG' => 'Delivery Status: 1 - Standard, 2 - If out of Stock, offline, 3 - If out of Stock, not orderable, 4 - External Storehouse',
            'OXSTOCK' => 'Article quantity in stock',
            'OXWEIGHT' => 'Weight(kg)',
            'OXVAT' => 'Value added tax.If specified, used in all calculations instead of global vat',
            'OXURLIMG' => 'External URL image',
            'OXURLDESC' => 'Text for external URL',
            'OXPARENTID' => 'Parent article id',
            'OXACTIVE' => 'Active',
            'OXACTIVEFROM' => 'Active from specified date',
            'OXACTIVETO' => 'Active to specified date',
            'OXARTNUM' => 'Article number',
            'OXEAN' => 'International Article Number(EAN)',
            'OXDISTEAN' => 'Manufacture International Article Number(Man.EAN)',
            'OXMPN' => 'Manufacture Part Number(MPN)',
            'OXTITLE' => 'Title',
            'OXSHORTDESC' => 'Short description',
            'OXPRICE' => 'Article Price',
            'OXBLFIXEDPRICE' => 'No Promotions(Price Alert)',
            'OXPRICEA' => 'Price A',
            'OXPRICEB' => 'Price B',
            'OXPRICEC' => 'Price C',
            'OXBPRICE' => 'Purchase Price',
            'OXTPRICE' => 'Recommended Retail Price(RRP)',
            'OXUNITNAME' => 'Unit name(kg, g, l, cm etc), used in setting price per quantity unit calculation',
            'OXUNITQUANTITY' => 'Article quantity, used in setting price per quantity unit calculation',
            'OXEXTURL' => 'External URL to other information about the article',
            'OXRATING' => 'Article rating',
            'OXMINDELTIME' => 'Minimal delivery time(unit is set in oxdeltimeunit)',
            'OXMAXDELTIME' => 'Maximum delivery time(unit is set in oxdeltimeunit)',
            'OXDELTIMEUNIT' => 'Delivery time unit: DAY, WEEK, MONTH',
            'OXUPDATEPRICE' => 'If not 0, oxprice will be updated to this value on oxupdatepricetime date',
            'OXUPDATEPRICEA' => 'If not 0, oxprice will be updated to this value on oxupdatepricetime date',
            'OXUPDATEPRICEB' => 'If not 0, oxprice will be updated to this value on oxupdatepricetime date',
            'OXUPDATEPRICEC' => 'If not 0, oxprice will be updated to this value on oxupdatepricetime date',
            'OXUPDATEPRICETIME' => 'Date, when oxprice[a, b, c] should be updated to oxupdateprice[a, b, c] values',
            'OXISDOWNLOADABLE' => 'Enable download of files for this product'
        ];

        $oxProductFields = $this->getProductFields($lang, $descriptions);
        $oxPrice2ArticleFields = $this->getPrice2ArticleFields($descriptions);
        $additionalFields = $this->getAdditionalFields();

        return array_merge($oxProductFields, $oxPrice2ArticleFields, $additionalFields);
    }

    public function getProductFields($lang, $descriptions)
    {
        $oxProductFields = [];
        $oDb = DatabaseProvider::getDb(DatabaseProvider::FETCH_MODE_ASSOC);
        $metaFields = $oDb->metaColumns('oxv_oxarticles_'.$lang);

        foreach ($metaFields as $field) {
            switch ($field->type) {
                case 'int':
                case 'tinyint':
                    $type = 'Integer';
                    break;
                case 'double':
                case 'float':
                    $type = 'Float';
                    break;
                case 'date':
                case 'datetime':
                case 'timestamp':
                    $type = 'Date';
                    break;
                default:
                    $type = 'String';
            }
            $oxProductFields[] = [
                'id' => 'oxarticles.' . $field->name,
                'name' => $field->name,
                'description' => isset($descriptions[$field->name]) ? $descriptions[$field->name] : $field->name,
                'type' => $type
            ];
        }

        return $oxProductFields;
    }

    public function getPrice2ArticleFields($descriptions)
    {
        $oxPrice2ArticleFields = [];
        $oDb = DatabaseProvider::getDb(DatabaseProvider::FETCH_MODE_ASSOC);
        $dbPrice2ArticleFields = $oDb->metaColumns('oxprice2article');

        foreach ($dbPrice2ArticleFields as $field) {
            switch ($field->type) {
                case 'int':
                case 'tinyint':
                    $type = 'Integer';
                    break;
                case 'double':
                case 'float':
                    $type = 'Float';
                    break;
                case 'date':
                case 'datetime':
                case 'timestamp':
                    $type = 'Date';
                    break;
                default:
                    $type = 'String';
            }
            $oxPrice2ArticleFields[] = [
                'id' => 'oxprice2article.' . $field->name,
                'name' => $field->name,
                'description' => isset($descriptions[$field->name]) ? $descriptions[$field->name] : $field->name,
                'type' => $type
            ];
        }

        return $oxPrice2ArticleFields;
    }

    public function getAdditionalFields()
    {
        $additionalFields = [
            [
                'id' => 'images',
                'name' => 'Item images',
                'description' => '',
                'type' => 'Array',
            ],
            [
                'id' => 'link',
                'name' => 'Link to the item',
                'description' => 'Link to the item',
                'type' => 'String',
            ],
            [
                'id' => 'url',
                'name' => 'Shop URL',
                'description' => 'Shop URL',
                'type' => 'String',
            ],
            [
                'id' => 'vat',
                'name' => 'Default VAT',
                'description' => 'Default shop VAT',
                'type' => 'Float',
            ],
            [
                'id' => 'oxshops.OXNAME',
                'name' => 'Shop name',
                'description' => 'Shop name',
                'type' => 'String',
            ],
            [
                'id' => 'oxartextends.OXLONGDESC',
                'name' => 'Long description',
                'description' => 'Long description',
                'type' => 'String',
            ],
            [
                'id' => 'oxvendor.OXTITLE',
                'name' => 'Vendor name',
                'description' => 'Vendor name',
                'type' => 'String',
            ],
            [
                'id' => 'oxmanufacturers.OXTITLE',
                'name' => 'Manufacturer name',
                'description' => 'Manufacturer name',
                'type' => 'String',
            ],
        ];

        return $additionalFields;
    }

    public function getProductInfo($id, $language = 'de', array $attributeIds = [])
    {
        foreach (Registry::getLang()->getLanguageArray() as $lang) {
            if (strlen($language) === 0) {
                if ($lang->selected) {
                    $language = $lang->abbr;
                    break;
                }
            } else {
                if ($lang->abbr == $language) {
                    $language = $lang->abbr;
                    break;
                }
            }
        }

        if (empty($attributeIds)) {
            $attributeIds = array_column($this->getProductAttributes($language), 'id');
        }

        $articleView = 'oxv_oxarticles_' . $language;
        $shopView = 'oxv_oxshops_' . $language;
        $vendorView = 'oxv_oxvendor_' . $language;
        $manufView = 'oxv_oxmanufacturers_' . $language;
        $artextView = 'oxv_oxartextends_' . $language;
        $artPriceView = 'oxprice2article';

        $querySelect = $this->createProductSelect($attributeIds, $language);
        $queryWhere = " WHERE $articleView.OXID = '" .
            $id . "' OR $articleView.OXARTNUM = '" .
            $id . "' ";
        $queryFrom = " FROM $articleView
                            LEFT JOIN $shopView ON $shopView.OXID = $articleView.OXSHOPID
                            LEFT JOIN $vendorView ON $vendorView.OXID = $articleView.OXVENDORID
                            LEFT JOIN $manufView ON $manufView.OXID = $articleView.OXMANUFACTURERID
                            LEFT JOIN $artextView ON $artextView.OXID = $articleView.OXID
                            LEFT JOIN $artPriceView ON $artPriceView.OXARTID = $articleView.OXID";

        $oDb = DatabaseProvider::getDb(DatabaseProvider::FETCH_MODE_ASSOC);
        /** @var \OxidEsales\Eshop\Core\Database\Adapter\ResultSetInterface $rsProduct */
        $rsProduct = $oDb->select($querySelect . $queryFrom . $queryWhere);
        $result = $rsProduct->fetchAll()[0];
        if (!$result) {
            return false;
        }

        /** @var Article $article */
        $article = oxNew(Article::class);
        $article->load($result['oxarticles.OXID']);
        if (array_search('oxarticles.OXID', $attributeIds) === false) {
            unset($result['oxarticles.OXID']);
        }

        if (array_search('images', $attributeIds) !== false) {
            $pictureLimit = Registry::getConfig()->getConfigParam('iPicCount');
            $result['images'] = [];
            foreach (range(1, $pictureLimit) as $index) {
                if (($imageUrl = $article->getPictureUrl($index)) && !(strstr($imageUrl, 'nopic.jpg'))) {
                    $result['images'][$index] = $imageUrl;
                }
            }
        }
        $link = $article->getLink();
        $host = parse_url($link,PHP_URL_HOST).'/';
        if (array_search('link', $attributeIds) !== false) {
            $result['link'] =  ltrim(substr($link, strpos($link,$host) + strlen($host)), '/');

            if(strpos($result['link'], '?force_sid') !== false){
                $result['link'] = substr($result['link'], 0 , strpos($result['link'], '?force_sid'));
            }
        }

        /** @var Config $config */
        $config = oxNew(Config::class);

        if (array_search('vat', $attributeIds) !== false) {
            $result['vat'] = $config->getConfigParam('dDefaultVAT') * 0.01;
        }

        if (array_search('url', $attributeIds) !== false) {
            $result['url'] =  rtrim(substr($link,0, strpos($link, $host) + strlen($host)), '/').'/';
        }

        $vat =(isset($result['oxarticles.OXVAT']) && strlen($result['oxarticles.OXVAT']) > 0 ? $result['oxarticles.OXVAT'] : $config->getConfigParam('dDefaultVAT')) * 0.01;


        if (isset($result['oldPriceNet']) && $vat> 0) {
            $result['oldPriceNet'] = number_format($result['oldPriceNet'] / (1 + $vat), 2);
        }

        if (isset($result['newPriceNet'])  && $vat > 0) {
            $result['newPriceNet'] = number_format($result['newPriceNet'] / (1 + $vat), 2);
        }

        return $result;
    }

    private function createProductSelect($attributeIds, $lang)
    {
        $select = "SELECT oxv_oxarticles_$lang.OXID AS 'oxarticles.OXID', ";
        foreach ($attributeIds as $attribute) {
            switch ($attribute) {
                case 'images':
                case 'link';
                case 'vat':
                case 'url':
                case 'oxarticles.OXID':
                case 'oxprice2article.OXID':
                case 'oxprice2article.OXSHOPID':
                case 'oxprice2article.OXARTID':
                    break;
                default:
                    $strings = explode('.', $attribute);
                    if($strings[0] == 'oxprice2article'){
                        $select .= "$attribute AS '$attribute', ";
                    }else{
                        $select .= "oxv_$strings[0]_$lang.$strings[1] AS '$attribute', ";
                    }
                    break;
            }
        }

        return substr($select, 0, -2);
    }
}