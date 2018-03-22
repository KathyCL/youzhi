<?php
/**
 * Created by PhpStorm.
 * User: edjgf
 * Date: 2018/3/5
 * Time: 16:22
 */

namespace Home\Controller;


class AdminController extends CommonController
{

    public function getMsg()
    {
        //获取数据转成数组

        $data=json_decode(file_get_contents('php://input'),true);

        $data=(array)$data[0];

        //提取数据包

        $min=unserialize($data['data']);

        //删除任务表和数据包的自增id

        unset($min['id']);

        unset($data['id']);

        //存任务表

        $task=M('lic_burnk_task');

        $re= $task->data($data)->add();

        //如果是订单，存订单表和商品表

        if($data['type_table']=='order'){

            //商品数据

            $goods=$min['ordergoods'];

            unset($min['ordergoods']);

            //数据包存订单表

            $order=M('lic_order');

            $reor = $order->data($min)->add();

            //商品存商品表

            $order_goods=M('lic_order_goods');

            foreach($goods as $k=>$v){

                unset($v['id']);

             $regoods = $order_goods->data($v)->add();
            }

        }elseif ($data['type_table']=='dy_group'){

            //存导游表

            $dy = M('lic_dy_group');

            $redy = $dy->data($min)->add();

        }elseif ($data['type_table']=='yk_member') {

            //存用户表

            $dy = M('lic_yk_member');

            $reyk =  $dy->data($min)->add();

        }
        if($re){

            if(($reor && $regoods) || $redy || $reyk){

                $back=array('bguid'=>$data['bguid'],'msg'=>'成功');

            }else{

                $back=array('bguid'=>$data['bguid'],'msg'=>'添加数据失败');

            }

        }else{

            $back=array('bguid'=>$data['bguid'],'msg'=>'添加任务失败');

        }

        echo json_encode($back);

    }


    public function test()
    {
        //连接本地的 Redis 服务

        $redis = new \Redis();

        $redis->connect('127.0.0.1', 6379);

        //设置 redis 字符串数据

        $redis->set("test-name", "one test msg");

        // 获取存储的数据并输出

        echo $redis->get("test-name");

    }


    public function info()
    {
        phpinfo();
    }

    public function excel()
    {

        $data=array();
        $data[]=array('标题1','标题2','标题3','标题4','标题5');
        $data[]=array(111,222,333,444,555);
        $data[]=array(999,888,777,666,444);
        $data[]=array(666,123,456,678,443);
        $filename='测试表';
        Eexcel($filename,'测试测试',$data,0,20);


    }

}