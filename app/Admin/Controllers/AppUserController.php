<?php

namespace App\Admin\Controllers;

use App\Models\AppUser;
use App\Http\Controllers\Controller;
use App\Models\PlatformProduct;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class AppUserController extends Controller
{
    use HasResourceActions;

    protected $formAtrr = false;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('appkey列表')
            ->description('appkey列表')
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
            ->header('appkey详情')
            ->description('appkey详情')
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
        $this->formAtrr = true;
        return $content
            ->header('appkey编辑')
            ->description('appkey编辑详情')
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
        $grid = new Grid(new AppUser);

        $grid->id('Id');
        $grid->app_key('App key');
        $grid->app_secret('App secret');
//        $grid->user_id('User id');
        $grid->column('user.name', '所属用户');
        $grid->created_at('创建时间');
        $grid->updated_at('更新时间');
        $grid->model('Model');
        $grid->type('Type');


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
        $show = new Show(AppUser::findOrFail($id));

        $show->id('Id');
        $show->app_key('App key');
        $show->app_secret('App secret');
//        $show->user_id('User id');
//        $show->created_at('Created at');
//        $show->updated_at('Updated at');
        $show->model('Model');
        $show->type('授权类型')->as(function ($type) {
            return $type ? '永久授权型' : '过期型';
        });

        $show->user('用户信息', function ($user) {
            $user->panel()
                ->style('info')
                ->tools(function ($tools) {
                    $tools->disableDelete();
//                    $tools->disableEdit();
                });

            $user->setResource('/admin/auth/frontUsers');

            $user->name('名称');
            $user->telephone('电话');
            $user->email('邮箱');
            $user->craeted_at('创建时间');
        });

        $app_key_id = $id;

        $script = $this->getUnbindScript();
        Admin::script($script);
        // 授权关系
        $show->products('可用服务产品', function ($products) use ($app_key_id) {
            $products->tools(function ($tools) {
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
            });

            // fitler
            $products->filter(function ($filter) {
                $filter->disableIdFilter();
                $filter->like('name', '名称');
//                $filter->scope('deleted_at', '已删除服务')->onlyTrashed();
            });

            $products->setResource('/admin/platformProduct');

            $products->id('ID');
            $products->name('服务产品名称');
            $products->api_path('服务产品对外uri');
            $products->internal_api_path('内部请求的uri');
            $products->request_method('请求的方式');
            $products->internal_request_method('内部请求的方式');

            $products->actions(function ($actions) use ($app_key_id) {
                $platform_product_id = $actions->getKey('id');

                $btn = '<a data-href="/admin/api/unbindAppKeyPlatformProduct/' . $app_key_id . '/' . $platform_product_id . '" class="unbindAppKeyPlatformProduct"  title="移除该服务" href="#"><i class="fa fa-eraser"></i></a>';
                $actions->disableDelete();
//                $actions->disableEdit();
//                $actions->disableView();
                $actions->append($btn);
            });
        });


        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new AppUser);

        $form->text('app_key', 'App key')->attribute(['disabled' => $this->formAtrr]);
        $form->text('app_secret', 'App secret')->attribute(['disabled' => $this->formAtrr]);
//        $form->number('user_id', 'User id');
        $form->text('user.name', '用户')->attribute(['disabled' => $this->formAtrr]);
//        $form->select('user.name', '用户')->options('/admin/api/searchAppKeyUser');
//        $form->text('model', 'Model');
//        $form->switch('type', 'Type');
        $form->multipleSelect('products', '授权服务产品')->options(PlatformProduct::all(['name', 'id'])->pluck('name', 'id'));
        return $form;
    }


    protected function getUnbindScript()
    {
        $delete_confirm = trans('admin.unbind_confirm');
        $confirm = trans('admin.confirm');
        $cancel = trans('admin.cancel');
        return <<<EOT
        $('.unbindAppKeyPlatformProduct').on('click', function(){
     
            var url = $(this).data('href')
     
            swal({
                title: "$delete_confirm",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "$confirm",
                showLoaderOnConfirm: true,
                closeOnConfirm: false,
                cancelButtonText: "$cancel",
                preConfirm: function() {
                    return new Promise(function(resolve) {
                        $.ajax({
                            method: 'delete',
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
EOT;
    }

    public function unbindAppKeyPlatformProduct($app_key_id, $product_id)
    {
        AppUser::find($app_key_id)->products()->detach($product_id);

        return response()->json([
            'status' => true,
            'message' => trans('admin.unbind_succeeded'),
        ]);
    }
}
