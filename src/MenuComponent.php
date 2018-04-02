<?php

namespace Larrock\ComponentMenu;

use Cache;
use LarrockMenu;
use Larrock\Core\Component;
use Larrock\ComponentMenu\Models\Menu;
use Larrock\Core\Helpers\FormBuilder\FormInput;
use Larrock\Core\Helpers\FormBuilder\FormSelect;
use Larrock\Core\Helpers\FormBuilder\FormSelectKey;

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
        $this->setRow($row->setValid('max:255|required')->setTypo()->setInTableAdmin()->setFillable());

        $row = new FormSelect('type', 'Тип меню');
        $this->setRow($row->setValid('required')->setAllowCreate()
            ->setConnect($this->model, null, 'type')->setDefaultValue('default')
            ->setCssClassGroup('uk-width-1-1 uk-width-1-3@m')->setFillable());

        $row = new FormSelectKey('parent', 'Родитель');
        $this->setRow($row->setConnect($this->model)->setOptionsTitle('title')->setOptionsKey('id')
            ->setDefaultValue('')->setCssClassGroup('uk-width-1-1 uk-width-1-3@m')->setFillable());

        $row = new FormInput('url', 'URL');
        $this->setRow($row->setValid('max:255|required')->setFillable()->setInTableAdminEditable());

        return $this;
    }

    public function renderAdminMenu()
    {
        $count = Cache::rememberForever('count-data-admin-'.LarrockMenu::getName(), function () {
            return LarrockMenu::getModel()->count(['id']);
        });

        return view('larrock::admin.sectionmenu.types.default', ['count' => $count, 'app' => LarrockMenu::getConfig(),
            'url' => '/admin/'.LarrockMenu::getName(), ]);
    }
}
