<?php

// basic crud for time Card
// needs username and password with a pin
// way to keep track of user role, admin or normal
// add time for user and track it, keep track of what days are paid for
// admin to edit time and approve time
// Roles: 1 admin, 2 user

$path = substr($_SERVER['PATH_INFO'], 1);

switch($path) {
    case 'login':
        login();
        break;
    case 'time/track':
        trackTime();
        break;
    case 'times':
        getTimes();
        break;
    case 'install':
        install();
        break;
    case 'create':
        createUser();
        break;
}

function connect() {
    $db = new PDO('sqlite:timeTrack.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, 'SET NAMES utf8');
    return $db;
}

function install() {
    $db = connect();
    $db->exec("CREATE TABLE IF NOT EXISTS Users (Id INTEGER PRIMARY KEY autoincrement, Name TEXT, Pin INTEGER, Role INTEGER)");
    $db->exec("CREATE TABLE IF NOT EXISTS Times (Id INTEGER PRIMARY KEY autoincrement, TimeIn TEXT, TimeOut TEXT, UserId INTEGER, Paid TEXT)");
}

// create user send in 2 params, Name and role. Pin will default to 1111 and will be changed later
function createUser() {
    $db = connect();
    $user_sql = "INSERT INTO Users (
        'Name',
        'Pin',
        'Role'
    ) VALUES (
        :name,
        :pin,
        :role
    )";
    $insert = $db->prepare($user_sql);
    if($insert->execute(array($_GET['name'], 1111, $_GET['role']))) {
        echo '{"userId": ' . $db->lastInsertId() . ', "name": ' . $_GET['name'] . '}';
    } else {
        echo '{"error": "failed to create user"}';
    }
}

// return if valid and login time
function login() {
    $data = json_decode(file_get_contents('php://input'), true);
    $db = connect();
    $name = $data['name'];
    $pin = $data['pin'];
    $userSql = "SELECT Id, Name, Role FROM 'Users' WHERE Name = '$name' AND Pin = '$pin'";
    $rowCount = $db->query($userSql, PDO::FETCH_ASSOC);
    if($rowCount->fetchColumn() > 0) {
        foreach ($db->query($userSql, PDO::FETCH_ASSOC) as $row) {
            echo json_encode($row);
        }
    } else {
        echo '{"error": "Wrong User name or Pin"}';
    }
}

function trackTime() {
    $db = connect();
    $userId = $_GET['userId'];
    // find if there are clocked in times to clock out of.
    $timeSql = "SELECT Id FROM 'Times' WHERE userId = $userId AND TimeIn IS NOT NULL AND TimeOut IS NULL";

    $rowCount = $db->query($timeSql, PDO::FETCH_ASSOC);
    if($rowCount->fetchColumn() > 0) {
        // update row
        $keyId = '';
        foreach ($db->query($timeSql, PDO::FETCH_ASSOC) as $row) {
            $keyId = $row['Id'];
        }
        $review_sql = "UPDATE Times SET TimeOut = ? WHERE id = ?";
        $insert = $db->prepare($review_sql);
        if($insert->execute(array($_GET['time'], $keyId))) {
            echo '{"status": "updated"}';
        } else {
            echo '{"error": "failed to track time"}';
        }
    } else {
        // insert row
        $timeInsert = "INSERT into 'Times' (TimeIn, UserId) VALUES (:timeIn, :userId)";
        $insert = $db->prepare($timeInsert);
        if($insert->execute(array($_GET['time'], $userId))) {
            echo '{"status": "inserted"}';
        } else {
            echo '{"error": "failed to track time"}';
        }
        // insert row
    }
}

function getTimes() {
    $db = connect();
    $userId = $_GET['userId'];
    $userSql = "SELECT * FROM 'Times' WHERE UserId = '$userId' AND Paid IS NULL ORDER BY Id DESC";
    $rowCount = $db->query($userSql, PDO::FETCH_ASSOC);
    if($rowCount->fetchColumn() > 0) {
        $times = [];
        foreach ($db->query($userSql, PDO::FETCH_ASSOC) as $row) {
            $times[] = $row;
        }
        echo json_encode($times);
    } else {
        echo '[]';
    }
}
