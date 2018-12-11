<?php

namespace Newsletter2Go\Newsletter2Go\Controller;

class BaseController extends RootController
{
    /**
     * Plugin version
     *
     * @var string
     */
    private $version = '4000';

    /**
     * Test that the plugin is working and that it provides endpoint for the Connector.
     */
    public function testConnection()
    {
        $this->sendResponse(['test' => true]);
    }

    /**
     * Returns plugin version.
     */
    public function pluginVersion()
    {
        $this->sendResponse(['version' => $this->version]);
    }
}