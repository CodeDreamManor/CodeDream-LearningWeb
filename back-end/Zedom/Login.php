<?php
/**
 * Created by PhpStorm.
 * User: Zedom
 * Date: 2018/11/7
 * Time: 23:19
 */

namespace app\index\controller;

use app\common\model\Dailystudy;
use app\common\model\User;
use function PHPSTORM_META\type;
use think\Db;
use think\facade\Session;
use think\Request;
use think\Validate;

class Login
{
    /**
     * Writer:      吴潘安
     * Date:        2018/12/8
     * Function:    返回注册登录页面
     */
    public function page(){
        return view();
    }

    /**
     * Writer:      吴潘安
     * Date:        2018/12/8
     * Function:    返回注册登录填写框
     */
    public function box(){
        return view();
    }

    /**
     * Writer:      吴潘安
     * Date:        2018/12/8
     * Function:    登陆检测接口
     */
    public function login(Request $request){
        $post = $request->post();

        $result = Db::table("sysmanager")->where(["mail"=>$post["mail"]])->find();
        if($result){
            json_encode(array("responseStatus"=>2));
            Session::set("identity",2);
        }

        $result = Db::table("busmanager")->where(["mail"=>$post["mail"]])->find();
        if($result){
            json_encode(array("responseStatus"=>2));
            Session::set("identity",2);
        }

        $result = User::where(["mail"=>$post["mail"]])->find();
        $response = 0;
        $errorMessage = "";
        if($result){
            if($result["password"]==$post["password"]){
                Session::set("userID",$result["ID"]);
                Session::set("mail",$result["mail"]);
                Session::set("nickname",$result["nickname"]);
                Session::set("startTime",date("H:i:s"));
                Session::set("identity",0);
            }else{
                $response = -1;
                $errorMessage = "密码错误";
            }
        }else{
            $response = -1;
            $errorMessage = "用户名不存在";
        }
        return json_encode(
            array(
                "responseStatus"=>$response,
                "errorMessage"=>$errorMessage
            )
        );
    }

    /**
     * Writer:      吴潘安
     * Date:        2018/12/8
     * Function:    注册接口
     */
    public function register(Request $request){
        $post = $request->post();
        // 对post数据进行验证，创建一个验证规则
        $validate = Validate::make([
            // 键名：定义需要验证的字段名
            // 键值：规则
            "nickname"  =>  "require|min:3|max:15",
            "mail"      =>  "require",
            "password"  =>  "require|min:6|max:16",
        ]);
        $states = $validate->check($post);
        $response = "";
        if($states){
            // 向数据库中插入数据
            $effect = User::insert([
                "nickname" => $post["nickname"],
                "password" => $post["password"],
                "mail" => $post["mail"],
                "registrationDate" => date("Y-m-d")
            ]);
            if($effect==1)
                $response = array("responseStatus"=>0);
            else{
                $response = array("responseStatus"=>1, "errorMessage"=>"用户名重复");
            }
        }else{
            $response = array("responseStatus"=>1,"errorMessage"=>$validate->getError());
        }
        return json_encode($response);
    }

    /**
     * Writer:      吴潘安
     * Date:        2018/12/8
     * Function:    注销接口，保存用户在线时长
     */
    public function logout(Request $request){
        $userID = Session::get("userID");
        $startTime = Session::get("startTime");
        $endTime = date("H:i:s");
        $day = date("Y-m-d");
        $onlineTime = (strtotime($endTime) - strtotime($startTime))/60.0;
        $onlineTime = max($onlineTime, 0.0);
        $exists = Dailystudy::where("userID",$userID)->where("studyDate",$day)->find();
        if($exists){
            $exists["onlineTime"] += $onlineTime;
            $exists["onlineTime"] = min(300.0, $exists["onlineTime"]);
            $exists->save();
            return ($onlineTime);
        }
        else{
            $exists = new Dailystudy([
                "studyDate"=>$day,
                "onlineTime"=>$onlineTime,
                "userID"=>$userID]);
            $exists->save();
        }
        Session::clear();
        return json_encode(["responseStatus"=>0]);
    }

    /**
     * Writer:      吴潘安
     * Date:        2018/12/13
     * Function:    用户状态检测接口，判断用户是否登陆以及身份
     */
    public function judge(Request $request){
        $result = Session::get("identity");
        return json_encode(["responseStatus"=>$result]);
    }
}

