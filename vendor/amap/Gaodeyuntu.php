<?php
/**
 * 高德云图数据接口
 * Date: 2018-06-22 09:01
 */
namespace amap;

class Gaodeyuntu
{
    private static $key;
    private static $tableid;

    public function __construct($key='', $tableid='') {
        self::$key = "77978e28102a6a5304e26275ef5c5468";
        self::$tableid = "5b2c683aafdf522fe23a9312";
    }
    /**
     * 创建云图数据表
     *
     * @param      <type>  $name   表名
     * @param      <type>  $name   数字前面
     *
     * http://yuntuapi.amap.com/datamanage/table/create
     * 返回值 status info tableid
     */
    public function create_table($name='', $sig='')
    {
        $name = $name ? $name : date('YmdHis');
        $url = "http://yuntuapi.amap.com/datamanage/table/create";

        $data = array(
            'key' => self::$key,
            'name' => $name,
            'sig' => $sig
        );
        $res = $this->https_request($url, $data);

        return $res;
    }

    /**
     * 创建单条位置信息
     *
     * @param      <type>  $loctype   定位方式 1经纬度 2地址
     * @param      <type>  $_location 经纬度
     * @param      <type>  $_address  地址
     * @param      <type>  $_name     数据名称
     * @param      <type>  $coordtype 坐标类型 1: gps 2: autonavi 3: baidu
     * @param      <type>  $sig       数字签名
     * @param      <type>  $diy      用户自定义数据
     *
     * http://yuntuapi.amap.com/datamanage/data/create
     * 返回值 status info _id
     */
    public function deal_single_data($postArr, $isUpdate=false)
//    public function deal_single_data($loctype=1, $_location='', $_address='', $_name='', $coordtype='3', $sig='', $diy=array(), $isUpdate=false)
    {
        $url = 'http://yuntuapi.amap.com/datamanage/data/create';
        if($isUpdate){
            $url = 'http://yuntuapi.amap.com/datamanage/data/update';
        }
        //基础信息 仅处理经纬度方式

        $res =$this -> https_request($url, $postArr);

        return $res;
    }

    /**
     * 暂无用 使用高德后台上传
     */
    public function add_multi_data()
    {

    }

    /**
     * 删除单条/多条数据
     *
     * @param      string  $ids      数据1-50条 _id 1,3,4
     * @param      string  $sig      The signal
     *
     * 返回值 status info success fail成功/失败条数
     */
    public function delete_data($data)
    {
        $url = "http://yuntuapi.amap.com/datamanage/data/delete";

        $res = $this->https_request($url, $data);
//        $res = json_decode($res,true);
        return $res;
    }

    /**
     * 获取批量处理进度 暂无用
     */
    public function get_import_status()
    {

    }

    /**
     * 云检索 本地检索
     *
     * @param      string  $keywords 搜索关键词
     * @param      string  $city     中文城市
     * @param      string  $filter   过滤条件
     * @param      string  $sortrule 排序规则
     * @param      string  $limit    分页条数
     * @param      string  $page     当前页
     * @param      string  $sig      数字签名
     */
    public function search_local($keywords=' ', $city='全国', $filter='', $sortrule='', $limit=10, $page=1, $sig='')
    {
        $url = 'http://yuntuapi.amap.com/datasearch/local';
        $data = array(
            'key' => self::$key,
            'tableid' => self::$tableid,
            'keywords' => $keywords,
            'city' => $city,
            'filter' => $filter,
            'sortrule' => $sortrule,
            'limit' => $limit,
            'page' => $page,
            'sig' => $sig
        );

        $res = $this->https_request($url, $data, true);

        return $res;
    }

    /**
     * 云检索 周边检索
     *
     * @param      string  $keywords 搜索关键词
     * @param      string  $center   中心经纬度
     * @param      string  $radius   查询半径
     * @param      string  $filter   过滤条件
     * @param      string  $sortrule 排序规则
     * @param      string  $limit    分页条数
     * @param      string  $page     当前页
     * @param      string  $sig      数字签名
     */
    public function search_around($data)
    {
//        $url = 'http://yuntuapi.amap.com/datasearch/around';
        $url = 'http://yuntuapi.amap.com/nearby/around';
                
        $res = $this->https_request($url, $data, true);

        return $res;
    }

    /**
     * 多边形检索 暂无用
     */
    public function search_polygon()
    {

    }

    /**
     * 云检索 id检索（poi详情检索）
     *
     * @param      string  $_id      地图数据id
     * @param      string  $sig      数字签名
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function search_id($data)
    {
        $url = 'http://yuntuapi.amap.com/datasearch/id';

        $res = $this->https_request($url, $data, true);

        return $res;
    }


    /**
     * 云检索 按条件检索数据（可遍历整表数据）
     *
     * @param      string  $filter   过滤条件
     * @param      string  $sortrule 排序规则
     * @param      string  $limit    分页条数
     * @param      string  $page     当前页
     * @param      string  $sig      数字签名
     */
    public function search_by_condition($data)
    {
        $url = 'http://yuntuapi.amap.com/datamanage/data/list';

        $res = $this->https_request($url, $data, true);

        return $res;
    }

    /**
     * 云检索 数据分布检索
     *
     * @param      string  $keywords 搜索关键词
     * @param      string  $center   中心经纬度
     * @param      string  $radius   查询半径
     * @param      string  $filter   过滤条件
     * @param      string  $sortrule 排序规则
     * @param      string  $limit    分页条数
     * @param      string  $page     当前页
     * @param      string  $sig      数字签名
     */
    public function search_area_count($keywords=' ', $type='', $country='中国', $province='', $city='', $filter='', $sig='', $callback='cb')
    {
        $data = array(
            'key' => self::$key,
            'tableid' => self::$tableid,
            'filter' => $filter,
            // 'callback' => $callback,
            'sig' => $sig
        );
        switch ($type) {
            case 'province':
                $url = 'http://yuntuapi.amap.com/datasearch/statistics/province';
                $data['country'] = $country;
                break;
            case 'city':
                $url = 'http://yuntuapi.amap.com/datasearch/statistics/city';
                $data['province'] = $province;
                break;
            case 'district':
                $url = 'http://yuntuapi.amap.com/datasearch/statistics/district';
                $data['province'] = $province;
                $data['city'] = $city;
                break;

            default:
                $url = '';
                break;
        }

        $res = $this->https_request($url, $data, true);

        return $res;
    }





    /**
     * curl
     *
     * @param      <type>  $url    The url
     * @param      <type>  $data   The data
     * @param      <type>  $isget  是否为get请求
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function https_request($url, $data = null, $isget = false)
    {
        $headers = array(
            'Cache-Control:no-cache',
            'Pragma:no-cache'
        );

        if($isget){
            $url .= '?';
            if(is_array($data)){
                $url .= http_build_query($data);
            }elseif(is_string($data)) {
                $url .= $data;
            }
        }

//        var_dump($url);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_TIMEOUT,3);
        if (!empty($data) && !$isget){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }

}