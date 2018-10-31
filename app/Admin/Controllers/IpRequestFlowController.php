<?php

namespace App\Admin\Controllers;

use App\Models\IpRequestFlow;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class IpRequestFlowController extends Controller
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
        $time = date('Y-m-d', strtotime("-1 day"));
        return $content
            ->header('流量统计')
            ->description('流量统计(最新更新:' . $time . ')')
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
        $grid = new Grid(new IpRequestFlow);


        $grid->tools(function ($tools) {
            $tools->batch(function ($batch) {
                $batch->disableDelete();
            });
        });

        $grid->disableCreateButton();
        $grid->disableActions();

        $grid->filter(function ($filter) {

            // 去掉默认的id过滤器
            $filter->disableIdFilter();

            // 在这里添加字段过滤器
            $filter->like('request_uri', '请求uri');
            $filter->like('ip', 'ip');
            $filter->between('today_total_number', '请求数');
            $filter->between('created_at', '请求时间')->datetime();
        });

        $grid->model()->orderBy('created_at', 'desc');

        $grid->id('Id');
        $grid->ip('Ip');
        $grid->request_uri('请求uri');
        $grid->today_total_number('当日请求量');
        $grid->created_at('请求时间');
//        $grid->updated_at('Updated at');

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
        $show = new Show(IpRequestFlow::findOrFail($id));

        $show->id('Id');
        $show->ip('Ip');
        $show->request_uri('Request uri');
        $show->today_total_number('Today total number');
        $show->created_at('Created at');
        $show->updated_at('Updated at');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new IpRequestFlow);

        $form->ip('ip', 'Ip');
        $form->text('request_uri', 'Request uri');
        $form->number('today_total_number', 'Today total number')->default(1);

        return $form;
    }
}
