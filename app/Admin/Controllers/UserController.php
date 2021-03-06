<?php

namespace App\Admin\Controllers;

use App\Http\Requests\V1\AdminAppkeyUserRule;
use App\Models\AppUser;
use App\Models\PlatformProduct;
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
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redis;

class UserController extends Controller
{
    use ModelForm;
    use AppTool;

    public $title = '用户管理';


    public $states = [
        'on' => ['value' => 1, 'text' => '已激活', 'color' => 'success'],
        'off' => ['value' => 0, 'text' => '未激活', 'color' => 'default'],
    ];

    private $isedit = false;

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

        if (array_key_exists('password', $data) && empty($data['password'])) {
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
            $this->isedit = true;
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
        $this->isedit = false;
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
                $filter->scope('deleted_at', '已删除用户')->onlyTrashed();
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

            $input_query = Input::query();

//            dd( $request->input());
            $grid->actions(function ($actions) use ($input_query) {
                if (isset($input_query['_scope_']) && $input_query['_scope_'] == 'deleted_at') {
                    $actions->disableDelete();
                    $actions->disableEdit();
                } else {
                    $app_key_id = $actions->getKey('appuser.id');
                    $actions->append('<a href="' . url('/admin/auth/appkeyAuth/' . $app_key_id) . '/edit" title="查看编辑权限"><i class="fa fa-superpowers"></i></a>');
                }
//                    $actions->disableDelete();

//                $actions->disableEdit();
//                $actions->disableView();
            });

            $grid->paginate(20);
        });
    }

    public function editappkeyAuth($app_key_id, Content $content)
    {
        return $content
            ->header('appkey授权编辑')
            ->description('appkey授权编辑详情')
            ->body($this->appkeyform()->edit($app_key_id));
    }

    protected function appkeyform()
    {
        return Admin::form(AppUser::class, function (Form $form) {

            $form->tools(function (Form\Tools $tools) {

                // 去掉`列表`按钮
                $tools->disableList();

                // 去掉`删除`按钮
                $tools->disableDelete();

                // 去掉`查看`按钮
                $tools->disableView();

                // 添加一个按钮, 参数可以是字符串, 或者实现了Renderable或Htmlable接口的对象实例
                $tools->add('<a href="' . url('/admin/auth/frontUsers') . '" class="btn btn-sm btn-info"><i class="fa fa-arrow-left"></i>&nbsp;&nbsp;返回</a>');
            });

            $form->text('app_key', 'App key')->attribute(['disabled' => true]);
            $form->text('app_secret', 'App secret')->attribute(['disabled' => true]);
//            $form->hidden('user_id');
            $form->hidden('id');
//        $form->number('user_id', 'User id');
            $form->text('user.name', '用户')->attribute(['disabled' => true]);
            $form->multipleSelect('products', '授权服务产品')->options(PlatformProduct::all(['name', 'id'])->pluck('name', 'id'));

            $form->footer(function ($footer) {
                $footer->disableReset();
                $footer->disableViewCheck();
                $footer->disableEditingCheck();
            });

            $form->setAction('/admin/auth/updateAppkeyAuth');
        });
    }

    public function updateAppkeyAuth(AdminAppkeyUserRule $request)
    {
        //
        $appkey = AppUser::find($request->input('id'));

        $appkey->products()->sync(array_filter($request->input('products')));

        admin_toastr('授权编辑成功', 'success');
        return redirect('/admin/auth/frontUsers');
//        $user->()->sync([1, 2, 3]);
    }

    public function store(Request $request)
    {
        $data = $request->input();
        $data['type'] = $data['type'] == 'on' ? 1 : 0;
        if ($validationMessages = $this->form()->validationMessages($data)) {
            return back()->withInput()->withErrors($validationMessages);
        }
        $data['password'] = Hash::make($data['password']);

        DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'telephone' => $data['telephone'],
                'type' => $data['type'],
            ]);

            $user->appuser()->create([
                'app_key' => $data['appuser']['app_key'],
                'app_secret' => $data['appuser']['app_secret'],
                'user_id' => $user->id,
                'model' => 'App\User',
                'type' => 0
            ]);
        });

        admin_toastr('新增成功', 'success');
        return redirect('/admin/auth/frontUsers');
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
                if ($this->isedit) {
                    $form->text('appuser.app_key')->value($this->factoryUserAppkey())->attribute(['disabled' => true]);
                    $form->text('appuser.app_secret')->value($this->factoryUserAppkey())->attribute(['disabled' => true]);
                } else {
                    $form->text('appuser.app_key')->value($this->factoryUserAppkey());
                    $form->text('appuser.app_secret')->value($this->factoryUserAppkey());
                }
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

            $content->body(Admin::show(User::withTrashed()->findOrFail($id), function (Show $show) {

                $show->panel()
                    ->style('info')
                    ->title('用户基本信息')
                    ->tools(function ($tools) use ($show) {
                        //                        $tools->disableEdit();
                        if ($show->deleted_at()) {
                            $tools->disableDelete();
                            $tools->disableEdit();
                        }
                    });

                $show->id('ID');
                $show->name('名称');
                $show->email('邮箱');
                $show->telephone('手机号码');
                $show->created_at('创建时间');
                $show->updated_at('更新时间');
                $show->deleted_at('删除时间');

                $show->appuser('授权信息', function ($appuser) use ($show) {
                    $appuser->panel()
                        ->style('info')
                        ->tools(function ($tools) use ($show) {
                            if ($show->deleted_at()) {
                                $tools->disableDelete();
                                $tools->disableEdit();
                            }
                        });

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

    public function searchAppKeyUser($kw, $appkey = null)
    {
        $users = User::where('name', 'like', '%' . $kw . '%')->get(['id', 'name'])->map(function ($u, $k) {
            $u['text'] = $u['name'];
            return $u;
        });

        return $users;
    }
}
