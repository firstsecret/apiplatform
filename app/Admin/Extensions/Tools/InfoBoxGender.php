<?php
/**
 * Created by Bevan.
 * User: Bevan@zhoubinwei@aliyun.com
 * Date: 2018/10/18
 * Time: 14:53
 */

namespace App\Admin\Extensions\Tools;

class InfoBoxGender extends \Encore\Admin\Widgets\InfoBox
{
    public function __construct(string $name, string $icon, string $color, string $link, string $info, string $view = 'admin.widgets.info-box')
    {
        parent::__construct($name, $icon, $color, $link, $info);

        $this->view = $view;
    }

    public function setView($v)
    {
        $this->view = $v;
    }

    public function getView()
    {
        return $this->view;
    }
}