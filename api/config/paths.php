<?php

// Define absolute paths for key directories
define('ROOT_PATH', dirname(__DIR__, 2));
define('DATA_PATH', ROOT_PATH . '/api/data');
define('FILES_PATH', ROOT_PATH . '/files');

return [
    'data' => DATA_PATH,
    'files' => FILES_PATH,
    'api' => ROOT_PATH . '/api',
    'core' => ROOT_PATH . '/api/core',
    'modules' => ROOT_PATH . '/api/modules',
    'config' => ROOT_PATH . '/api/config',
];
