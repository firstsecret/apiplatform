<?php
/**
 * Created by Bevan.
 * User: Bevan@zhoubinwei@aliyun.com
 * Date: 2018/10/22
 * Time: 15:43
 */

namespace App\Services\Admin;


use App\Models\AppUser;
use App\User;
use Illuminate\Support\Facades\Redis;

class AppKeySecretService
{
    public function mapAppkeysecret()
    {
        AppUser::with(['user' => function ($query) {
            $query->where('type', User::IS_ACTIVE_STATUS);
        }])->where('model', User::LOGIC_MODEL)->chunk(200, function ($users) {
            //filter
            $users = $users->filter(function ($u, $key) {
                return $u['user'];
            })->toArray();

            // handle
            foreach ($users as $user) {
                Redis::hset(User::APP_KEY_FLAG . $user['app_key'], 'app_secret', $user['app_secret']);
                Redis::hset(User::APP_KEY_FLAG . $user['app_key'], 'user_type', $user['user']['type']);
                Redis::hset(User::APP_KEY_FLAG . $user['app_key'], 'app_key_type', $user['type']);
                Redis::hset(User::APP_KEY_FLAG . $user['app_key'], 'user_id', $user['user_id']);
            }
        });

//        AppUser::chunk(200, function ($users) {
//            foreach ($users as $u) {
////                var_dump($u->type . ':' . $u->app_key . ',user_id:' . $u->id);
//                Redis::set('app_key:' . $u['app_key'], $u['app_secret'] . $u['type']);
//            }
//        });

        // cursor handle
//        foreach (AppUser::where('model', 'App\User')->cursor() as $user) {
//            Redis::set($user['app_key'], $user['app_secret'] . $user['type']);
//        }

//        return $this->responseClient(200, '成功', []);
    }
}