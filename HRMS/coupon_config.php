<?php

function coupon_map() {
    return [
        'FIX10' => 10
    ];
}

function normalize_coupon_code($code) {
    return strtoupper(trim((string) $code));
}

function coupon_discount_percent($code) {
    $code = normalize_coupon_code($code);
    $map = coupon_map();
    return isset($map[$code]) ? (float) $map[$code] : 0.0;
}

