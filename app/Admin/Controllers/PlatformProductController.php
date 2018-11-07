<?php

namespace App\Admin\Controllers;

use App\Http\Requests\V1\AdminPlatformProductRule;
use App\Models\PlatformProduct;
use App\Http\Controllers\Controller;
use App\Models\PlatformProductCategory;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Facades\Admin;
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

    protected $onlineStatus;

    public function __construct()
    {
        $this->init();
    }

    protected function init()
    {
        $this->onlineStatus = [
            'on' => ['value' => PlatformProduct::ONLINE_STATUS, 'text' => '上线', 'color' => 'success'],
            'off' => ['value' => PlatformProduct::DOWNLINE_STATUS, 'text' => '下线', 'color' => 'danger'],
        ];
    }

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

            $filter->scope('deleted_at', '已删除服务产品')->onlyTrashed();

            $filter->between('created_at', '创建时间')->datetime();
            $filter->between('updated_at', '更新时间')->datetime();
        });


        $grid->id('Id');
        $grid->name('产品名称');
        $grid->detail('产品描述')->limit(30);

//        $grid->category_id('Category id');
//        $grid->deleted_at('Deleted at');
        $grid->column('category.title', '所属分类');
        $grid->api_path('请求uri');
        $grid->internal_api_path('内部请求uri');
        $grid->request_method('请求方式');
        $grid->internal_request_method('内部请求uri');

        $grid->is_online('是否上线');

        $grid->created_at('创建时间');
        $grid->updated_at('更新时间');

        $input_query = Input::query();

        $script = $this->getRecoverScript();
        Admin::script($script);

        $grid->actions(function ($actions) use ($input_query) {
            if (isset($input_query['_scope_']) && $input_query['_scope_'] == 'deleted_at') {
                $actions->disableDelete();
                $actions->disableEdit();
                // 添加 恢复按钮
                $platform_product_id = $actions->getKey('id');
                $actions->append('<a href="#"  class="recoverPlatformProduct" data-href="' . url('/admin/api/recoveryPlatformProduct/' . $platform_product_id) . '" title="恢复"><i class="fa fa-recycle"></i></a>');


            }
//                    $actions->disableDelete();

//                $actions->disableEdit();
//                $actions->disableView();
        });

        return $grid;
    }

    protected function getRecoverScript()
    {
        return <<<'SCRIPT'
$('.recoverPlatformProduct').on('click', function(){
        var url = $(this).data('href')
    
        swal({
            title: "是否恢复吗",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "恢复",
            showLoaderOnConfirm: true,
            closeOnConfirm: false,
            cancelButtonText: "取消",
            preConfirm: function() {
                return new Promise(function(resolve) {
                    $.ajax({
                        method: 'get',
                        url: url,
                        data: {
                            _token:LA.token
                        },
                        success: function (data) {
                            $.pjax.reload('#pjax-container');
    
                            resolve(data);
                        }
                    });
    
                });
            }
        }).then(function(result){
            var data = result.value;
            if (typeof data === 'object') {
                if (data.status) {
                    swal(data.message, '', 'success');
                } else {
                    swal(data.message, '', 'error');
                }
            }
        });
        return false
})
SCRIPT;

    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $platformProduct = PlatformProduct::withTrashed()->findOrFail($id);
        $show = new Show($platformProduct);

        $show->panel()
            ->style('info')
            ->tools(function ($tools) {
                $tools->disableDelete();
            });

        $show->id('Id');
        $show->name('产品名称');
        $show->detail('简要描述');

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

        $show->is_online('是否上线');

        $show->created_at('创建时间');
        $show->updated_at('更新时间');

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
//        $update_data = [
//            'name' => $data['name'],
//            'detail' => $data['detail'],
//            'category_id' => $data['category_id'],
//            'api_path' => $data['api_path'],
//            'internal_api_path' => $data['internal_api_path'],
//            'request_method' => $data['request_method'],
//            'internal_request_method' => $data['internal_request_method'],
//        ];
//        if (isset($data['last_old_api_path'])) $update_data['last_old_api_path'] = $data['last_old_api_path'];
//        PlatformProduct::where('id', $id)->update($update_data);

        $platformProduct = PlatformProduct::find($id);

        $platformProduct->name = $data['name'];
        $platformProduct->detail = $data['detail'];
        $platformProduct->category_id = $data['category_id'];
        $platformProduct->api_path = $data['api_path'];
        $platformProduct->internal_api_path = $data['internal_api_path'];
        $platformProduct->request_method = $data['request_method'];
        $platformProduct->internal_request_method = $data['internal_request_method'];
        // handle online
        $platformProduct->is_online = $this->onlineStatus[$data['is_online']]['value'] == PlatformProduct::ONLINE_STATUS ? 1 : 0;

        if (isset($data['last_old_api_path'])) $platformProduct->last_old_api_path = $data['last_old_api_path'];

        $platformProduct->save();
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
            $pp = PlatformProduct::withTrashed()->find($this->modelPkId);
            $form->hidden('tmp_last_old_api_path')->default($pp->tmp_last_old_api_path);
//            var_dump($pp->tmp_last_old_api_path);
            $form->hidden('id')->default($this->modelPkId);
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
//
//        $states = [
//            'on' => ['value' => PlatformProduct::ONLINE_STATUS, 'text' => '上线', 'color' => 'success'],
//            'off' => ['value' => PlatformProduct::DOWNLINE_STATUS, 'text' => '下线', 'color' => 'danger'],
//        ];

        $form->switch('is_online', '是否上线')->default(PlatformProduct::ONLINE_STATUS)->states($this->onlineStatus);

        return $form;
    }

    /**
     *  recovery platform product
     */
    public function recovery($id)
    {
        PlatformProduct::withTrashed()
            ->where('id', $id)
            ->restore();

        // 先 下线
        PlatformProduct::where('id', $id)->update(['is_online' => 0]);

        return response()->json([
            'status' => true,
            'message' => '恢复成功',
        ]);
    }
}
