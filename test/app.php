<?php

use Lmfriends\LmfdsFoundation\Model;

require_once __DIR__ . '/../vendor/autoload.php';

$dbInfo = [
  'host' => 'localhost',      // 호스트 주소(localhost, 127.0.0.1)
  'dbname' => 'homestead',    // 데이타 베이스(DataBase) 이름
  'username' => 'homestead',  // DB 아이디
  'password' => 'secret',     // DB 패스워드
  'charset' => 'utf8mb4'      // 문자 인코딩
];

$model = new Model($dbInfo, 'monologs');
var_dump($model->findById(1));
