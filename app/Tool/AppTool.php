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
    function getWithoutSelfTree($menus = [], $pId = 0)
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
}