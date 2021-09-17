<?php

function ezPassword($length) {
    $small_letters = implode (range ('a', 'z'));
    $Big_letters = implode (range ('A', 'Z'));
    $all_symbols = '0123456789' . $small_letters . $Big_letters;
    $symbol_value = strlen($all_symbols);
    $symbol_value--;
    $value = null;
    for ($i = 0; $i < $length; $i++) {
        $value .= $all_symbols[rand(0, $symbol_value)];
    }
    return $value;
}

echo ezPassword(99999);
?>