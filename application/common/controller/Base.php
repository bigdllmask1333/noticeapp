<?php

namespace app\common\controller;

use think\Controller;
use think\Request;
use think\Db;

class Base extends Controller
{
    /**
     * 显示资源列表
     * 登录后验证的基类
     * @return \think\Response
     * http://127.0.0.1/fastadmin/public/api/v1/index
     */

    protected function _initialize()
    {

        echo 123;
    }

    public function index1()
    {
        echo 12222222223;
        //
    }
}
