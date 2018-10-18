<?php

namespace App\Admin\Controllers;

use App\Models\PlatformProduct;
use App\Models\Service;
use App\Http\Controllers\Controller;
use Encore\Admin\Admin;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Support\Facades\DB;


class NodeServicesController extends Controller
{
    use HasResourceActions;

    public $title = '节点管理';
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
            ->description('节点列表')
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
            ->description('节点详情')
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
            ->description('节点编辑')
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
            ->description('节点创建')
            ->body($this->form());
    }

    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            Service::find($id)->products()->detach();
            $this->form()->destroy($id);
        });
        return response()->json([
            'status' => true,
            'message' => trans('admin.delete_succeeded'),
        ]);
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Service);

        $grid->tools(function ($tools) {
//            $tools->batch(function ($batch) {
//                $batch->disableDelete();
//            });
        });

        $grid->id('Id');
        $grid->service_host('Service host');
        $grid->service_host_port('Service host port');
        $grid->created_at('Created at');
        $grid->updated_at('Updated at');

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
        $show = new Show(Service::findOrFail($id));
//        dd(get_class_methods($show));
        $show->panel()
            ->style('info')
            ->title('节点详情')
            ->tools(function ($tools) {
                //                        $tools->disableEdit();
                $tools->disableDelete();
                $tools->disableEdit();
            });

        $show->id('Id');
        $show->service_host('Service host');
        $show->service_host_port('Service host port');
        $show->created_at('Created at');
        $show->updated_at('Updated at');

//        $show->tools(function ($tools) {
////            $tools->batch(function ($batch) {
////                $batch->disableDelete();
////            });
//        });
        // add script
        $script = $this->getUnbindScript();
        $service_id = $id;

        Admin::script($script);

        $show->products('节点上服务列表', function ($products) use ($service_id) {

            $products->tools(function ($tools) {
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
            });

            // fitler
            $products->filter(function ($filter){
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

            $products->actions(function ($actions) use ($service_id) {
                $platform_product_id = $actions->getKey('id');

                $btn = '<a data-href="/admin/api/unbindServicePlatformProduct/' . $service_id . '/' . $platform_product_id . '" class="unbindServicePlatformProduct"  title="移除该服务" href=""><i class="fa fa-eraser"></i></a>';
                $actions->disableDelete();
//                $actions->disableEdit();
//                $actions->disableView();
                $actions->append($btn);
            });
        });

        return $show;
    }

//    public function store(Request $request)
//    {
//        dd($request->all());
//    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Service);

        $form->text('service_host', 'Service host')->default('127.0.0.1');
        $form->text('service_host_port', 'Service host port')->default('80');

        $form->multipleSelect('products', 'API产品服务')->options(PlatformProduct::all(['name', 'id'])->pluck('name', 'id'));

        return $form;
    }

    /**
     * api 解除服务产品绑定关系
     * @param $platform_product_id
     */
    public function unbindServicePlatformProduct($service_id, $product_id)
    {
//        throw new \Exception('异常');
        Service::find($service_id)->products()->detach($product_id);

        return response()->json([
            'status' => true,
            'message' => trans('admin.unbind_succeeded'),
        ]);

//        dd($platform_product_id);
    }

    /**
     * 解綁的 script
     * @return string
     */
    protected function getUnbindScript()
    {
        $delete_confirm = trans('admin.unbind_confirm');
        $confirm = trans('admin.confirm');
        $cancel = trans('admin.cancel');
        return <<<EOT
        $('.unbindServicePlatformProduct').on('click', function(){
     
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
}
