<?php

/**
 * Get an item from an array based on a key
 *
 * @param array $array
 * @param string $key
 * @param mixed $default
 * @return mixed
 */
function array_get(array $array, $key, $default = null) {
    if (isset($array[$key])) {
        return $array[$key];
    }

    return $default;
}

function head(array $array) {
    return reset($array);
}

function tail(array $array) {
    return end($array);
}
