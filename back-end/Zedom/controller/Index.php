<?php
namespace app\index\controller;

use app\common\model\Users;
use think\Db;
use think\facade\Session;

class Index
{
    public function index()
    {
        return view();
    }
}
