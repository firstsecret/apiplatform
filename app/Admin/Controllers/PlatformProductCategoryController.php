<?php

namespace App\Admin\Controllers;

use App\Models\PlatformProductCategory;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Traits\ModelTree;

class PlatformProductCategoryController extends Controller
{
    use HasResourceActions;
    use ModelTree;

    public $title = '服务产品分类';

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header($this->title)
            ->description('服务产品分类列表')
            ->body(PlatformProductCategory::tree());
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header($this->title)
            ->description('服务产品分类详情')
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header($this->title)
            ->description('服务产品分类编辑')
            ->body($this->form($id)->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header($this->title)
            ->description('服务产品分类创建')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new PlatformProductCategory);

        $grid->id('Id');
        $grid->parent_id('上级分类id');
        $grid->name('分类名');
        $grid->detail('分类简要描述');
        $grid->created_at('创建时间');
        $grid->updated_at('更新时间');

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(PlatformProductCategory::findOrFail($id));

        $show->id('Id');
        $show->parent_id('上级分类id');
        $show->name('分类名');
        $show->detail('分类简要描述');
        $show->created_at('创建时间');
        $show->updated_at('更新时间');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form($id = '')
    {
        // 获取 分类
        $ppCategoryModel = new PlatformProductCategory;
//        $categories = $id ? $ppCategoryModel->getWithoutSelfCatgories($id) : $ppCategoryModel->getAllCatgories();
        $categories = $ppCategoryModel->getAllCatgories();

        $form = new Form($ppCategoryModel);

//        $form->number('parent_id', '上级分类');
        $form->select('parent_id', '上级分类')->options($categories->pluck('title', 'id'))->rules('required', ['required' => '上级分类必选']);

        $form->text('name', '分类名称');
        $form->text('detail', '分类描述');

        return $form;
    }
}
