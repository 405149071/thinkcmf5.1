<?php
/**
 *
 * Created by thinkcmf5_1.
 * User: wuzz
 * Date: 2019/7/31
 * Time: 7:08 AM
 */

namespace api\shc\controller;

use api\shc\model\ShcQuestAnswerModel;
use cmf\controller\RestBaseController;

class QuestController extends RestBaseController
{
    // 提交答案
    public function submitAnswer()
    {
        $request =$this ->request;
        $param = $request->param();
//        var_dump(json_decode($param['data'],true));
        $m = new ShcQuestAnswerModel();
        $m->data([
            'name' => $param['name'],
            'answer'    => $param['data'],
            'score' => $param['score'],
            'desc'    => $param['desc'],
            'add_time' => date("Y/m/d h:i:s"),
        ]);
        if($m->save()>0){
            $this->success('处理成功!');
        }else{
            $this->success('处理失败!');
        };
    }
}
