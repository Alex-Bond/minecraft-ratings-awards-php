<?php

$config = array(
    'db' => array(
        'driver' => 'mysql',
        'host' => 'localhost',
        'username' => 'root',
        'password' => '',
        'database' => 'minecraft',
    ),
    'security-code' => '',
    'jobs' => array(
        array(
            'active' => true,
            'table-name' => 'iConomy',
            'type' => 0,
            /*
             * # ДОБАВЛЕНИЕ К ЧИСЛЕННОМУ ЗНАЧЕНИЮ
             * 0 - только обновление (пополнение).
             * 1 - вставка нового параметра если нет такой записи (поле user-column дожно быть уникально в схеме (уникальный индекс)).
             * # ВЫСТАВЛЕНИЕ ЗНАЧЕНИЯ
             * 2 - выставления значения
             * 3 - вставка или выставление значения (поле user-column дожно быть уникально в схеме (уникальный индекс)).
             * */
            'change-column' => 'balance',
            'user-column' => 'username',
            'default' => 30, // Стандартный баланс
            'change' => 1 // Бонус или значение
        )
    )
);

if (isset($_GET['hash']) && isset($_GET['player']) && strlen($_GET['hash']) == 32 && strlen($_GET['player']) >= 3 && $_GET['hash'] == md5($config['security-code'] . $_GET['player'])) {
    try {
        $db = new PDO($config['db']['driver'] . ':host=' . $config['db']['host'] . ';dbname=' . $config['db']['database'], $config['db']['username'], $config['db']['password']);

        foreach ($config['jobs'] as $item) {
            if ($item['active'] == true) {
                switch ($item['type']) {
                    case 0:
                    {
                        $query = $db->prepare("UPDATE " . $item['table-name'] . " SET `" . $item['change-column'] . "`=`" . $item['change-column'] . "`+" . $item['change'] . " WHERE `" . $item['user-column'] . "`=?");
                        $query->execute(array($_GET['player']));
                        break;
                    }
                    case 1:
                    {
                        $query = $db->prepare("INSERT INTO " . $item['table-name'] . " (`" . $item['user-column'] . "`,`" . $item['change-column'] . "`) VALUES (?, " . $item['default'] + $item['change'] . ") ON duplicate KEY UPDATE `" . $item['change-column'] . "`=`" . $item['change-column'] . "`+" . $item['change']);
                        $query->execute(array($_GET['player']));
                        break;
                    }
                    case 2:
                    {
                        $query = $db->prepare("UPDATE " . $item['table-name'] . " SET `" . $item['change-column'] . "`=`? WHERE `" . $item['user-column'] . "`=?");
                        $query->execute(array($item['change'], $_GET['player']));
                        break;
                    }
                    case 3:
                    {
                        $query = $db->prepare("INSERT INTO " . $item['table-name'] . " (`" . $item['user-column'] . "`,`" . $item['change-column'] . "`) VALUES (?, ?) ON duplicate KEY UPDATE `" . $item['change-column'] . "`=?");
                        $query->execute(array($_GET['player'], $item['change'], $item['change']));
                        break;
                    }
                }

                if ($db->errorCode() != 0000) {
                    $error_array = $db->errorInfo();
                    die("SQL error: " . $error_array[2] . '');
                }
            }
        }

        echo 'ok';
    } catch (PDOException $e) {
        die('Error: ' . $e->getMessage());
    }
} else {
    die('DO NOT HACK ME!');
}