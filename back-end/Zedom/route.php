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

// view:
Route::rule('/index', 'index/Index/index');
Route::rule("/course/newcourse", 'index/Ccourse/newcourse');
Route::rule("/course/course", 'index/Ccourse/course');
Route::rule('/login/page', 'index/Login/page');
Route::rule('/login/box', 'index/Login/box');

Route::rule('/test', 'index/Abx/index');


// login
Route::rule('/login/register', 'index/Login/register');
Route::rule('/login/login', 'index/Login/login');
Route::rule('/login/logout', 'index/Login/logout');

// Course
Route::rule("/course/getCourse", 'index/Ccourse/getCourse');
Route::rule("/course/getCourseList", 'index/Ccourse/getCourseList');
Route::rule("/course/getChapter", 'index/Ccourse/getChapter');
Route::rule("/course/getContent", 'index/Ccourse/getContent');

Route::rule("/course/addCourse", 'index/Ccourse/addCourse');
Route::rule("/course/addChapter", 'index/Ccourse/addChapter');
Route::rule("/course/addSection", 'index/Ccourse/addSection');

Route::rule("/course/editCourse", 'index/Ccourse/editCourse');
Route::rule("/course/editSection", 'index/Ccourse/editSection');



return [

];
