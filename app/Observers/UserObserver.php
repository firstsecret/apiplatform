<?php
/**
 * Created by Bevan.
 * User: Bevan@zhoubinwei@aliyun.com
 * Date: 2018/11/1
 * Time: 17:52
 */

namespace App\Observers;


use App\Jobs\UpdateAppKeyMap;
use App\Models\AppUser;
use App\User;

class UserObserver
{
    public function created(User $user)
    {
//        $appuser = AppUser::where('user_id', $user->id)->get();
//
//        dd($appuser);
    }

    public function deleted(User $user)
    {
        // update
        $this->updateAppKeyMap($user);
    }

    public function updated(User $user)
    {
        $this->updateAppKeyMap($user);
    }

    public function updateAppKeyMap($user)
    {
        $appuser = AppUser::where('user_id', $user->id)->limit(1)->get()[0];

        if ($appuser) {
            UpdateAppKeyMap::dispatch($appuser->toArray());
        }
    }
}