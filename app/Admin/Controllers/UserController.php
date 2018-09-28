<?php

namespace App\Admin\Controllers;

use App\Tool\AppTool;
use App\User;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Show;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use ModelForm;
    use AppTool;

    public $title = '用户管理';


    public $states = [
        'on' => ['value' => 1, 'text' => '已激活', 'color' => 'success'],
        'off' => ['value' => 0, 'text' => '未激活', 'color' => 'default'],
    ];

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header($this->title);
            $content->description('注册用户管理');

            $content->body($this->grid());
        });
    }

    public function update($id, Request $request)
    {
        $data = $request->input();

        if (array_key_exists('password',$data) && empty($data['password'])) {
            unset($data['password']);
            unset($data['password_confirmation']);
        }
        
        return $this->form()->update($id, $data);
    }

    /**
     * Edit interface.
     *
     * @param $id
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header($this->title);
            $content->description('用户编辑');

            $content->body($this->form()->edit($id));
        });
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {

            $content->header($this->title);
            $content->description('用户创建');

            $content->body($this->form());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(User::class, function (Grid $grid) {
            // 过滤
            $grid->filter(function ($filter) {
                // 去掉默认的id过滤器
                $filter->disableIdFilter();
                // 在这里添加字段过滤器
                $filter->like('name', '名称');
                $filter->scope('deleted_at', '软删除用户')->onlyTrashed();
//                $filter->equal('created_at')->datetime();
                $filter->between('created_at', '创建时间')->datetime();
                $filter->between('updated_at', '更新时间')->datetime();
                $filter->between('deleted_at', '删除时间')->datetime();
            });

            $grid->id('ID')->sortable();
            $grid->name('名称')->limit(30)->badge('danger');
            $grid->email('邮箱')->label('info');

//            $grid->appuser()->app_key();
            $grid->column('appuser.app_key')->display(function ($app_key) {
                return $app_key ? "<button type=\"button\" class=\"btn-xs btn btn-success\">$app_key</button>" : '';
            });
            $grid->column('appuser.app_secret')->display(function ($app_secret) {
                return $app_secret ? "<button type=\"button\" class=\"btn-xs btn btn-warning\">$app_secret</button>" : '';
            });

            $grid->appuser()->type('授权类型')->display(function ($key_type) {
                return $key_type ? "<button type=\"button\" class=\"btn-xs btn btn-warning active\">永久型</button>" : "<button type=\"button\" class=\"btn-xs btn btn-default active\">过期型</button>";
            });

//            $grid->appuser()->app_secret();
            $grid->appuser()->model();
            // 扩展 信息
//            $grid->column('expand','扩展信息')->expand(function () {
//
////                if (empty($this->profile)) {
////                    return '';
////                }
//                $this->profile = [];
//                $profile = array_only($this->profile->toArray(), ['homepage', 'gender', 'birthday', 'address', 'last_login_at', 'last_login_ip', 'lat', 'lng']);
//
//                return new Table([], $profile);
//
//            }, 'Profile');
            // 是否 永久型的 授权


            $grid->type('状态')->switch($this->states);
//            $grid->type('状态')->display(function ($type){
//               return $type ? "<button type=\"button\" class=\"btn-xs btn btn-success active\">已激活</button>" : "<button type=\"button\" class=\"btn-xs btn btn-default\">未激活</button>";
//            });
            $grid->deleted_at('是否删除')->display(function ($deleted_at) {
                return $deleted_at ? "<button type=\"button\" class=\"btn-xs btn btn-danger\">$deleted_at</button>" : "<button type=\"button\" class=\"btn-xs btn btn-success\">否</button>";
            });
            $grid->created_at('创建时间')->sortable();
            $grid->updated_at('更新时间')->sortable();

            $grid->paginate(20);
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(User::class, function (Form $form) {

            $form->tab('账户', function (Form $form) {
                $form->model()->makeVisible('password');
                $form->display('id', 'ID');
                $form->text('name', '名称')->rules('required', ['required' => '名称必须']);
                $form->email('email', '邮箱')->rules('required', ['required' => '邮箱必须']);
                $form->mobile('telephone', '电话')->rules('required', ['required' => '电话号码不能为空']);

                $form->switch('type', '用户状态')->value(1)->states($this->states);
//                $form->radio('type', '用户状态')->options([0 => '非激活', 1 => '已激活'])->default(1);
                $form->display('created_at', '创建时间');
                $form->display('updated_at', '更新时间');

            })->tab('账户密码', function (Form $form) {
                $form->password('password', '账户密码')->rules('confirmed', ['confirmed' => '两次密码不一致']);
                $form->password('password_confirmation', '确认密码');
            })->tab('AccessToken', function (Form $form) {
                $form->text('appuser.app_key')->value($this->factoryUserAppkey());
                $form->text('appuser.app_secret')->value($this->factoryUserAppkey());
                $form->text('appuser.model')->value('App\User');
                $form->radio('appuser.type', 'key类型')->options([0 => '非永久(过期型)', 1 => '永久授权'])->default(0);
            })->tab('个人信息', function (Form $form) {

            });

            $form->ignore(['password_confirmation']);
        });
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Content
     */
    public function show($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header($this->title);
            $content->description('用户详情');

            $content->body(Admin::show(User::findOrFail($id), function (Show $show) {

                $show->panel()
                    ->style('info')
                    ->title('用户基本信息')
                    ->tools(function ($tools) {
                        //                        $tools->disableEdit();
                    });;

                $show->id('ID');
                $show->name('名称');
                $show->email('邮箱');
                $show->telephone('手机号码');
                $show->created_at();
                $show->updated_at();

                $show->appuser('授权信息', function ($appuser) {
                    $appuser->app_key();
                    $appuser->app_secret();
                    $appuser->type('授权类型')->as(function ($type) {
                        return $type ? '永久授权型' : '过期型';
                    });
                    $appuser->model();
                });
//                $show->release_at();
            }));
        });
    }


//    public function store(Request $request)
//    {
//        $request->input('app_key', $this->factoryUserAppkey());
//        $request->input('app_secret', $this->factoryUserAppkey());
//    }

}
