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

class InternalService extends BaseService
{
    /**
     *  内部其他应用 创建用户
     */
    public function createAdminUser($data)
    {
        // check unique
        if (!$this->checkIsPhone($data['telephone'])) throw new PlatformProductException(400, '手机号码不正确');

        // check unique
        if (Admin::where('name', $data['name'])->first(['name'])) throw new PlatformProductException(400, '用户名已存在');

        if (Admin::where('telephone', $data['telephone'])->first(['telephone'])) throw new PlatformProductException(400, '该号码已注册');

        if ($data['email']) {
            if (Admin::where('email', $data['email'])->first(['email'])) throw new PlatformProductException(400, '该邮箱已注册');
        }

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
}