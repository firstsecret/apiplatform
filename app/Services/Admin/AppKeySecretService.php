<?php
/**
 * Created by Bevan.
 * User: Bevan@zhoubinwei@aliyun.com
 * Date: 2018/10/22
 * Time: 15:43
 */

namespace App\Services\Admin;


use App\Models\AppUser;
use Illuminate\Support\Facades\Redis;

class AppKeySecretService
{
    public function mapAppkeysecret()
    {
        AppUser::chunk(200, function ($users) {
            foreach ($users as $u) {
//                var_dump($u->type . ':' . $u->app_key . ',user_id:' . $u->id);
                Redis::set('app_key:' . $u['app_key'], $u['app_secret'] . $u['type']);
            }
        });

        // cursor handle
//        foreach (AppUser::where('model', 'App\User')->cursor() as $user) {
//            Redis::set($user['app_key'], $user['app_secret'] . $user['type']);
//        }

        return $this->responseClient(200, '成功', []);
    }
}