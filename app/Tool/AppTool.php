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
    function cmf_sort_with_deep($menus = [], $pId = 0, $deep = 0): Array
    {
        static $sortMenu = [];
        foreach ($menus as $menu) {
            if ($menu['parent_id'] == $pId) {
                $menu['deep']  = $deep;
                $flag          = str_repeat('└―', $deep);
                $menu['label'] = $flag . $menu['name'];
                $sortMenu[]    = $menu;
                $this->cmf_sort_with_deep($menus, $menu['id'], $deep + 1);
            }
        }

        return $sortMenu;
    }

// 应用公共文件
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
    function cmf_tree_sort($menus = [], $pId = 0, $deep = 0): Array
    {
        $treeMenu = [];
        foreach ($menus as $k => $menu) {

            if ($menu['parent_id'] == $pId) {
                $menu['deep']  = $deep;
                $menu['label'] = str_repeat('└―', $deep) . $menu['name'];
                unset($menus[$k]);

                $key_name        = 'children';
                $menu[$key_name] = $this->cmf_tree_sort($menus, $menu['id'], $deep + 1);

                $treeMenu[] = $menu;
            }
        }

        return $treeMenu;
    }
}