<?php

namespace Civi\FrameworkBundle;

class CiviCRM {
    static $initialized;

    function __construct($settingsPath = '/var/aegir/platforms/civicrm-4.0/sites/sco.civisys.think.hm/civicrm.settings.php')
    {
        if (self::$initialized) {
            return;
        }

        // set error_reporting to prevent S2 errors
        // error_reporting(3);

        if (! file_exists($settingsPath)) {
            echo "civicrm.settings.php file does not exist here: $settingsPath. Please fix!<p>";
            exit();
        }

        require_once $settingsPath;
        require_once 'CRM/Core/ClassLoader.php';
        \CRM_Core_ClassLoader::singleton()->register();
        \CRM_Core_Config::singleton();

        self::$initialized = 1;
    }
}
