<?php

namespace App\Models;

use Encore\Admin\Traits\AdminBuilder;
use Encore\Admin\Traits\ModelTree;
use Illuminate\Database\Eloquent\Model;

class PlatformProductCategory extends Model
{
    use ModelTree, AdminBuilder;

    public $title = 'title';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    //
    public function products()
    {
        return $this->hasMany('\App\Models\PlatformProduct', 'category_id', 'id');
    }

//    public function getWithoutSelfCatgories($id)
//    {
//
//    }

    public function getAllCatgories()
    {
        $m = self::all(['title', 'id', 'detail', 'parent_id'])->sortBy(function ($category, $key) {
            return $category->order;
        })->toArray();

        $cm = collect($this->sortWithDeep($m));
//        dd(get_class_methods($m));

//        return self::all(['title', 'id', 'detail', 'parent_id']);
        // 添加顶级分类


        return $cm;
    }

    function sortWithDeep($menus = [], $pId = 0, $deep = 0): Array
    {
        static $sortMenu = [];
        foreach ($menus as $menu) {
            if ($menu['parent_id'] == $pId) {
                $menu['deep'] = $deep;
                $flag = str_repeat('└―', $deep);
                $menu['title'] = $flag . $menu[$this->title];
                $sortMenu[] = $menu;
                $this->sortWithDeep($menus, $menu['id'], $deep + 1);
            }
        }

        return $sortMenu;
    }

}
