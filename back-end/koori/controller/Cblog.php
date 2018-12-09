<?php
/**
 * Created by PhpStorm.
 * User: koori
 * Date: 2018/11/29
 * Time: 20:28
 */
namespace app\index\controller;

use app\common\model\Blog;
use app\common\model\Blogmessage;
use app\common\model\Course;
use app\common\model\Message;
use app\common\model\User;
use think\Db;
use think\facade\Session;
use think\Request;
use think\Validate;

class Cblog {
    public function index() {
        return view();
    }

    /*
     * updated on 12/7
     */
    public function blog() {
        return view();
    }

    /*
     * updated on 12/9
     */
    public function stack() {
        return view();
    }

    public function add(Request $request) {
        $post = $request->post();
        $content = $post['comment'];
        $courseId = $post['courseID'];

        //updated on 2018/12/04
        //$uid = Session::get("id");
        $uid = "u1";

        $date = date('Y-m-d H:i:s');
        $responseStatus = 1;

        $result1 = Db::table('Message')->insert(["content"=>$content, "courseID"=>$courseId, "releaseTime"=>$date]);

        //updated on 2018/12/04
        //$result2 = Db('User')

        if($result1) {
            $responseStatus = 0;
        }

        return json_encode(
            array(
                "responseStatus"=>$responseStatus,
            )
        );
    }

    /*updated on 2018/12/7
     *交流栈分别获取前三门课程的最新五条帖子
     * 测试成功 12/7
     */
    public function getIndexList() {
        $indexList = array();
        $minCourseId = Blog::min('courseID'); //blog涉及的最小课程id
        $cnt = Blog::count('distinct courseID');  //blog涉及的课程数量

        //如果多于3门课程，选前三门
        if($cnt > 3) {
            $cnt = 3;
        }
        for($i = 0; $i < $cnt; $i++) {
            $curIndexList = &$indexList[$i];
            $curIndexList['courseID'] = $minCourseId + $i;

            $courseNameResult = Course::where(['ID'=>$curIndexList['courseID']])->find();
            if($courseNameResult) {
                $curIndexList['courseName'] = $courseNameResult['courseName'];
            }

            $result = Db::table('Blog')->where(['courseID'=>$curIndexList['courseID']])->order('time desc')->limit(0,5)->select();

            if($result) {

                $curIndexList['blogList'] = array();

                $j = 0;
                foreach ($result as $res) {
                    $curblog = &$curIndexList['blogList'][$j];

                    $curblog['blogID'] = $res['ID'];
                    $curblog['blogName'] = $res['title'];
                    $j++;
                }
            }
        }

        return json_encode(
            array(
                "list"=>$indexList,
            )
        );
    }

    /*updated on 12/7
     *获取帖子列表
     * 测试成功 12/7
     */
    public function getBlogList() {
        $courseId = Session::get('courseID');
        $blogList = array();

        //找出课程对应的所有帖子
        $blogsResult = Blog::where(['courseID'=>$courseId])->select();

        $i = 0;
        foreach ($blogsResult as $blog) {
            $curBlog = &$blogList[$i];
            $curBlog['title'] = $blog['title'];
            $curBlog['time'] = $blog['time'];
            $curBlog['authorName'] = $blog['author'];

            $blogId = $blog['ID'];
            $curBlog['commentCount'] = Message::where(["blogID"=>$blogId])->count();
            $curBlog['comment'] = $blog['content'];
            $curBlog['blogID'] = $blogId;

            $i++;
        }

        return json_encode(
            array(
                "blog"=>$blogList,
            )
        );
    }

    /*
     * created on 12/7
     * 点进blog前将blogID传给Session
     * 待测试
     */
    public function enter(Request $request) {
        $post = $request->post();
        $blogID = $post['blogID'];
        Session::set('blogID', $blogID);
    }

    /*
     * created on 12/7
     * 获取帖子内容和对应留言
     * 测试成功 12/9
     * 待确认问题：headLink，昵称是否可以重复，是否只返回三条评论
     */
    public function getBlog() {
        $blogID = Session::get('blogID');
        $userID = Session::get('userID');
//        $blogID = "1";
//        $userID = "u1";

        //只返回三条评论
        $result = Message::where(['blogID'=>$blogID])->order('time desc')->limit(0,3)->select();
        $comment = array();
        if($result) {
            $i = 0;
            foreach ($result as $res) {
                $comment[$i]['nickName'] = $res['nickname'];

                $comment[$i]['time'] = $res['time'];

                //用户头像，条件：昵称不能重复
                $userResult = User::where(['nickname'=>$res['nickname']])->find();
                $comment[$i]['headLink'] = $userResult['portrait'];

                $comment[$i]['content'] = $res['content'];

                if($res['replyID'] == "") {
                    $comment[$i]['reply'] = -1;
                }
                else {
                    $comment[$i]['reply'] = $res['replyID'];
                }

                $commentUser = Db::table('Usermessage')->where(['messageID'=>$res['ID']])->find();
                $commentUserId = $commentUser['userID'];
                if($commentUserId == $userID) {
                    $comment[$i]['editable'] = true;
                }
                else {
                    $comment[$i]['editable'] = false;
                }

                $i++;
            }
        }
        else {
            return json_encode(
                array('responseStatus'=>1)
            );
        }

        $result2 = Blog::where(['ID'=>$blogID])->find();
        $title = "";
        $time = "";
        $authorName = "";
        $content = "";
        if($result2) {
            $title = $result2['title'];
            $time = $result2['time'];
            $authorName = $result2['author'];
            $content = $result2['content'];
        }
        else {
            return json_encode(
                array('responseStatus'=>1)
            );
        }

        $collected = 0;
        $result3 = Db::table('Collectblog')->where(['userID'=>$userID, 'blogID'=>$blogID])->find();
        if($result3) {
            $collected = 1;
        }
        else {
            return json_encode(
                array('responseStatus'=>1)
            );
        }

        return json_encode(
            array(
                "responseStatus"=>0,
                "title"=>$title,
                "time"=>$time,
                "authorName"=>$authorName,
                "content"=>$content,
                "collected"=>$collected,
                "comment"=>$comment,
            )
        );
    }


    //先测试Session！
}