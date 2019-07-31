<?php
/**
 *
 * Created by thinkcmf5_1.
 * User: wuzz
 * Date: 2019/7/31
 * Time: 6:51 AM
 */

namespace app\shc\controller;

use cmf\controller\HomeBaseController;

class QuestController extends HomeBaseController
{

    public function index(){
        var_dump(date("Y/m/d h:i:s"));
    }


    public function show()
    {
//        exit("haha");
//        if(cmf_is_mobile()){
            return $this->fetch();
//        }else{
//            exit("本游戏只支持手机");
//        }

    }



}
