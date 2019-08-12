<?php
/**
 *
 * Created by thinkcmf5_1.
 * User: wuzz
 * Date: 2019/8/3
 * Time: 8:53 AM
 */
namespace app\shc\controller;

use cmf\controller\HomeBaseController;

class DiscController extends HomeBaseController
{
    function show(){
//        if(cmf_is_mobile()){
            return $this->fetch();
//        }else{
//            exit("本测试只提供手机版");
//        }
    }

    function code(){
//        return $this->fetch();
        if(cmf_is_mobile()){
            return $this->fetch();
        }else{
            exit("本测试只提供手机版");
        }
    }

}