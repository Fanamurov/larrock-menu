<?php

namespace Larrock\ComponentMenu;

use Larrock\ComponentMenu\Models\Menu;
use Larrock\Core\Component;
use Larrock\Core\Helpers\FormBuilder\FormInput;
use Larrock\Core\Helpers\FormBuilder\FormSelect;

class MenuComponent extends Component
{
    public function __construct()
    {
        $this->name = $this->table = 'menu';
        $this->title = 'Меню';
        $this->description = 'Навигация по сайту';
        $this->addRows()->addPositionAndActive();
    }

    protected function addRows()
    {
        $row = new FormInput('title', 'Название пункта');
        $this->rows['title'] = $row->setValid('max:255|required')->setTypo()->setInTableAdmin();

        $row = new FormSelect('type', 'Тип меню');
        $this->rows['type'] = $row->setValid('required')
            ->setConnect(Menu::class)
            ->setInTableAdmin()->setDefaultValue('default');

        $row = new FormSelect('parent', 'Родитель');
        $this->rows['parent'] = $row->setConnect(Menu::class)->setOptionsTitle('title')->setDefaultValue('');

        $row = new FormInput('connect', 'Связь');
        $this->rows['connect'] = $row;

        $row = new FormInput('url', 'URL');
        $this->rows['url'] = $row->setValid('max:255|required');

        return $this;
    }

    public function renderAdminMenu()
    {
        $count = \Cache::remember('count-data-admin-'. $this->name, 1440, function(){
            return Menu::count(['id']);
        });
        return view('larrock::admin.sectionmenu.types.default', ['count' => $count, 'app' => $this, 'url' => '/admin/'. $this->name]);
    }
}