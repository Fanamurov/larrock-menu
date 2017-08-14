<?php

namespace Larrock\ComponentMenu;

use Larrock\ComponentMenu\Facades\LarrockMenu;
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
        $this->model = \config('larrock.models.menu', Menu::class);
        $this->addRows()->addPositionAndActive();
    }

    protected function addRows()
    {
        $row = new FormInput('title', 'Название пункта');
        $this->rows['title'] = $row->setValid('max:255|required')->setTypo()->setInTableAdmin();

        $row = new FormSelect('type', 'Тип меню');
        $this->rows['type'] = $row->setValid('required')
            ->setConnect($this->model)
            ->setInTableAdmin()->setDefaultValue('default');

        $row = new FormSelect('parent', 'Родитель');
        $this->rows['parent'] = $row->setConnect($this->model)->setOptionsTitle('title')->setDefaultValue('');

        $row = new FormInput('connect', 'Связь');
        $this->rows['connect'] = $row;

        $row = new FormInput('url', 'URL');
        $this->rows['url'] = $row->setValid('max:255|required');

        return $this;
    }

    public function renderAdminMenu()
    {
        $count = \Cache::remember('count-data-admin-'. LarrockMenu::getName(), 1440, function(){
            return LarrockMenu::getModel()->count(['id']);
        });
        return view('larrock::admin.sectionmenu.types.default', ['count' => $count, 'app' => LarrockMenu::getConfig(), 'url' => '/admin/'. LarrockMenu::getName()]);
    }
}