<?php

function InfromatikaDlyaEblanov ($value) {
    $result = null;
    while ($value > 0) {
        $value /= 2;
        if (gettype($value) === 'integer') {
            $result .= '0';
        }
        elseif (gettype($value) === 'double') {
            $result .= '1';
            $value = round($value, 0, PHP_ROUND_HALF_DOWN);
            settype ($value, 'int');
        }
    }
    $result = strrev($result);
    return $result;
} 

echo InfromatikaDlyaEblanov(60863);
?>