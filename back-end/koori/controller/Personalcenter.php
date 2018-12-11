<?php
/**
 * Created by PhpStorm.
 * User: Zedom
 * Date: 2018/11/8
 * Time: 23:58
 */

namespace app\index\controller;

use app\common\model\Collectmessage;
use app\common\model\Dailystudy;
use app\common\model\Message;
use app\common\model\Usercourse;
use app\common\model\Userdailystudy;
use app\common\model\Usermessage;
use app\common\model\User;
use app\common\model\Course;
use think\Db;
use think\facade\Session;
use think\Request;
use think\Validate;

class Personalcenter {
    public function center() {
        return view();
    }

    /*
     * Writer: 卢彦谚
     * updated on 12/7
     * Function: 获取用户信息
     */
    public function getAllInfo() {
        $userID = Session::get("id");
        //$userID = "u1";
        //用户基本信息
        $result = User::where(["ID"=>$userID])->find();
        if($result) {
            $mail = $result["mail"];
            $nickname=$result["nickname"];
            $gender=$result["sex"];
        }

        //用户所学课程信息
        $result2 = Db::table('Usercourse')->join('course',['Usercourse.courseID=course.id'])->where(['userID'=>$userID])->select();
        $courseResult = array();
        if($result2) {
            $i = 0;
            foreach ($result2 as $res2) {
                $cres = &$courseResult[$i];

                $cres['courseName'] = $res2['courseName'];
                $cres['courseID'] = $res2['courseID'];
                $cres['studyProgress'] = $res2['learningProgress'];
                $i++;
            }
        }

        /*
         * 用户每日学习状况
         */
//        $result3 = Dailystudy::where(["userID"=>$userID]);
//            $cnt = count($result3);
//            $j1 = 0;
//            $j2 = 0;
//            $dailyResult = array();
//            if($cnt < 7) {
//                foreach ($result3 as $res3) {
//                    $dailyResult[$j2]['time'] = $res3['onlineTime'];
//                    $dailyResult[$j2]['dateString'] = $res3['studyDate'];
//                    $j2++;
//                }
//            }
//            else {
//                for ($i = $cnt - 7; $i < $cnt; $i++) {
//                    $dailyResult[$j1]['time'] = $result3[$i]['onlineTime'];
//                    $dailyResult[$j1]['dateString'] = $result3[$i]['studyDate'];
//                    $j1++;
//                }
//            }

        //updated on 12/8
        $dailyResult = array();
        for ($i = 1; $i <= 7; $i++) {
            $index = (string)$i;
            $str = "-" . $index . " day";
            $curDate = (string)date("Y-m-d", strtotime($str));
            $dailyResult[$i - 1]['dateString'] = $curDate;
            $daily = Dailystudy::where(["userID"=>$userID, "studyDate"=>$curDate])->find();
            if($daily) {
                $dailyResult[$i - 1]['time'] = $daily['onlineTime'];
            }
            else {
                $dailyResult[$i - 1]['time'] = 0;
            }
        }



        /*
         * 用户收藏的帖子
         *updated on 2018/12/04
        */

        //updated on 12/7
        $result4 = Db::table('Collectblog')->join('Blog',['Collectblog.blogID=Blog.ID'])->where(["userID"=>$userID])->select();
        $collectBlogResult = array();
        if($result4) {
            $i = 0;
            foreach ($result4 as $res4) {
                $collectBlogResult[$i]["desc"] = $res4["content"];
                $collectBlogResult[$i]["author"] = $res4["author"];

                //added on 12/11
                $collectBlogResult[$i]["blogID"] = $res4["blogID"];

                $i++;
            }
        }

        return json_encode(
            array(
                "mail"=>$mail,
                "nickname"=>$nickname,
                "gender"=>$gender,
                "course"=>$courseResult,
                "learnTime"=>$dailyResult,
                "collectBlog"=>$collectBlogResult,
            )
        );
    }

