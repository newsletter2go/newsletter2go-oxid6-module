<?php

namespace Newsletter2Go\Newsletter2Go\Model;

use \OxidEsales\Eshop\Core\Registry;
use \OxidEsales\Eshop\Core\DatabaseProvider;

class CustomerModel
{
    /**
     * Returns all customer groups that are present on the shop.
     *
     * @return array
     */
    public function getCustomerGroups()
    {
        $lang = Registry::getLang()->getLanguageAbbr();

        $sViewName = 'oxv_oxgroups_' . $lang;
        $oDb = DatabaseProvider::getDb(DatabaseProvider::FETCH_MODE_ASSOC);
        /** @var \OxidEsales\Eshop\Core\Database\Adapter\ResultSetInterface $rs */
        $rs = $oDb->select('SELECT * FROM ' . $sViewName);
        $result = [];

        $row = $rs->fetchRow();

        while ($row) {
            $result[] = [
                'id' => $row['OXID'],
                'name' =>$row['OXTITLE'],
                'description' => null,
            ];
            $row = $rs->fetchRow();
        }

        return $result;
    }

    /**
     * Returns customer fields.
     *
     * @return array
     */
    public function getCustomerFields()
    {

        $descriptions = [
            'COLUMN_NAME' => 'COLUMN_COMMENT',
            'OXID' => 'User id',
            'OXACTIVE' => 'Is active',
            'OXRIGHTS' => 'User rights: user, malladmin',
            'OXSHOPID' => 'Shop id (oxshops)',
            'OXUSERNAME' => 'Username',
            'OXCUSTNR' => 'Customer number',
            'OXUSTID' => 'VAT ID No.',
            'OXCOMPANY' => 'Company',
            'OXFNAME' => 'First name',
            'OXLNAME' => 'Last name',
            'OXSTREET' => 'Street',
            'OXSTREETNR' => 'House number',
            'OXADDINFO' => 'Additional info',
            'OXCITY' => 'City',
            'OXCOUNTRYID' => 'Country id (oxcountry)',
            'OXSTATEID' => 'State id (oxstates)',
            'OXZIP' => 'ZIP code',
            'OXFON' => 'Phone number',
            'OXFAX' => 'Fax number',
            'OXSAL' => 'User title (Mr/Mrs)',
            'OXBONI' => 'Credit points',
            'OXCREATE' => 'Creation time',
            'OXREGISTER' => 'Registration time',
            'OXPRIVFON' => 'Personal phone number',
            'OXMOBFON' => 'Mobile phone number',
            'OXBIRTHDATE' => 'Birthday date',
            'OXURL' => 'Url',
            'OXUPDATEKEY' => 'Update key',
            'OXUPDATEEXP' => 'Update key expiration time',
            'OXPOINTS' => 'User points (for registration, invitation, etc)',
            'OXFBID' => 'Facebook id (used for openid login)',
            'OXTIMESTAMP' => 'Timestamp'

        ];

        //get oxuser-fields dynamically
        $oxUserFields = [];

        $oDb = DatabaseProvider::getDb(DatabaseProvider::FETCH_MODE_ASSOC);
        $metaFields = $oDb->metaColumns('oxuser');


        foreach ($metaFields as $field) {

            if (in_array($field->name, ['OXPASSWORD', 'OXPASSSALT'])) {
                continue;
            }

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
            $oxUserFields[] = [
                'id' => 'oxuser.' . $field->name,
                'name' => isset($descriptions[$field->name]) ? $descriptions[$field->name] : $field->name,
                'description' => $field->name,
                'type' => $type];

        }

        $additionalFields = [
            [
                'id' => 'oxcountry.OXTITLE',
                'name' => 'Country name',
                'description' => 'Country name',
                'type' => 'String',
            ],
            [
                'id' => 'oxstates.OXTITLE',
                'name' => 'State name',
                'description' => 'State name',
                'type' => 'String',
            ],
            [
                'id' => 'oxnewssubscribed.OXDBOPTIN',
                'name' => 'Opt in',
                'description' => 'Subscription status: 0 - not subscribed, 1 - subscribed, 2 - not confirmed',
                'type' => 'Integer',
            ],
            [
                'id' => 'oxnewssubscribed.OXEMAILFAILED',
                'name' => 'Email bounce',
                'description' => 'Subscription email sending status',
                'type' => 'Integer',
            ],
            [
                'id' => 'oxnewssubscribed.OXSUBSCRIBED',
                'name' => 'Subscription date',
                'description' => 'Subscription date',
                'type' => 'Date',
            ],
            [
                'id' => 'oxnewssubscribed.OXUNSUBSCRIBED',
                'name' => 'Unsubscription date',
                'description' => 'Unsubscription date',
                'type' => 'Date',
            ],
        ];

        return array_merge($oxUserFields, $additionalFields);
    }

