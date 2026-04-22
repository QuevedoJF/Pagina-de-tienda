<?php
$con = new mysqli('localhost', 'root', '1234', 'tienda');
$result = $con->query('DESCRIBE usuario');
while ($row = $result->fetch_assoc()) {
    echo $row['Field'] . "\n";
}
$con->close();