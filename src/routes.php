<?php

Route::group(['prefix' => 'admin'], function(){
    Route::resource('menu', 'Larrock\ComponentMenu\AdminMenuController');
});

Breadcrumbs::register('admin.'. LarrockMenu::getName() .'.index', function($breadcrumbs){
    $breadcrumbs->push(LarrockMenu::getTitle(), '/admin/'. LarrockMenu::getName());
});

Breadcrumbs::register('admin.menu.edit', function($breadcrumbs, $data)
{
    $breadcrumbs->parent('admin.'. LarrockMenu::getName() .'.index');
    $breadcrumbs->push($data->type, '/admin/menu#type-'. $data->type);
    $breadcrumbs->push($data->title);
});