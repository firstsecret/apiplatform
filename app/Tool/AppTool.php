<?php
/**
 * Created by PhpStorm.
 * User: Bevan@zhoubinwei@aliyun.com
 * Date: 2018/8/30
 * Time: 14:51
 */

namespace App\Tool;


trait AppTool
{
    /**
     *
     * @method 分类子父排序
     * @version
     * @User: bevan
     * @param array $menus
     * @param int $pId
     * @param int $deep
     * @return
     */
    function sortWithDeep($menus = [], $pId = 0, $deep = 0): Array
    {
        static $sortMenu = [];
        foreach ($menus as $menu) {
            if ($menu['parent_id'] == $pId) {
                $menu['deep'] = $deep;
                $flag = str_repeat('└―', $deep);
                $menu['label'] = $flag . $menu['name'];
                $sortMenu[] = $menu;
                $this->sortWithDeep($menus, $menu['id'], $deep + 1);
            }
        }

        return $sortMenu;
    }

    /**
     *
     * @method 树状排序
     * @version
     * @User: bevan
     * @param array $menus
     * @param int $pId
     * @param int $deep
     * @return
     */
    function treeSort($menus = [], $pId = 0, $deep = 0): Array
    {
        $treeMenu = [];
        foreach ($menus as $k => $menu) {

            if ($menu['parent_id'] == $pId) {
                $menu['deep'] = $deep;
                $menu['label'] = str_repeat('└―', $deep) . $menu['name'];
                unset($menus[$k]);

                $key_name = 'children';
                $menu[$key_name] = $this->treeSort($menus, $menu['id'], $deep + 1);

                $treeMenu[] = $menu;
            }
        }

        return $treeMenu;
    }

    /**
     *
     * @method 去除自身 及 自身的 子分类
     * @version
     * @User: bevan
     * @param array $menus
     * @param int $pId
     * @return
     */
    function getWithoutSelfTree($menus = [], $pId = 0): Array
    {
        foreach ($menus as $key => $menu) {
            if ($menu['parent_id'] == $pId) {
                $pid = $menu['id'];
                unset($menus[$key]);
                $this->getWithoutSelfTree($menus, $pid);
            }
        }

        return $menus;
    }

    /**
     * 验证手机号码
     * @User: bevan
     * @param $phone
     * @return bool
     */
    function checkIsPhone($phone)
    {
        return preg_match("/^1[345678]{1}\d{9}$/",$phone);
    }

    function randomStr($len = 6)
    {
        $chars    = [
            'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k',
            'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v',
            'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G',
            'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R',
            'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '0', '1', '2',
            '3', '4', '5', '6', '7', '8', '9'
        ];
        $charsLen = count($chars) - 1;
        shuffle($chars);    // 将数组打乱
        $output = '';
        for ($i = 0; $i < $len; $i++) {
            $output .= $chars[mt_rand(0, $charsLen)];
        }
        return $output;
    }
}