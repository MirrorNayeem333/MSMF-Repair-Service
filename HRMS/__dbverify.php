<?php
include 'connection.php';
$r = mysqli_query($con, 'SELECT DATABASE() AS db');
$row = $r ? mysqli_fetch_assoc($r) : null;
echo $row ? ('DB=' . $row['db']) : 'NO_DB';
