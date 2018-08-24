<?php

namespace app\index\controller;

use think\Controller;
use think\Request;

class Upload extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        return $this->fetch();
        //
    }

    /**
     * 头像上传
     *
     * @return \think\Response
     */
    public function upimg()
    {
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('image');
        // 移动到框架应用根目录/public/uploads/ 目录下
        if($file){
            $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
            if($info){
                // 成功上传后 获取上传信息
                // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
                $str=$info->getSaveName();
                $fg= substr($str , 8 , 1); /*分隔符*/
                $cc1=explode($fg,$str); /*分割结果*/
//                echo 'http://apis.com/'.'uploads/'.$cc1[0].'/'.$cc1[1];
                $retimg='/uploads/'.$cc1[0].'/'.$cc1[1];

                $ret['code']=1;
                $ret['message']='success';
                $ret['data']=$retimg;

            }else{
                $ret['code']=0;
                $ret['message']='error';
                $ret['data']=$file->getError();
                // 上传失败获取错误信息
            }
        }else{
            $ret['code']=0;
            $ret['message']='not file';
            $ret['data']='{}';
        }

        return json($ret);
        //
    }


    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }
}
