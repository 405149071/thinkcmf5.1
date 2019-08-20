<?php
/**
 *
 * Created by thinkcmf5_1.
 * User: wuzz
 * Date: 2019/8/3
 * Time: 8:53 AM
 */
namespace api\shc\controller;

use api\shc\model\ShcCodeModel;
use api\shc\model\ShcDiscCodeModel;
use api\shc\model\ShcDiscDetailModel;
use api\shc\model\ShcDiscResultModel;
use cmf\controller\RestBaseController;
use think\Db;

class pos{
    var $h=0;
    var $d1=0;
    var $i1=0;
    var $s1=0;
    var $c1=0;
    var $d2=0;
    var $i2=0;
    var $s2=0;
    var $c2=0;
    var $d3=0;
    var $i3=0;
    var $s3=0;
    var $c3=0;

    function __construct($h,$d1,$i1,$s1,$c1,$d2,$i2,$s2,$c2,$d3,$i3,$s3,$c3)
    {
        $this->h=$h;
        $this->d1=$d1;
        $this->i1=$i1;
        $this->s1=$s1;
        $this->c1=$c1;
        $this->d2=$d2;
        $this->i2=$i2;
        $this->s2=$s2;
        $this->c2=$c2;
        $this->d3=$d3;
        $this->i3=$i3;
        $this->s3=$s3;
        $this->c3=$c3;
    }
}

class disc{
    var $d=0; // 每个disc出现的个数
    var $d_p=0; //位置，相对于（-28，28）
    var $d_px=0; // px位置，图中实际位置
    var $dl=0;  // 右侧对应的区域，为左侧对应区域的/4 ，ceil
    var $i=0;
    var $i_p=0;
    var $i_px=0;
    var $il=0;
    var $s=0;
    var $s_p=0;
    var $s_px=0;
    var $sl=0;
    var $c=0;
    var $c_p=0;
    var $c_px=0;
    var $cl=0;
}

class DiscController extends RestBaseController
{


