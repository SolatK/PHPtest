<?php

function ConnectDBAndPush(): bool
{
    $config = require 'src/config.php';
    $mysqlLogs =  fopen('mysql.log', 'a');

    try {
        $connection = new mysqli($config['host'], $config['username'], $config['password'], $config['database']);
    } catch (Exception $e) {
        fwrite($mysqlLogs, date('Y-m-d H:i:s') . ' Failed to connect to MySQL: ' . $e->getMessage() . "\r\n");
    }


    if (isset($connection) && $connection->connect_errno) {
        fwrite($mysqlLogs, date('Y-m-d H:i:s') . ' Failed to connect to MySQL: ' . $mysqli->connect_error . "\r\n");
        return false;
    } else {
        //тут могли быть ваши MySQL запросы
    }

    fclose($mysqlLogs);
    return true;
}

