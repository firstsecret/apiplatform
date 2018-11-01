<?php
/**
 * Created by Bevan.
 * User: Bevan@zhoubinwei@aliyun.com
 * Date: 2018/11/1
 * Time: 9:37
 */

namespace App\Observers;


use App\Models\AppUser;

class AppUserObserver
{
    public function deleting(AppUser $appUser)
    {
        dd($appUser);
    }
}