<?php
$version = '5.7.0';

if (isset($GLOBALS['version'])) {
    $version = $GLOBALS['version'];
}

return [
    'version' => $version
];
