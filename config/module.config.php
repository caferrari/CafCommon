<?php

namespace CafCommon;

return array(
    'router' => array(
        'routes' => array(
            'crud' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/:controller[/:action][?id=:id]',
                    'defaults' => array(
                        'action' => 'index',
                        'id' => null,
                    ),
                    'constraints' => array(
                        'id' => '[0-9]+',
                    )
                ),
                'priority' => 0
            ),
        )
    ),
    'view_helpers' => array(
        'invokables' => array(
            'bootstrapRow' => 'CafCommon\Helper\BootstrapRow',
            'FlashMessages' => 'CafCommon\Helper\FlashMessages',
        )
    )
);
