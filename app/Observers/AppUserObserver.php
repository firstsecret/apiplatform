<?php
/**
 * Created by Bevan.
 * User: Bevan@zhoubinwei@aliyun.com
 * Date: 2018/11/1
 * Time: 9:37
 */

namespace App\Observers;


use App\Exceptions\AppUserException;
use App\Jobs\UpdateAppKeyMap;
use App\Models\AppUser;
use App\User;

class AppUserObserver
{
    public function deleting(AppUser $appUser)
    {
//        dd($appUser);
        // check is can't del
        $user = User::find($appUser->user_id);

        if ($user && $user->type == User::IS_ACTIVE_STATUS) throw new AppUserException('4084', '关联用户未删除或未关闭授权,不允许删除');
    }

    public function deleted(AppUser $appUser)
    {
        $this->updateAppKeyMap($appUser);
    }

    public function created(AppUser $appUser)
    {
        $this->updateAppKeyMap($appUser);
    }

    public function updated(AppUser $appUser)
    {
        $this->updateAppKeyMap($appUser);
    }

    private function updateAppKeyMap(AppUser $appUser)
    {
        UpdateAppKeyMap::dispatch($appUser->toArray());
    }
}