<?php

/*
 * MCTOP VOTING BONUS CLIENT
 * Version 1.2
 * Created by Alex Bond
 */

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
            'change-column' => 'balance',
            'user-column' => 'username',
            'change' => 1
        ),
        array(
            'active' => true,
            'table-name' => 'users-groups',
            'type' => 3,
            'change-column' => 'group',
            'user-column' => 'username',
            'change' => 'voted'
        ),
        array(
            'active' => true,
            'table-name' => 'items',
            'type' => 4,
            'advanced' => true,
            'fields' => array(
                'username' => '{player}',
                'item' => '1',
                'item-count' => 64
            )
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
                        $select = $db->prepare("SELECT * FROM " . $item['table-name'] . " WHERE `" . $item['user-column'] . "`=?");
                        $select->execute(array($_GET['player']));
                        if ($select->fetchColumn() > 0)
                            $query = $db->prepare("UPDATE " . $item['table-name'] . " SET `" . $item['change-column'] . "`=`" . $item['change-column'] . "`+" . $item['change'] . " WHERE `" . $item['user-column'] . "`=?");
                        else
                            $query = $db->prepare("INSERT INTO " . $item['table-name'] . " (`" . $item['user-column'] . "`,`" . $item['change-column'] . "`) VALUES (?, " . (intval($item['default']) + intval($item['change'])) . ")");
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
                        $select = $db->prepare("SELECT * FROM " . $item['table-name'] . " WHERE `" . $item['user-column'] . "`=?");
                        $select->execute(array($_GET['player']));
                        if ($select->fetchColumn() > 0) {
                            $query = $db->prepare("UPDATE " . $item['table-name'] . " SET `" . $item['change-column'] . "`=? WHERE `" . $item['user-column'] . "`=?");
                            $query->execute(array($item['change'], $_GET['player']));
                        } else {
                            $query = $db->prepare("INSERT INTO " . $item['table-name'] . " (`" . $item['user-column'] . "`,`" . $item['change-column'] . "`) VALUES (?, ?)");
                            $query->execute(array($_GET['player'], $item['change']));
                        }
                        break;
                    }
                    case 4:
                    {
                        if ($item['advanced']) {
                            $exec = array();
                            $columns = '';
                            $values = '';
                            foreach ($item['fields'] as $k => $a) {
                                if (!empty($columns))
                                    $columns .= ', `' . $k . '`';
                                else {
                                    $columns .= '`' . $k . '`';
                                }
                                if (!empty($values))
                                    $values .= ', ?';
                                else {
                                    $values .= '?';
                                }
                                $exec[] = str_replace('{player}', $_GET['player'], $a);
                            }
                            $query = $db->prepare("INSERT INTO " . $item['table-name'] . " (" . $columns . ") VALUES (" . $values . ")");
                            $query->execute($exec);
                        } else {
                            $query = $db->prepare("INSERT INTO " . $item['table-name'] . " (`" . $item['user-column'] . "`,`" . $item['change-column'] . "`) VALUES (?, ?)");
                            $query->execute(array($_GET['player'], $item['change']));
                        }
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