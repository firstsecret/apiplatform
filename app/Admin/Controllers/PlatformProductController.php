<?php

namespace App\Admin\Controllers;

use App\Http\Requests\V1\AdminPlatformProductRule;
use App\Models\PlatformProduct;
use App\Http\Controllers\Controller;
use App\Models\PlatformProductCategory;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Support\Facades\Input;

class PlatformProductController extends Controller
{
    use HasResourceActions;

    public $title = '服务产品管理';

    private $modelPkId; // 当前模型的pk

    /**
     * support http methods
     * @var array
     */
    protected $support_http_methods = [
        'GET' => 'GET',
        'PUT' => 'PUT',
        'POST' => 'POST',
        'DELETE' => 'DELETE'
    ];

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
            ->description('服务产品列表')
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
            ->header($this->title)
            ->description('服务产品详情')
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
        $this->modelPkId = $id;
        return $content
            ->header($this->title)
            ->description('编辑服务产品')
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
            ->header($this->title)
            ->description('添加新的服务产品')
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

        $categorys = PlatformProductCategory::all(['title', 'id'])->pluck('title', 'id');

        // filter
        $grid->filter(function ($filter) use ($categorys) {
            $filter->disableIdFilter();

            $filter->like('name', '名称');

            $filter->equal('category_id', '分类')->select($categorys);

            $filter->between('created_at', '创建时间')->datetime();
            $filter->between('updated_at', '更新时间')->datetime();
        });


        $grid->id('Id');
        $grid->name('产品名称');
        $grid->detail('产品描述')->limit(30);
        $grid->created_at('创建时间');
        $grid->updated_at('更新时间');
//        $grid->category_id('Category id');
//        $grid->deleted_at('Deleted at');
        $grid->column('category.name', '所属分类');
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
        $platformProduct = PlatformProduct::findOrFail($id);
        $show = new Show($platformProduct);

        $show->panel()
            ->style('info')
            ->tools(function ($tools) {
                $tools->disableDelete();
            });

        $show->id('Id');
        $show->name('产品名称');
        $show->detail('简要描述');
        $show->created_at('创建时间');
        $show->updated_at('更新时间');
        $show->category('所属分类', function ($category) {
            $category->panel()
                ->style('info')
                ->tools(function ($tools) {
                    $tools->disableDelete();
                });

            $category->setResource('/admin/platformProductCategory');

            $category->title('分类名称');
            $category->detail('分类简述');
        });
//        $show->category_id('Category id');
        if ($platformProduct->deleted_at) $show->deleted_at('删除时间');
        $show->api_path('api uri')->default();
        $show->internal_api_path('内部请求api uri');
        $show->request_method('请求方式');
        $show->internal_request_method('内部请求方式');

        return $show;
    }

    /**
     * rewrite update
     * @param $id
     * @param AdminPlatformProductRule $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function update($id, AdminPlatformProductRule $request)
    {
        $data = $request->input();

        // update ?
        if ($data['tmp_last_old_api_path'] != $data['api_path']) $data['last_old_api_path'] = $data['tmp_last_old_api_path'];
        unset($data['tmp_last_old_api_path']);

        // validate
        $update_data = [
            'name' => $data['name'],
            'detail' => $data['detail'],
            'category_id' => $data['category_id'],
            'api_path' => $data['api_path'],
            'internal_api_path' => $data['internal_api_path'],
            'request_method' => $data['request_method'],
            'internal_request_method' => $data['internal_request_method'],
        ];
        if (isset($data['last_old_api_path'])) $update_data['last_old_api_path'] = $data['last_old_api_path'];
        PlatformProduct::where('id', $id)->update($update_data);

        admin_toastr(trans('admin.update_succeeded'));

        return redirect('/admin/platformProduct');
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new PlatformProduct);

        if ($this->modelPkId) {
            $pp = PlatformProduct::find($this->modelPkId);
            $form->hidden('tmp_last_old_api_path')->default($pp->tmp_last_old_api_path);
//            var_dump($pp->tmp_last_old_api_path);
        }

        $form->text('name', '产品服务名称')->rules('required', ['required' => '缺少服务产品名']);
        $form->text('detail', '简要描述')->rules('required', ['required' => '缺少服务产品描述']);
        $form->select('category_id', '所属分类')->options(PlatformProductCategory::all(['id', 'title'])->pluck('title', 'id'))->rules('required', ['required' => '必须选择分类']);
//        $form->number('category_id', 'Category id');
        $form->text('api_path', 'api uri')->rules('required', ['required' => '缺少请求uri']);

        $form->text('internal_api_path', '内部请求api uri')->rules('required', ['required' => '缺少内部请求uri'])->help('例:/api/test');
//        $form->text('request_method', '请求方式')->default('GET');
        $form->select('request_method', '请求方式')->options($this->support_http_methods)->default('GET');
//        $form->text('internal_request_method', '内部请求方式')->default('GET');
        $form->select('internal_request_method', '内部请求方式')->options($this->support_http_methods)->default('GET');

        return $form;
    }
}
