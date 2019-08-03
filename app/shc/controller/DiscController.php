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

    }

    function discResult(){
        return $this->fetch();
    }
}