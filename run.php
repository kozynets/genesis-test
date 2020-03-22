<?php declare(strict_types=1);

if (defined('STDIN')) {
    $mainParams = [];
    foreach ($argv as $one) {
        if (!preg_match('|^--(.*?)=|', $one, $key) ||
            !preg_match('|=(.*?)$|', $one, $value)) {
            continue;
        }
        $mainParams[$key[1]] = $value[1];
    }
    $additionalParams = file_get_contents('php://stdin');
    $result = ($additionalParams) ? array_merge($mainParams, \json_decode($additionalParams, true)) : $mainParams;
} else {
    $mainParams = $_GET;
    $additionalParams = json_decode(file_get_contents('php://input'), true);
    $result = ($additionalParams) ? array_merge($additionalParams, $mainParams) : $mainParams;
}

var_dump($result);