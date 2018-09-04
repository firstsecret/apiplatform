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

        DB::beginTransaction();
        try {
            $admin = Admin::create([
                'name' => $data['name'],
                'email' => $data['email'] ?? '',
                'password' => bcrypt($data['password']),
                'telephone' => $data['telephone'],
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

        if ($model::where('name', $data['name'])->first(['name'])) throw new PlatformProductException(400, '用户名已存在');

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

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $data['name'],
                'email' => ($data['email'] ?? ''),
                'password' => bcrypt($data['password']),
                'telephone' => $data['telephone'],
                'type' => (int)$data['type']
            ]);

            if ($user) {
                // app_user
                $app_key = md5($this->randomStr(11));
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
            // 记录 哪个 应用 请求 创建的
            Log::info("\r\n\r\n" . $creater->name . '(model_id:' . $creater->id . ')创建了用户: ' . $user->name . '(user_id:' . $user->id . ')');
            DB::commit();

            return ['res' => true, 'data' => ['app_key' => $app_key, 'app_secret' => $app_secret]];
        } catch (\Exception $e) {
            DB::rollBack();
            throw new PlatformProductException('500', '创建失败' . $e->getMessage());
        }
    }
}