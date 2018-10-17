<?php

namespace App\Admin\Controllers;

use App\Models\PlatformProduct;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class PlatformProductController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('Index')
            ->description('description')
            ->body($this->grid());
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
            ->header('Detail')
            ->description('description')
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
            ->header('Edit')
            ->description('description')
            ->body($this->form()->edit($id));
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
            ->header('Create')
            ->description('description')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new PlatformProduct);

        $grid->id('Id');
        $grid->name('产品名称');
        $grid->detail('产品描述')->limit(30);
        $grid->created_at('创建时间');
        $grid->updated_at('更新时间');
//        $grid->category_id('Category id');
//        $grid->deleted_at('Deleted at');
        $grid->column('category.name','所属分类');
        $grid->api_path('请求uri');
        $grid->internal_api_path('内部请求uri');
        $grid->request_method('请求方式');
        $grid->internal_request_method('内部请求uri');

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
        $show = new Show(PlatformProduct::findOrFail($id));

        $show->id('Id');
        $show->name('Name');
        $show->detail('Detail');
        $show->created_at('Created at');
        $show->updated_at('Updated at');
        $show->category_id('Category id');
        $show->deleted_at('Deleted at');
        $show->api_path('Api path');
        $show->internal_api_path('Internal api path');
        $show->request_method('Request method');
        $show->internal_request_method('Internal request method');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new PlatformProduct);

        $form->text('name', 'Name');
        $form->text('detail', 'Detail');
        $form->number('category_id', 'Category id');
        $form->text('api_path', 'Api path');
        $form->text('internal_api_path', 'Internal api path');
        $form->text('request_method', 'Request method')->default('GET');
        $form->text('internal_request_method', 'Internal request method')->default('GET');

        return $form;
    }
}