    /**
     *  生成最后结果图表
     * @throws \think\Exception\DbException
     */
    function discResult(){
         $authcode = $this->request->param('authcode');
         if(!$authcode){
             return $this->error("未知错误123");
         }else{
             // 校验授权码正确性
            if(!$this->authcodeOK($authcode)){
                return $this->error("验证码失效");
            }
         }

         $datajson = $this->request->param('data');
         $data =json_decode($datajson);
//        var_dump($data);

        if(!$data)
        {
            return $this->error("未知错误456");
        }
        // 记录数据库日志
        $detail = new ShcDiscDetailModel();
        $detail->data([
            'code' => $authcode,
            'result'    => $datajson,
            'add_time' => date("Y-m-d H:i:s",time()),
        ]);
        $detail->save();

        //1，2，3，4答案对应的数据库里的真实答案（以角个数定）
        $dbanswer = DB::name('shc_disk')->all();

        // 找到真的答案
        foreach($data as $key => $value){
//            var_dump($key); // 0 , 1,2
//            var_dump($value); //  'm' => int 'l' => int 2
//            var_dump($dbanswer[$key]["m".$value->m]);  // dbanswer 序号id与题目相同
            $value->m =$dbanswer[$key]["m".$value->m];
            $value->l =$dbanswer[$key]["l".$value->l];
        }

// 分别计算真答案的个数         // Z-2，三角-3，正方-4 五星-5 N-0
        $dm = new disc();
        $dl = new disc();
        foreach($data as $key => $value){
//            var_dump($value->m . "," .$value->l);
            switch ($value->m)
            {
                case 2:
                    $dm->d++;
                    break;
                case 3:
                    $dm->s++;
                    break;
                case 4:
                    $dm->i++;
                    break;
                case 5:
                    $dm->c++;
                    break;
                default:
                    break;
            };
            switch ($value->l)
            {
                case 2:
                    $dl->d++;
                    break;
                case 3:
                    $dl->s++;
                    break;
                case 4:
                    $dl->i++;
                    break;
                case 5:
                    $dl->c++;
                    break;
                default:
                    break;
            };
        }

        $dml = new disc();
        $dml->d=$dm->d-$dl->d;
        $dml->i=$dm->i-$dl->i;
        $dml->s=$dm->s-$dl->s;
        $dml->c=$dm->c-$dl->c;

//        var_dump($dm);
//        var_dump($dl);
//        var_dump($dml);
//        var_dump("most值为：d=".$dm->d.",i=".$dm->i.",s=".$dm->s.",c=".$dm->c."<br/>");
//        var_dump("least值为：d=".$dl->d.",i=".$dl->i.",s=".$dl->s.",c=".$dl->c."<br/>");
//        var_dump("差异值为：d=".$dml->d.",i=".$dml->i.",s=".$dml->s.",c=".$dml->c."<br/>");
////        // 位置数组，一共（-28，28）个数，数据表示该列在id=9时对应的坐标轴的高度
        $pos=array();
        $pos["-28"]= new pos(0,0,0,0,0,0,0,0,0,0,0,0,0);
        $pos["-27"]= new pos(0,0,0,0,0,0,0,0,0,1,0,1,0);
        $pos["-26"]= new pos(0,0,0,0,0,0,0,0,0,1+1/5.5,1,1+1/10,1);
        $pos["-25"]= new pos(0,0,0,0,0,0,0,0,0,1+2/5.5,1+1/8.5,1+2/10,1+1/10);
        $pos["-24"]= new pos(0,0,0,0,0,0,0,0,0,1+3/5.5,1+2/8.5,1+3/10,1+2/10);
        $pos["-23"]= new pos(0,0,0,0,0,0,0,0,0,1+4/5.5,1+3/8.5,1+4/10,1+3/10);
        $pos["-22"]= new pos(0,0,0,0,0,0,0,0,0,1+5/5.5,1+4/8.5,1+5/10,1+4/10);
        $pos["-21"]= new pos(0,0,0,0,0,0,0,0,0,1+6/5.5,1+5/8.5,1+6/10,1+5/10);
        $pos["-20"]= new pos(0,0,0,0,0,0,0,0,0,1+7/5.5,1+6/8.5,1+7/10,1+6/10);
        $pos["-19"]= new pos(0,0,0,0,0,0,0,0,0,1+8/5.5,1+7/8.5,1+8/10,1+7/10);
        $pos["-18"]= new pos(0,0,0,0,0,0,0,0,0,1+9/5.5,1+8/8.5,1+9/10,1+8/10);
        $pos["-17"]= new pos(0,0,0,0,0,0,0,0,0,1+10/5.5,1+9/8.5,1+10/10,1+9/10);
        $pos["-16"]= new pos(0,0,0,0,0,0,0,0,0,3,1+10/8.5,1+11/10,1+10/10);
        $pos["-15"]= new pos(0,0,0,0,0,0,0,0,0,4,1+11/8.5,1+12/10,1+11/10);
        $pos["-14"]= new pos(0,0,0,0,0,0,0,0,0,5.5,1+12/8.5,1+13/10,1+12/10);
        $pos["-13"]= new pos(0,0,0,0,0,0,0,0,0,7,1+13/8.5,1+14/10,1+13/10);
        $pos["-12"]= new pos(0,0,0,0,0,0,0,0,0,8,1+14/8.5,1+15/10,1+14/10);
        $pos["-11"]= new pos(0,0,0,0,0,0,0,0,0,9,1+15/8.5,1+16/10,2.5);
        $pos["-10"]= new pos(0,0,0,0,0,0,0,0,0,10,1+16/8.5,1+17/10,3.25);
        $pos["-9"]= new pos(0,0,0,0,0,0,0,0,0,11,3,1+18/10,4);
        $pos["-8"]= new pos(0,0,0,0,0,0,0,0,0,12,3+1/3,1+19/10,5);
        $pos["-7"]= new pos(0,0,0,0,0,0,0,0,0,14,3+2/3,3,6.5);
        $pos["-6"]= new pos(0,0,0,0,0,0,0,0,0,15,4,3+1/1.5,8);
        $pos["-5"]= new pos(0,0,0,0,0,0,0,0,0,16,5.5,3+2/1.5,9.5);
        $pos["-4"]= new pos(0,0,0,0,0,0,0,0,0,17.5,7,5,11);
        $pos["-3"]= new pos(0,0,0,0,0,0,0,0,0,18.25,8,6,12);
        $pos["-2"]= new pos(0,0,0,0,0,0,0,0,0,19,9.5,7,14.5);
        $pos["-1"]= new pos(0,0,0,0,0,0,0,0,0,20,11,8,16);
        $pos["0"]= new pos(0,3,1,1,1,28,28,28,28,21,12,10,18);
        $pos["1"]= new pos(0,6,2,2,2,27.5,26.5,26.5,27,22,14,11.5,18.75);
        $pos["2"]= new pos(0,9.5,4,3,4,26.5,25,22,26,22.5,15.5,12,19.5);
        $pos["3"]= new pos(0,12,6.5,4,7,26,23,18,25,23.5,17.5,13,21);
        $pos["4"]= new pos(0,14,9.5,7,10,25,20,15,23.5,24,19,13.75,22);
        $pos["5"]= new pos(0,16,12,9.5,13,24,16,12,21.5,24.5,20,14.5,23.5);
        $pos["6"]= new pos(0,19,15,11,16,22,12,9.5,19.5,25,22,17,25);
        $pos["7"]= new pos(0,20,16,13,19.5,21,10,7.5,16,25+1/3,23.5,18.5,25+1/6);
        $pos["8"]= new pos(0,21.5,20,15,21,19.5,7.25,6,13,25+2/3,25,20,25+2/6);
        $pos["9"]= new pos(0,23,21.5,17,23,18,4,4,12,26,26,21,25+3/6);
        $pos["10"]= new pos(0,24,23.5,19,24,14,3,2,8,26+1/9,26+1/9.5,22,25+4/6);
        $pos["11"]= new pos(0,25,25,21,25.5,12,3-1/8,2-1/17,5.5,26+2/9,26+2/9.5,23.5,25+5/6);
        $pos["12"]= new pos(0,26,26,23.5,27,11,3-2/8,2-2/17,4,26+3/9,26+3/9.5,25,25+6/6);
        $pos["13"]= new pos(0,26+1/7.5,26+1/8,25,27+1/12,9.5,3-3/8,2-3/17,3,26+4/9,26+4/9.5,26,25+7/6);
        $pos["14"]= new pos(0,26+2/7.5,26+2/8,26.5,27+2/12,7,3-4/8,2-4/17,3-1/6.5,26+5/9,26+5/9.5,27,25+8/6);
        $pos["15"]= new pos(0,26+3/7.5,26+3/8,26.5+1/8,27+3/12,5.5,3-5/8,2-5/17,3-2/6.5,26+6/9,26+6/9.5,27+1/12,25+9/6);
        $pos["16"]= new pos(0,26+4/7.5,26+4/8,26.5+2/8,27+4/12,4,3-6/8,2-6/17,3-3/6.5,26+7/9,26+7/9.5,27+2/12,25+10/6);
        $pos["17"]= new pos(0,26+5/7.5,26+5/8,26.5+3/8,27+5/12,3,3-7/8,2-7/17,3-4/6.5,26+8/9,26+8/9.5,27+3/12,25+11/6);
        $pos["18"]= new pos(0,26+6/7.5,26+6/8,26.5+4/8,27+6/12,3-1/5,3-8/8,2-8/17,3-5/6.5,26+9/9,26+9/9.5,27+4/12,27);
        $pos["19"]= new pos(0,26+7/7.5,26+7/8,26.5+5/8,27+7/12,3-2/5,3-9/8,2-9/17,3-6/6.5,26+10/9,26+10/9.5,27+5/12,27+1/6);
        $pos["20"]= new pos(0,26+8/7.5,26+8/8,26.5+6/8,27+8/12,3-3/5,3-10/8,2-10/17,3-7/6.5,26+11/9,26+11/9.5,27+6/12,27+2/6);
        $pos["21"]= new pos(0,26+9/7.5,26+9/8,26.5+7/8,27+9/12,3-4/5,3-11/8,2-11/17,3-8/6.5,26+12/9,26+12/9.5,27+7/12,27+3/6);
        $pos["22"]= new pos(0,26+10/7.5,26+10/8,26.5+8/8,27+10/12,3-5/5,3-12/8,2-12/17,3-9/6.5,26+13/9,26+13/9.5,27+8/12,27+4/6);
        $pos["23"]= new pos(0,26+11/7.5,26+11/8,26.5+9/8,27+11/12,3-6/5,3-13/8,2-13/17,3-10/6.5,26+14/9,26+14/9.5,27+9/12,27+5/6);
        $pos["24"]= new pos(0,26+12/7.5,26+12/8,26.5+10/8,28,3-7/5,3-14/8,2-14/17,3-11/6.5,26+15/9,26+15/9.5,27+10/12,28);
        $pos["25"]= new pos(0,26+13/7.5,26+13/8,26.5+11/8,28,3-8/5,3-15/8,2-15/17,3-12/6.5,26+16/9,26+16/9.5,27+11/12,28);
        $pos["26"]= new pos(0,26+14/7.5,26+14/8,28,28,3-9/5,1,2-16/17,1,26+17/9,26+17/9.5,28,28);
        $pos["27"]= new pos(0,28,26+15/8,28,28,1,1,1,1,28,26+18/9.5,28,28);
        $pos["28"]= new pos(0,28,28,28,28,1,1,1,1,28,28,28,28);

//        var_dump($pos);
        // most的位置  ,16的位置是15个高度，需要少减一个
        $dm->d_p=$pos[$dm->d]->d1;
        $dm->d_px=415-10.5*$dm->d_p+10.5;
        $dm->i_p=$pos[$dm->i]->i1;
        $dm->i_px=415-10.5*$dm->i_p+10.5;
        $dm->s_p=$pos[$dm->s]->s1;
        $dm->s_px=415-10.5*$dm->s_p+10.5;
        $dm->c_p=$pos[$dm->c]->c1;
        $dm->c_px=415-10.5*$dm->c_p+10.5;
        // least 位置
        $dl->d_p=$pos[$dl->d]->d2;
        $dl->d_px=415-10.5*$dl->d_p+10.5;
        $dl->i_p=$pos[$dl->i]->i2;
        $dl->i_px=415-10.5*$dl->i_p+10.5;
        $dl->s_p=$pos[$dl->s]->s2;
        $dl->s_px=415-10.5*$dl->s_p+10.5;
        $dl->c_p=$pos[$dl->c]->c2;
        $dl->c_px=415-10.5*$dl->c_p+10.5;
        // 差值位置
        $dml->d_p=$pos[$dml->d]->d3;
        $dml->d_px=415-10.5*$dml->d_p+10.5;
        $dml->i_p=$pos[$dml->i]->i3;
        $dml->i_px=415-10.5*$dml->i_p+10.5;
        $dml->s_p=$pos[$dml->s]->s3;
        $dml->s_px=415-10.5*$dml->s_p+10.5;
        $dml->c_p=$pos[$dml->c]->c3;
        $dml->c_px=415-10.5*$dml->c_p+10.5;

        // 计算disc的右侧的区域值，为右侧的数值,左侧4个数字为右侧一个区域
        // most
        $dm->dl=ceil($dm->d_p/4);
        $dm->il=ceil($dm->i_p/4);
        $dm->sl=ceil($dm->s_p/4);
        $dm->cl=ceil($dm->c_p/4);
        // least
        $dl->dl=ceil($dl->d_p/4);
        $dl->il=ceil($dl->i_p/4);
        $dl->sl=ceil($dl->s_p/4);
        $dl->cl=ceil($dl->c_p/4);
        // 差异
        $dml->dl=ceil($dml->d_p/4);
        $dml->il=ceil($dml->i_p/4);
        $dml->sl=ceil($dml->s_p/4);
        $dml->cl=ceil($dml->c_p/4);

        // 查看位置
//        var_dump($dm);
//        var_dump($dl);
//        var_dump($dml);

//        $this->assign("d1",$dm);
//        $this->assign("d2",$dl);
//        $this->assign("d3",$dml);
//        return $this->fetch();

        // 01 - 成就者型
        // 02 - 经纪人型
        // 03 - 评估者型
        // 04 - 协谈者型
        // 05 - 创意者型
        // 06 - 开创者型
        // 07 - 激发者型
        // 08 - 调查者型
        // 09 - 客观思考者型
        // 10 - 完美主义者型
        // 11 - 说服者型
        // 12 - 务实者型
        // 13 - 推广者型
        // 14 - 成果导向者型
        // 15 - 专业者型

        // 换算结果
        $typenum = $dml->dl.$dml->il.$dml->sl.$dml->cl;

        $discresult = new ShcDiscResultModel();
        $typer = $discresult->where('id', $typenum)
            ->find();
//        var_dump($typer);
//        exit;
        $typecode = "00";
        if($typer){
            if($typer['type']=="成就者")
                $typecode = "01";
            if($typer['type']=="经纪人")
                $typecode = "02";
            if($typer['type']=="评估者")
                $typecode = "03";
            if($typer['type']=="协谈者")
                $typecode = "04";
            if($typer['type']=="创意者")
                $typecode = "05";
            if($typer['type']=="开创者")
                $typecode = "06";
            if($typer['type']=="激发者")
                $typecode = "07";
            if($typer['type']=="调查者")
                $typecode = "08";
            if($typer['type']=="客观思考者")
                $typecode = "09";
            if($typer['type']=="完美主义者")
                $typecode = "10";
            if($typer['type']=="说服者")
                $typecode = "11";
            if($typer['type']=="务实者")
                $typecode = "12";
            if($typer['type']=="推广者")
                $typecode = "13";
            if($typer['type']=="成果导向者")
                $typecode = "14";
            if($typer['type']=="专业者")
                $typecode = "15";
        }
        $this->success('处理成功!',["d1"=>$dm,"d2"=>$dl,"d3"=>$dml,"typecode"=>$typecode]);

    }

