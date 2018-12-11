<?php

namespace Newsletter2Go\Newsletter2Go\Controller;

use Newsletter2Go\Newsletter2Go\Helper\Nl2GoResponseHelper;
use Newsletter2Go\Newsletter2Go\Model\CustomerModel;
use OxidEsales\Eshop\Core\Request;

class CustomerController extends RootController
{
    /**
     * Returns all customer groups present on the shop.
     */
    public function getGroups()
    {
        /** @var CustomerModel $model */
        $model = oxNew(CustomerModel::class);
        $this->sendResponse(['groups' => $model->getCustomerGroups()]);
    }

    /**
     * Returns all customer fields.
     */
    public function getFields()
    {
        /** @var CustomerModel $model */
        $model = oxNew(CustomerModel::class);
        $this->sendResponse(['fields' => $model->getCustomerFields()]);
    }

    /**
     * Gets a number of customers that meet the requirements given in the request
     */
    public function getCustomerCount()
    {
        /** @var CustomerModel $model */
        $model = oxNew(CustomerModel::class);
        /** @var Request $request */
        $request = oxNew(Request::class);

        $subscribed = $request->getRequestParameter('subscribed', 0) == 1;
        $group = $request->getRequestParameter('group');

        $count = $model->getCustomerCount($subscribed, $group);
        $this->sendResponse(['count' => $count]);
    }

    /**
     * Returns all customers that meet the requirements given in the request.
     */
    public function getCustomers()
    {
        /** @var Request $request */
        $request = oxNew(Request::class);

        $params = [];
        $params['hours'] = $request->getRequestParameter('hours');
        $params['subscribed'] = $request->getRequestParameter('subscribed', 0) == 1;
        $params['offset'] = $request->getRequestParameter('offset');
        $params['limit'] = $request->getRequestParameter('limit');
        $params['groups'] = $request->getRequestParameter('groups');
        $params['fields'] = $request->getRequestParameter('fields', []);
        $params['emails'] = $request->getRequestParameter('emails', []);

        /** @var CustomerModel $model */
        $model = oxNew(CustomerModel::class);
        $customers = $model->getCustomers($params);

        $this->sendResponse([
            'customers' => $customers['data'],
            'results' => count($customers['data']),
        ]);
    }

    /**
     * Subscribe a customer with a given email.
     */
    public function subscribeCustomer()
    {
        /** @var Request $request */
        $request = oxNew(Request::class);

        $email = $request->getRequestParameter('email');
        if (!$email) {
            self::delegateError('Email parameter not found', Nl2GoResponseHelper::ERRNO_PLUGIN_OTHER);
        }

        /** @var CustomerModel $model */
        $model = oxNew(CustomerModel::class);

        if ($model->changeSubscription($email, 1)) {
            $this->sendResponse([]);
        } else {
            self::delegateError('No customer has been subscribed', Nl2GoResponseHelper::ERRNO_PLUGIN_OTHER);
        }
    }

    /**
     * Unsubscribe a customer with a given email.
     */
    public function unsubscribeCustomer()
    {
        /** @var Request $request */
        $request = oxNew(Request::class);

        $email = $request->getRequestParameter('email');
        if (!$email) {
            self::delegateError('Email parameter not found', Nl2GoResponseHelper::ERRNO_PLUGIN_OTHER);
        }

        /** @var CustomerModel $model */
        $model = oxNew(CustomerModel::class);

        if ($model->changeSubscription($email, 0)) {
            $this->sendResponse([]);
        } else {
            self::delegateError('No customer has been unsubscribed', Nl2GoResponseHelper::ERRNO_PLUGIN_OTHER);
        }
    }
}