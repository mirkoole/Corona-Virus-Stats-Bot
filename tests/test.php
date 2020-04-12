<?php

// ha ha, don't expect real tests here

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../public/restapi.php";
#require_once __DIR__ . "/../public/settings.php";
require_once __DIR__ . "/../public/log.php";
require_once __DIR__ . "/../public/helper.php";
require_once __DIR__ . "/../public/stats.php";

echo get_country_status('DE');