    private function createCustomersSelect($fieldIds)
    {
        $select = "SELECT ";
        foreach ($fieldIds as $field) {
            switch ($field) {
                case 'totalorders':
                case 'totalrevenue':
                case 'averagecartsize':
                case 'lastorder':
                    break;
                default:
                    $select .= $field . " as '" . $field . "', ";
                    break;
            }
        }

        return substr($select, 0, -2);
    }

    /**
     * Gets a number of customers that meet the requirements given in the request
     *
     * @param $subscribed
     * @param $group
     * @return
     */
    public function getCustomerCount($subscribed, $group)
    {
        $queryWhere = '';
        $conditions = [];

        $sql =
            "SELECT COUNT(oxuser.OXID) as count FROM oxuser LEFT JOIN oxnewssubscribed ON oxnewssubscribed.OXUSERID = oxuser.OXID";
        if ($subscribed) {
            $conditions[] = ' oxnewssubscribed.OXDBOPTIN = 1 ';
        }
        if (strlen($group) > 0) {
            $conditions[] =
                " oxuser.OXID IN (SELECT oxobject2group.OXOBJECTID FROM oxobject2group WHERE oxobject2group.OXGROUPSID = '" .
                $group . "') ";
        }

        if (count($conditions)) {
            $queryWhere = ' WHERE ' . implode(' AND ', $conditions);
        }

        $oDb = DatabaseProvider::getDb(DatabaseProvider::FETCH_MODE_ASSOC);
        /** @var \OxidEsales\Eshop\Core\Database\Adapter\ResultSetInterface $rsCustomers */
        $rsCustomers = $oDb->select($sql . $queryWhere);

        return $rsCustomers->fetchAll()[0]['count'];
    }

    /**
     * Gets all customers and filters them by given parameters.
     *
     * @param array $params
     * @return array
     */
    public function getCustomers($params = [])
    {
        $queryWhere = '';
        $queryLimit = '';
        $conditions = [];

        if (!empty($params['fields'])) {
            $fieldIds = $params['fields'];
        } else {
            $fieldIds = array_column($this->getCustomerFields(), 'id');
        }

        $querySelect = $this->createCustomersSelect($fieldIds);
        $queryFrom = ' FROM oxuser LEFT JOIN oxnewssubscribed ON oxnewssubscribed.OXUSERID = oxuser.OXID
                        LEFT JOIN oxstates ON oxuser.OXSTATEID = oxstates.OXID
                        LEFT JOIN oxcountry ON oxuser.OXCOUNTRYID = oxcountry.OXID ';

        if ($params['groups']) {
            $groups = implode("','", $params['groups']);
            $conditions[] =
                " oxuser.OXID IN (SELECT oxobject2group.OXOBJECTID FROM oxobject2group WHERE oxobject2group.OXGROUPSID IN ('" .
                $groups . "')) ";
        }

        if ($params['subscribed']) {
            $conditions[] = ' oxnewssubscribed.OXDBOPTIN = 1 ';
        }

        if ($params['emails']) {
            $emails = implode("','", $params['emails']);
            $conditions[] = " oxuser.OXUSERNAME IN ('" . $emails . "') ";
        }

        if ($params['hours']) {
            $ts = date('Y - m - d H:i:s', time() - 3600 * $params['hours']);
            $conditions[] = " oxuser.OXTIMESTAMP >= '$ts' ";
        }

        if (count($conditions)) {
            $queryWhere = ' WHERE ' . implode(' AND ', $conditions);
        }

        if ($params['limit']) {
            $limit = $params['limit'];
            $offset = $params['offset'] ? $params['offset'] : 0;
            $queryLimit = "LIMIT $offset, $limit";
        }

        $oDb = DatabaseProvider::getDb(DatabaseProvider::FETCH_MODE_ASSOC);
        $query = $querySelect . $queryFrom . $queryWhere . $queryLimit;
        /** @var \OxidEsales\Eshop\Core\Database\Adapter\ResultSetInterface $rsCustomers */
        $rsCustomers = $oDb->select($query);

        $rows = $rsCustomers->fetchAll();

        return ['data' => $rows];
    }

    /**
     * Subscribe or unsubscribe a customer, depending on the subscribe flag
     *
     * @param $email
     * @param int $subscribe
     * @return int
     */
    public function changeSubscription($email, $subscribe = 0)
    {
        $oDb = DatabaseProvider::getDb(DatabaseProvider::FETCH_MODE_ASSOC);
        $result = $oDb->execute("UPDATE oxnewssubscribed SET OXDBOPTIN = " . $subscribe . " WHERE OXEMAIL=?", [$email]);

        return $result;
    }


}
