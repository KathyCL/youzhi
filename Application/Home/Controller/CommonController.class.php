<?php
/**
 * Created by PhpStorm.
 * User: edjgf
 * Date: 2018/3/5
 * Time: 16:23
 */

namespace Home\Controller;
use Think\Controller;

class CommonController extends Controller
{

    public function post($url,$post)
    {

        //请求参数
        $option=array(

            'http'=>array(

                'method'=>'POST',

                'content'=>http_build_query($post)
            )
        );

        //获取返回数据
        $data=file_get_contents($url,false,stream_context_create($option));

        //json数据转数组
        $data=(array)json_decode($data);

        return $data;

    }
}