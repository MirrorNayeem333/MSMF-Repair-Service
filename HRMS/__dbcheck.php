<?php
mysqli_report(MYSQLI_REPORT_OFF);
$c = @mysqli_connect('localhost', 'root', '', 'DBMS');
if (!$c) {
    echo mysqli_connect_error();
    exit(1);
}
echo 'OK';
