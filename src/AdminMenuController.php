<?php

namespace Larrock\ComponentMenu;

use Breadcrumbs;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use JsValidator;
use Alert;
use Larrock\ComponentCategory\Models\Category;
use Larrock\ComponentMenu\Facades\LarrockMenu;
use Larrock\ComponentMenu\Models\Menu;
use Larrock\Core\AdminController;
use Larrock\Core\Component;
use Larrock\Core\Helpers\Tree;
use Validator;
use Redirect;
use View;

class AdminMenuController extends AdminController
{
    public function __construct()
    {
        $this->config = LarrockMenu::shareConfig();

        \Config::set('breadcrumbs.view', 'larrock::admin.breadcrumb.breadcrumb');
        Breadcrumbs::register('admin.'. LarrockMenu::getName() .'.index', function($breadcrumbs){
            $breadcrumbs->push(LarrockMenu::getTitle(), '/admin/'. LarrockMenu::getName());
        });
    }

    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        $tree = new Tree();
        $data['types_menu'] = LarrockMenu::getModel()->groupBy('type')->get(['type']);
        $data['data'] = [];
        foreach($data['types_menu'] as $type){
            $data['data'][$type->type] = $tree->build_tree(LarrockMenu::getModel()->orderBy('position', 'DESC')->whereType($type->type)->get());
        }

        return view('larrock::admin.menu.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create(Request $request)
    {
        $test = Request::create('/admin/menu', 'POST', [
            'title' => 'Новый материал',
            'url' => str_slug('novyy-material'),
            'active' => 0,
            'type' => 'default'
        ]);
        return $this->store($test);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), $this->config->valid);
        if($validator->fails()){
            return back()->withInput($request->except('password'))->withErrors($validator);
        }

        $data = LarrockMenu::getModel()->fill($request->all());
        $data->active = $request->input('active', 1);
        $data->position = $request->input('position', 0);
        $data->type = $request->input('type', 'default');

        if($request->get('parent') === ''){
            $data->parent = NULL;
        }

        if($data->save()){
            Alert::add('successAdmin', 'Пункт меню '. $request->input('title') .' добавлен')->flash();
        }else{
            Alert::add('errorAdmin', 'Пункт меню '. $request->input('title') .' не добавлен')->flash();
        }
        return redirect()->to('/admin/menu/'. $data->id .'/edit');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return View
     */
    public function edit($id)
    {
        $data['data'] = LarrockMenu::getModel()->findOrFail($id);

        //Добавляем поле поиска материалов
        $data['search'] = [];
        $components = config('larrock-admin-search');
        if(isset($components['components'])){
            foreach ($components['components'] as $item){
                if($item->searchable){
                    if(isset($item->rows['active'])){
                        $search_data = $item->model::whereActive(1)->get();
                    }else{
                        $search_data = $item->model::all();
                    }
                    foreach ($search_data as $value){
                        if($value->url && $value->title){
                            $data['search'][$value->url] = $value->title .'/'. $item->model;
                        }
                    }
                }
            }
        }

        $rows = LarrockMenu::getRows();
        $row['search_autocomplite_menu'] = new FormSelect('search_autocomplite_menu', 'Прикрепить к материалу');
        $row['search_autocomplite_menu']->setOptions($data['search'])->setHelp('При указании материала проставлять url вручную не нужно');
        $rows = $row + $rows;

        $data['app'] = LarrockMenu::overrideComponent('rows', $rows)->tabbable($data['data']);

        $validator = JsValidator::make(Component::_valid_construct(LarrockMenu::getConfig(), 'update', $id));
        View::share('validator', $validator);

        Breadcrumbs::register('admin.menu.edit', function($breadcrumbs, $data)
        {
            $breadcrumbs->parent('admin.'. LarrockMenu::getName() .'.index');
            $breadcrumbs->push($data->type, '/admin/menu#type-'. $data->type);
            $breadcrumbs->push($data->title);
        });

        return view('larrock::admin.admin-builder.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|Redirect
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), Component::_valid_construct(LarrockMenu::getConfig(), 'update', $id));
        if($validator->fails()){
            return back()->withInput($request->except('password'))->withErrors($validator);
        }

        $data = LarrockMenu::getModel()->find($id);

        $data->fill($request->all());
        $data->active = $request->input('active', 1);
        $data->position = $request->input('position', 0);
        if($request->get('parent') === ''){
            $data->parent = NULL;
        }
        if($request->get('search_autocomplite_menu')){
            $search = explode('/', $request->get('search_autocomplite_menu'));
            $model = new $search[1];
            $material = $model->whereTitle($search[0])->first();
            $data->url = $material->full_url;
            $data->connect = $material->full_url;
        }

        if($data->save()){
            \Cache::flush();
            Alert::add('successAdmin', 'Пункт меню '. $request->input('title') .' изменен')->flash();
        }else{
            Alert::add('errorAdmin', 'Пункт меню '. $request->input('title') .' не изменен')->flash();
        }
        return back()->withInput();
    }
}