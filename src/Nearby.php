<?php


namespace Wangjianhong\RedisGeo;


class Nearby
{
    private static $instance;

    private static $redis;

    /**
     * 实例化redis
     * @param string $host
     * @param int $port
     * @param string $password
     * @param int $select
     * @return Nearby
     */
    public static function getInstance($host = '127.0.0.1', $port = 6379, $password = '', $select = 0)
    {
        if (!self::$instance instanceof self){
            self::$redis = new \Redis();
            self::$redis->connect($host, $port);
            if (!empty($password)) self::$redis->auth($password);
            self::$redis->select($select);
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     * 为用户添加经纬度位置 (单个用户添加) 一个用户可对应多个 经纬度 和 位置
     * @param $key  用户位置（如北京,上海）
     * @param $longitude 经度
     * @param $latitude 维度
     * @param $member 用户标识 （UID,token）
     * @return bool
     */
    public function addMember( $key, $longitude, $latitude, $member)
    {
        $redis = self::$redis;

        //为key添加位置信息
        return $redis->geoadd($key,$longitude,$latitude,$member);

    }

    /**
     * 获取用户位置的经纬度
     * @param $key  用户位置（如北京,上海）
     * @param $member 用户标识 （UID,token）可多个
     * @return mixed
     */
    public function getMemberLocation($key, $member)
    {
        $redis = self::$redis;

        return $redis->GEOPOS($key, $member); //否返回nil
    }

    /**
     * 返回两个给定位置之间的距离。如果两个位置之间的其中一个不存在， 那么命令返回空值。
     *   指定单位的参数 unit 必须是以下单位的其中一个：
     *   m 表示单位为米、km 表示单位为千米、mi 表示单位为英里、ft 表示单位为英尺。
     * @param $key
     * @param $member1
     * @param $member2
     * @return mixed
     */
    public function getMemberDistance($key, $member1, $member2, $unit = 'm')
    {
        $redis = self::$redis;

        return $redis->GEODIST($key, $member1, $member2, $unit);//否返回nil
    }

    /**根据地区输入经纬度 获取附近几千米的人
     * @param $key 地区 如北京
     * @param $longitude 经度
     * @param $latitude 维度
     * @param $radius 几公里
     * @param $unit 计量单位 m(米) km(千米) mi(英里) ft(英尺)
     * @param $WITHDIST 获取用户名以及离中心经纬度距离
     * @param $WITHCOORD  获取用户名以及经纬度 建议$WITHDIST $WITHCOORD 一起写
     * @param $limit 返回多少条数据
     * @param $order asc 查找结果根据距离从近到远排序。 DESC: 查找结果根据从远到近排序
     */
    public function getLngAndLatNearby($key, $longitude, $latitude, $radius, $unit)
    {
        $redis = self::$redis;
        $param = array('georadius', $key,  $longitude, $latitude, $radius, $unit, 'WITHDIST', 'WITHCOORD');
        $ret = call_user_func_array(array($redis, 'rawCommand'), $param);
        return $ret;
    }


    /**
     *
     * @param $key 如 北京
     * @param $member 用户
     * @return mixed
     */
    public function getMemberNearby($key, $member, $radius, $unit)
    {
        $redis = self::$redis;
        $param = array('GEORADIUSBYMEMBER', $key,  $member, $radius, $unit, 'WITHDIST', 'WITHCOORD');
        $ret = call_user_func_array(array($redis, 'rawCommand'), $param);
        return $ret;
    }
}