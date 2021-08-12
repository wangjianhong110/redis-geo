<?php

require "vendor/autoload.php";

$q = \Wangjianhong\RedisGeo\Nearby::getInstance('127.0.0.1', 6379, '', 1);

//添加
$res = $q->addMember('beijing', 13.361389, 38.115556, 'wangjianhong');

//if ($res >= 0)
//{
//    echo '添加成功'."\r\n";
//}else
//{
//    echo '添加失败'."\r\n";
//}
//获取用户经纬度

//$lngAndlat = $q->getMemberLocation('beijing', 'wangjianhong');

//print_r($lngAndlat);


//获取两个人位置距离
$q->addMember('beijing', 15.087269, 37.502669, 'liuxiongxiong');

//$distance = $q->getMemberDistance('beijing', 'wangjianhong', 'liuxiongxiong', 'km');

//两个人之间距离 默认为m
//echo $distance;

//根据经纬度获取附近200km的用户以及经纬度
//$nearby_people = $q->getLngAndLatNearby('beijing', 15, 37, 300, 'km');
//var_dump($nearby_people);

//根据用户获取附近 200km的用户
$nearby_member = $q->getMemberNearby('beijing', 'wangjianhong', 200, 'km');

var_dump($nearby_member);