    /*
     * Writer: 卢彦谚
     *
     * Function: 修改用户信息
     */
    public function modify(Request $request) {
        $post = $request->post();
        $responseStatus = 1;
        $uid = Session::get("userID");
        $newNickname = $post['nickname'];
        $newMail = $post['mail'];

        $result = User::get($uid)->save(["nickname"=>$newNickname, "mail"=>$newMail]);
        if($result) {
            $responseStatus = 0;
        }

        return json_encode(
            array(
                "responseStatus"=>$responseStatus,
            )
        );
//        if(Db::execute("update User where id = " + $uid +
//            " set username = " + $newUsername +
//            " and nickname = " + $newNickname +
//            " and mail = " + $newMail)) {
//            return json_encode(
//                array(
//                    "responseStatus"=>$responseStatus,
//                )
//            );
//        }
    }

    /*
     * Writer: 卢彦谚
     * Function: 删除收藏帖子
     * created on 12/11
     * 测试成功
     */
    public function deleteBlog(Request $request) {
        $post = $request->post();
        $blogID = $post['blogID'];
        $userID = Session::get('userID');
        //$userID = "u1";

        $result = Db::table('Collectblog')->where(["userID"=>$userID, "blogID"=>$blogID])->delete();
        if($result) {
            $responseStatus = 0;
        }
        else {
            $responseStatus = 1;
        }

        return json_encode(array("responseStatus"=>$responseStatus));
    }


    /*
     * Writer: 卢彦谚
     * Function: 更新专注度
     */
    public function concentrate() {
        $reponseStatus=0;
        return json_encode(
            array(
                "responseStatus"=>$reponseStatus,
            )
        );
    }

    /*
     * Writer: 卢彦谚
     * Function: 获取指定的单个用户信息
     */
//    public function getSingleInfo(Request $request) {
//        $get = $request->get();
//        $attr = $get['attribute'];
//        $uid = Session::get("userID");
//        $responseStatus = 0;
//
//        //用户基本信息
//        $result = User::where(["ID"=>$uid])->find();
//        //用户所学课程信息
//        $result2 = Usercourse::where(["userID"=>$uid])->select();
//        $i = 0;
//        $courseIdResult = array();
//        foreach ($result2 as $res) {
//            $cres = &$courseIdResult[$i];
//
//            $cnameResult = Course::where(["ID"=>$res['courseID']])->find();
//            if($cnameResult) {
//                $cres['courseName'] = $cnameResult['courseName'];
//            }
//            $cres['courseID'] = $res['courseID'];
//            $cres['studyProgress'] = $res['learningProgress'];
//
//            $i++;
//        }
//        //用户每日学习状况
//        $result3 = Userdailystudy::where(["userID"=>$uid])->select();
//        $cnt = count($result3);
//        $j1 = 0;
//        $j2 = 0;
//        $dailyResult = array();
//        if($cnt < 7) {
//            foreach ($result3 as $res3) {
//                $timeId = $res3['dailystudyID'];
//                $dailystudyResult = Dailystudy::where(["ID"=>$timeId])->find();
//                if($dailystudyResult) {
//                    $dailyResult[$j2]['time'] = $dailystudyResult['onlineTime'];
//                    $dailyResult[$j2]['dateString'] = $dailystudyResult['studyDate'];
//                    $j2++;
//                }
//            }
//        }
//        else {
//            for ($i = $cnt - 7; $i < $cnt; $i++) {
//                $timeId = $result3[$i]['dailystudyId'];
//                $dailystudyResult = Dailystudy::where(["id"=>$timeId])->find();
//                if($dailystudyResult) {
//                    $dailyResult[$j1]['time'] = $dailystudyResult['onlineTime'];
//                    $dailyResult[$j1]['dateString'] = $dailystudyResult['studyDate'];
//                    $j1++;
//                }
//            }
//        }
//
//        $value = "";
//        if($result) {
//            switch ($attr) {
//                case "username":
//                    $value = $result['username'];
//                    break;
//                case "gender":
//                    $value = $result['sex'];
//                    break;
//                case "mail":
//                    $value = $result['mail'];
//                    break;
//                case "nickname":
//                    $value = $result['nickname'];
//                    break;
//                case "course":
//                    $value = $courseIdResult;
//                    break;
//                case "learnTime":
//                    $value = $dailyResult;
//                    break;
//                default:
//                    $responseStatus = 1;
//                    break;
//            }
//        }
//        else {
//            $responseStatus = 1;
//        }
//
//        return json_encode(
//            array(
//                "responseStatus"=>$responseStatus,
//                "value"=>$value
//            )
//        );
//    }
}