<?php
/**
 * Created by Bevan.
 * User: Bevan@zhoubinwei@aliyun.com
 * Date: 2018/8/31
 * Time: 10:38
 */

namespace App\Services;


use App\Exceptions\PlatformProductException;
use App\Models\Admin;
use App\Models\AppUser;
use App\Models\UuidUser;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class InternalService extends BaseService
{
    /**
     *  admin创建用户
     */
    public function createAdminUser($data)
    {
        // check unique
        $this->checkUnique($data, '\App\Models\Admin');
        $password = md5($this->customCreateUUID());
        DB::beginTransaction();
        try {
            $admin = Admin::create([
                'name' => md5($this->customCreateUUID()),
                'email' => $data['email'] ?? null,
                'password' => bcrypt($password),
                'telephone' => $data['telephone'] ?? null,
            ]);

            if ($admin) {
                // app_user
                $app_key = md5($this->randomStr(11));
                $app_secret = md5($this->randomStr(11));
                AppUser::updateOrCreate([
                    'user_id' => $admin->id,
                    'model' => get_class($admin),
                ], [
                    'app_key' => $app_key,
                    'app_secret' => $app_secret,
                    'model' => get_class($admin),
                    'user_id' => $admin->id
                ]);
            }
            // 分配角色
            $admin->assignRole('internal');

            DB::commit();

            return ['res' => true, 'data' => ['app_key' => $app_key, 'app_secret' => $app_secret]];
        } catch (\Exception $e) {
            DB::rollBack();
            throw new PlatformProductException('500', '创建失败' . $e->getMessage());
        }
//        return ['res' => false, 'data' => []];
    }

    /**
     * 用户 模型 唯一校验
     * @param $data
     * @param string $model
     */
    protected function checkUnique($data, $model = '\App\User')
    {
        if (!$this->checkIsPhone($data['telephone'])) throw new PlatformProductException(400, '手机号码不正确');

        if ($user = $model::where('name', $data['name'])->first(['name'])) {
//            return Response()->json(['status_code' => 200,'msg'=>'用户名已存在', 'data'=> ]);
            throw new PlatformProductException(400, '用户名已存在');
        }

        if ($model::where('telephone', $data['telephone'])->first(['telephone'])) throw new PlatformProductException(400, '该号码已注册');

        if (isset($data['email']) && $data['email']) {
            if ($model::where('email', $data['email'])->first(['email'])) throw new PlatformProductException(400, '该邮箱已注册');
        }
    }

    /**
     * 开通用户
     * @param $data
     */
    public function openUser($data)
    {
        $this->checkUnique($data, '\App\User');

        dd(User::find(2)->app);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $data['name'],
                'email' => ($data['email'] ?? null),
                'password' => bcrypt($data['password']),
                'telephone' => $data['telephone'],
                'type' => (int)$data['type'] ?? 0
            ]);

            if ($user) {
                // app_user
                $app_key = md5($this->customCreateUUID());
                $app_secret = md5($this->randomStr(11));
                AppUser::updateOrCreate([
                    'user_id' => $user->id,
                    'model' => get_class($user),
                ], [
                    'app_key' => $app_key,
                    'app_secret' => $app_secret,
                    'model' => get_class($user),
                    'user_id' => $user->id
                ]);
            }

            $creater = JWTAuth::parseToken()->user();

            // 生成 用户 对应的 uuid
            $creater_uuid = $creater->uuid;
            $uuid = $this->customCreateUUID();
            $openid = $creater_uuid . $uuid;

            UuidUser::updateOrCreate([
                'user_id' => $user->id,
                'model_id' => $creater->id,
                'model_uuid' => $creater_uuid,
                'model' => get_class($creater)
            ], [
                'user_id' => $user->id,
                'model_id' => $creater->id,
                'model_uuid' => $creater_uuid,
                'openid' => $openid,
                'model' => get_class($creater)
            ]);
//            UuidUser::create([
//                'user_id' => $user->id,
//                'model_id' => $creater->id,
//                'model_uuid' => $creater_uuid,
//                'openid' => $openid,
//                'model' => get_class($creater)
//            ]);
            // 记录 哪个 应用 请求 创建的
            Log::info("\r\n\r\n" . $creater->name . '(model_id:' . $creater->id . ')创建了用户: ' . $user->name . '(user_id:' . $user->id . ')');
            DB::commit();

            return ['res' => true, 'data' => ['app_key' => $app_key, 'app_secret' => $app_secret, 'openid' => $openid]];
        } catch (\Exception $e) {
            DB::rollBack();
            throw new PlatformProductException('500', '创建失败' . $e->getMessage());
        }
    }

    public function factoryAccessToken($app_key, $app_secret)
    {
        if (!$app_key || !$app_secret) throw new PlatformProductException(400, 'appkey或appsecret未获取');

        $admin = AppUser::where([
            'app_key' => $app_key,
            'app_secret' => $app_secret,
            'model' => 'App\Models\Admin'
        ])->first()->admin;

        $token = JWTAuth::claims(['model' => 'admin'])->fromUser($admin);
        // 获取过期时间
        $express_in = config('jwt.ttl') * 60; // second

        if (!$token) throw new PlatformProductException(500, '令牌生成失败');

        return ['access_token' => $token, 'express_in' => $express_in];
    }
}