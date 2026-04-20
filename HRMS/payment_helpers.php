<?php

function ensure_services_payment_columns($con) {
    $required = [
        'payment_method' => "ALTER TABLE services ADD COLUMN payment_method VARCHAR(20) NULL DEFAULT NULL",
        'payment_ref'    => "ALTER TABLE services ADD COLUMN payment_ref VARCHAR(120) NULL DEFAULT NULL",
        'paid_at'        => "ALTER TABLE services ADD COLUMN paid_at DATETIME NULL DEFAULT NULL"
    ];

    $checkSql = "SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'services'";
    $result = mysqli_query($con, $checkSql);
    if (!$result) {
        return;
    }

    $existing = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $existing[$row['COLUMN_NAME']] = true;
    }

    foreach ($required as $column => $alterSql) {
        if (!isset($existing[$column])) {
            @mysqli_query($con, $alterSql);
        }
    }
}

function payment_method_label($method) {
    $method = strtolower(trim((string) $method));
    if ($method === 'online') return 'Online Payment';
    if ($method === 'cod') return 'Cash on Delivery';
    return 'Not Selected';
}

