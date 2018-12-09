<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

Route::get('think', function () {
    return 'hello,ThinkPHP5!';
});
Route::get('hello', 'index/hello');
Route::resource('stu', 'index/Stu');
//Route::get('add','index/article/index');

Route::rule('index', 'index/Index/index');

// login
Route::rule('login/register', 'index/Login/register');
Route::rule('login/index', 'index/Login/index');
Route::rule('login/login', 'index/Login/login');
Route::rule('login/box', 'index/Login/box');

//personalcenter
Route::rule('Personalcenter/center', 'index/Personalcenter/center');
Route::rule('/personalcenter/getAllInfo', 'index/Personalcenter/getAllInfo');
Route::rule('/personalcenter/modify', 'index/Personalcenter/modify');

//messageboard
Route::rule('Cblog/index', 'index/Cblog/index');
Route::rule('Cblog/stack', 'index/Cblog/stack');
Route::rule('Cblog/blog', 'index/Cblog/blog');
Route::rule('/cblog/add', 'index/Cblog/add');
Route::rule('/cblog/getBlogList', 'index/Cblog/getBlogList');
Route::rule('/cblog/getIndexList', 'index/Cblog/getIndexList');
Route::rule('/cblog/enter', 'index/Cblog/enter');
Route::rule('/cblog/getBlog', 'index/Cblog/getBlog');
Route::rule('/cblog/addBlog', 'index/Cblog/addBlog');
Route::rule('/cblog/modifyBlog', 'index/Cblog/modifyBlog');
Route::rule('/cblog/deleteBlog', 'index/Cblog/deleteBlog');
Route::rule('/cblog/replyComment', 'index/Cblog/replyComment');
Route::rule('/cblog/modifyComment', 'index/Cblog/modifyComment');
Route::rule('/cblog/deleteComment', 'index/Cblog/deleteComment');

// session
Route::rule('session', 'index/Basic/getSession');

return [

];
