<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2014 Zend Technologies USA Inc. (http://www.zend.com)
 */

/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

return array(
    // 'db' => array(
    //     'driver'   => 'PDO_Mysql',
    //     'database' => 'sql10208214',
    //     'user'     => 'sql10208214',
    //     'password' => 'jSdBZ7Su8J',
    //     'host'     => 'sql10.freesqldatabase.com',
    // ),
    'db' => array(
        'driver'   => 'PDO_Mysql',
        'database' => 'torneo',
        'user'     => 'root',
        'password' => 'frutill4s',
//        'host'     => 'dario-casa.sytes.net',
    ),
    'service_manager' => array(
        'factories' => array(
            'Zend\Db\Adapter\Adapter'
            => 'Zend\Db\Adapter\AdapterServiceFactory',
        ),
    ),

);
