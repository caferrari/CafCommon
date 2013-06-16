<?php

namespace CafCommon;

use CafCommon\Helper\FlashMessages;

class Module
{

    public function getConfig()
    {
        return include __DIR__ . '../../../config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                     __NAMESPACE__ => __DIR__,
                ),
            ),
        );
    }

    public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
                'flashMessage' => function ($sm) {
                    $flashmessenger = $sm->getServiceLocator()
                        ->get('ControllerPluginManager')
                        ->get('flashmessenger');

                    $message = new FlashMessages;
                    $message->setFlashMessager($flashmessenger);
                    return $message ;
                }
            ),
        );
    }

}
