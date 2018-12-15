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
//    public function index() {
//        return view();
//    }

    /*
     * created on 12/9
     * 查看帖子列表
     */
    public function stack() {
        return view();
    }

    /*
     * created on 12/7
     * 查看帖子
     */
    public function blog() {
        return view();
    }

    /*
     * created on 12/9
     * 发布帖子
     */
    public function post() {
        return view();
    }

//    /*created on 2018/12/7
//     *交流栈分别获取每门课程的最新三条帖子
//     * 用了Blog模型和Course模型
//     * 测试成功 12/7
//     * modified on 12/9 ：改为获取每门课程的最新三条帖子
//     */
//    public function getIndexList() {
//        $indexList = array();
//
//        //获取课程
//        $courseResult = Course::select();
//
//        $i = 0;
//        foreach ($courseResult as $course) {
//            $curIndexList = &$indexList[$i];
//            $curIndexList['courseID'] = $course['ID'];
//            $curIndexList['courseName'] = $course['courseName'];
//
//            $result = Blog::where(['courseID'=>$curIndexList['courseID']])->order('time desc')->limit(0,3)->select();
//
//            if($result) {
//
//                $curIndexList['blogList'] = array();
//
//                $j = 0;
//                foreach ($result as $res) {
//                    $curblog = &$curIndexList['blogList'][$j];
//
//                    $curblog['blogID'] = $res['ID'];
//                    $curblog['blogName'] = $res['title'];
//                    $j++;
//                }
//            }
//
//            $i++;
//        }
//
//        return json_encode(
//            array(
//                "list"=>$indexList,
//            )
//        );
//    }


    /*created on 12/7
     *获取当前课程的所有帖子列表
     * 用了Blog模型和Message模型
     * 测试成功 12/7
     * updated on 12/14: 将content内容改成内容前50字加省略号
     */
    public function getBlogList() {
        $courseId = Session::get('courseID');
        $blogList = array();

        //找出课程对应的所有帖子
        $blogsResult = Blog::where(['courseID'=>$courseId])->order('time desc')->select();

        $i = 0;
        foreach ($blogsResult as $blog) {
            $curBlog = &$blogList[$i];
            $curBlog['title'] = $blog['title'];
            $curBlog['time'] = $blog['time'];
            $curBlog['authorName'] = $blog['author'];

            $blogId = $blog['ID'];
            $curBlog['commentCount'] = Message::where(["blogID"=>$blogId])->count();

            $content = $blog['content'];
            $curBlog['comment'] = mb_substr($blog['content'],0,50, 'utf-8')."……";

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
     * created on 12/11
     * 获取当前课程最新五条帖子
     * 用了Blog模型
     * 测试成功
     */
    public function getNew() {
        $courseID = Session::get('courseID');
//        $courseID = "1";
        $blogList = array();

        //找出课程对应的所有帖子
        $blogsResult = Blog::where(['courseID'=>$courseID])->order('time desc')->limit(0,5)->select();

        $i = 0;
        foreach ($blogsResult as $blog) {
            $curBlog = &$blogList[$i];
            $curBlog['title'] = $blog['title'];
            $curBlog['blogID'] = $blog['ID'];

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
     * updated on 12/13 : 增加了返回状态
     */
    public function enter(Request $request) {
        $post = $request->post();
        $blogID = $post['blogID'];
        Session::set('blogID', $blogID);

        //added on 12/13
        if(Session::get('blogID')) {
            return json_encode(array("responseStatus"=>0));
        }
        else {
            return json_encode(array("responseStatus"=>1));
        }
    }

    /*
     * created on 12/7
     * 获取帖子内容和对应留言
     * 用了Message模型、User模型、Blog模型和Collectblog表
     * 测试成功 12/9
     * updated on 12/13: 删除了该用户没有收藏帖子时的内容
     * updated on 12/13: reply改成replyID，添加了replyName
     * updated on 12/15: 改了replyID
     */
    public function getBlog() {
        $blogID = Session::get('blogID');
        $userID = Session::get('userID');
//        $blogID = "1";
//        $userID = "u1";

        $result = Message::where(['blogID'=>$blogID])->order('time desc')->select();
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

                $comment[$i]['replyID'] = $res['replyID'];
                $replyNameResult = Message::where(["ID"=>$res['replyID']])->find();
                if($res['replyID'] == -1) {
                    $comment[$i]['replyName'] = "";
                }
                else {
                    $comment[$i]['replyName'] = $replyNameResult['nickname'];
                }

                $commentUser = Db::table('usermessage')->where(['messageID'=>$res['ID']])->find();
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
        $result3 = Db::table('collectblog')->where(['userID'=>$userID, 'blogID'=>$blogID])->find();
        if($result3) {
            $collected = 1;
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


    /*
     * written by 卢彦谚
     * created on 12/9
     * 发表帖子
     * 用了Blog模型和Userblog表
     * 测试成功 12/9
     */
    public function addBlog(Request $request) {
        $post = $request->post();
        $title = $post['title'];
        $content = $post['content'];
        $userID = Session::get('userID');
        $courseID = Session::get('courseID');
        $nickname = Session::get('nickname');
//        $userID = "u1";
//        $courseID = "1";
//        $nickname = "king";

        $date = date('Y-m-d H:i:s');

        $result = Blog::insert(["title"=>$title, "content"=>$content, "time"=>$date, "author"=>$nickname, "courseID"=>$courseID]);
        if($result) {
            //前提是blogID自增，获取新数据id
            //$blogID = Blog::max('ID');
            $blogID = Blog::getLastInsID();
        }
        else {
            return json_encode(array(
               "responseStatus"=>1,
            ));
        }

        $result2 = Db::table('userblog')->insert(['userID'=>$userID, 'blogID'=>$blogID]);
        if($result2) {
        }
        else {
            return json_encode(array(
                "responseStatus"=>1,
            ));
        }

        return json_encode(array(
            "responseStatus"=>0,
        ));
    }

    /*
     * written by 卢彦谚
     * created on 12/9
     * 删除帖子
     * 用了Blog模型
     * 用delete方法
     * 测试成功
     */
    public function deleteBlog() {
        $blogID = Session::get('blogID');

        $result = Blog::where(["ID"=>$blogID])->delete();
        if($result) {
            $responseStatus = 0;
        }
        else {
            $responseStatus = 1;
        }

        return json_encode(
            array(
                "responseStatus"=>$responseStatus,
            )
        );
    }

    /*
     * written by 卢彦谚
     * created on 12/9
     * 修改帖子
     * 用了Blog模型
     * 必须传title过来，如果title为空，则表里的title也更新为空
     * 测试成功
     */
    public function modifyBlog(Request $request) {
        $post = $request->post();
        $title = $post['title'];
        $content = $post['content'];
        $blogID = $post['blogID'];
        $time = date('Y-m-d H:i:s');

        $result = Blog::get($blogID)->save(["title"=>$title,"content"=>$content, "time"=>$time]);
        if($result) {
            $responseStatus = 0;
        }
        else {
            $responseStatus = 1;
        }

        return json_encode(array("responseStatus"=>$responseStatus));
    }

    /*
     * written by 卢彦谚
     * created on 12/9
     * 收藏帖子
     *用了Collectblog表
     * 测试成功
     * updated on 12/13: 增加了用户未登录的情况
     */
    public function collect() {
        $userID = Session::get('userID');
        $blogID = Session::get('blogID');
//        $userID = "u2";
//        $blogID = 4;
        if($userID) {
            $result = Db::table('collectblog')->insert(["userID"=>$userID, "blogID"=>$blogID]);
            if($result) {
                $responseStatus = 0;
            }
            else {
                $responseStatus = 2;
            }
        }
        else {
            $responseStatus = 1;
        }


        return json_encode(array("responseStatus"=>$responseStatus));
    }

    /*
     * written by 卢彦谚
     * created on 12/9
     * 取消收藏帖子
     * 用了Collectblog表
     * 用delete方法
     * 测试成功
     */
    public function cancelCollect() {
        $userID = Session::get('userID');
        $blogID = Session::get('blogID');
//        $userID = "u2";
//        $blogID = 3;

        $result = Db::table('collectblog')->where(["userID"=>$userID, "blogID"=>$blogID])->delete();
        if($result) {
            $responseStatus = 0;
        }
        else {
            $responseStatus = 1;
        }

        return json_encode(array("responseStatus"=>$responseStatus));
    }

    /*
     * written by 卢彦谚
     * created on 12/9
     * 发布留言
     * 用了Message模型和Usermessage表
     * nickname要存到表中
     * 测试成功
     * updated on 12/15: 改了replyID
     */
    public function replyComment(Request $request) {
        $post = $request->post();
        $content = $post['content'];
        $replyID = $post['replyID']; //-1代表不是回复留言的留言
        $time = date('Y-m-d H:i:s');

        $userID = Session::get('userID');
        $nickname = Session::get('nickname');
        $blogID = Session::get('blogID');
//        $userID = "u1";
//        $nickname = "king";
//        $blogID = 3;

        $result = Message::insert(["content"=>$content, "replyID"=>$replyID, "nickname"=>$nickname, "blogID"=>$blogID, "time"=>$time]);
        if($result) {
            //新插入数据的ID
            $messageID = Message::getLastInsID();
        }
        else {
            return json_encode(array("responseStatus"=>1));
        }

        $result2 = Db::table('usermessage')->insert(["userID"=>$userID, "messageID"=>$messageID]);
        if($result2) {
        }
        else {
            return json_encode(array("responseStatus"=>1));
        }

        return json_encode(array("responseStatus"=>0));
    }

    /*
     * written by 卢彦谚
     * created on 12/9
     * 修改留言
     * 用了Message模型
     * 测试成功
     */
    public function modifyComment(Request $request) {
        $post = $request->post();
        $content = $post['content'];
        $messageID = $post['commentID'];
        $time = date('Y-m-d H:i:s');

        $result = Message::get($messageID)->save(["content"=>$content, "time"=>$time]);
        if($result) {
            $responseStatus = 0;
        }
        else {
            $responseStatus = 1;
        }

        return json_encode(array("responseStatus"=>$responseStatus));
    }

    /*
     * written by 卢彦谚
     * created on 12/9
     * 删除留言
     * 用了Message模型
     * 测试成功
     */
    public function deleteComment(Request $request) {
        $post = $request->post();
        $commentID = $post['commentID'];

        $result = Message::where(['ID'=>$commentID])->delete();
        if($result) {
            $responseStatus = 0;
        }
        else {
            $responseStatus = 1;
        }

        return json_encode(array("responseStatus"=>$responseStatus));
    }
}