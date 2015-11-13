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
        getTimea();
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
    $db->exec("CREATE TABLE IF NOT EXISTS Times (Id INTEGER PRIMARY KEY autoincrement, TimeIn TEXT, TimeOut TEXT, UserId INTEGER)");
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
        echo '{userId: ' . $db->lastInsertId() . ', name: ' . $_GET['name'] . '}';
    } else {
        echo '{error: "failed to create user"}';
    }
}

// return if valid and login time
function login() {
    echo 'hello login';
}

function trackTime() {
    echo 'hello timesd';
}

function getTimes() {
    $db = connect();
    $userId = $_GET['user'];
    $result = $db->query("SELECT * FROM Times Where UserId = '$userId'");

}