    /**
     * 校验用户输入的授权码
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    function authcodeVerify(){
        $authcode = $this->request->param('authcode');
        if(!$authcode){
            return $this->error("未知错误123");
        }else{
            // 校验授权码正确性
            if(!$this->authcodeOK($authcode)){
                return $this->error("验证码失效");
            }
        }
        return $this->success();
    }

    /**
     *  校验授权码正确性
     * @param $code
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    function authcodeOK($code){
        log4()->debug($code);
        if($code){
            $authcode = new ShcCodeModel();
            $coder = $authcode->where(['code'=>$code,'status'=>1])
                ->find();
            if($coder){
                $time2 = $coder["end_time2"];
                if($time2){
                    if(time()<=$time2){
                        return true;
                    }else{
//                        return false;
                        // 过期了
                    }
                }else{
//                    return false;
                    // 没期限
                }
            }else{
                // 没授权码
//                return $this->error("出错了");
            }
        }else{
            // 没参数
        }
        return false;
    }

    /**
     * 生成新的授权码
     */
    function genAuthCode(){
//        var_dump(strtotime("+1 hour", time()));
//        exit;
        $hours = $this->request->param('hours');
        if(!$hours){
            return $this->error("未知错误123");
        }else{
            // 生成授权码
//            $ma = md5(time() . mt_rand(1,100));
            $ma = new ShcCodeModel();
//            $authcode = md5(time() . mt_rand(1,100));
            // 按要求生成8位验证码
            //$authcode=mt_rand(10000000,99999999);
            $authcode=mt_rand(10,30);
            //先逻辑删除所有同名的验证码

            $ma->save([
                'status' => 0,
                'end_time3' => date("Y-m-d H:i:s",time()),
            ],['code' => $authcode,'status' => 1]);

            $ma = new ShcCodeModel();
            $time2 = strtotime("+". $hours ." hour", time());
            $ma->data([
                'code' => $authcode,
                'add_time'    => date("Y-m-d H:i:s",time()),
                'end_time' => date("Y-m-d H:i:s",$time2),
                'end_time2' => $time2,
                'status' => 1
            ]);
            if($ma->save()){
                return $this->success("处理成功",["authcode"=>$authcode]);
            }else{
                return $this->error("处理失败");
            };

        }
    }

}