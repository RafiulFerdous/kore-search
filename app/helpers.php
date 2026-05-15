<?php

if (!function_exists('removeQuery')) {
    function removeQuery(array|string $keys): string
    {
        $keys = (array) $keys;
        $params = request()->query();
        foreach ($keys as $key) {
            unset($params[$key]);
        }
        $params['page'] = 1;
        return url()->current() . '?' . http_build_query($params);
    }
}
