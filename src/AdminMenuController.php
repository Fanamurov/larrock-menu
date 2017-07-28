<?php

namespace Larrock\ComponentMenu;

use Breadcrumbs;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use JsValidator;
use Alert;
use Larrock\ComponentCategory\Models\Category;
use Larrock\ComponentMenu\Models\Menu;
use Larrock\Core\Component;
use Larrock\Core\Helpers\Tree;
use Validator;
use Redirect;
use View;

class AdminMenuController extends Controller
{
    /**
     * @var mixed   Конфиг компонента
     */
    protected $config;

    public function __construct()
    {
        $Component = new MenuComponent();
        $this->config = $Component->shareConfig();

        Breadcrumbs::setView('larrock::admin.breadcrumb.breadcrumb');
        Breadcrumbs::register('admin.'. $this->config->name .'.index', function($breadcrumbs){
            $breadcrumbs->push($this->config->title, '/admin/menu');
        });
    }

    /**
     * Display a listing of the resource.
     *
     * @param Tree                        $tree
     *
     * @return View
     */
    public function index(Tree $tree)
    {
        $data['types_menu'] = Menu::groupBy('type')->get(['type']);
        $data['data'] = [];
        foreach($data['types_menu'] as $type){
            $data['data'][$type->type] = $tree->build_tree(Menu::orderBy('position', 'DESC')->whereType($type->type)->get());
        }

        return view('larrock::admin.menu.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create()
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

        $data = new Menu();
        $data->fill($request->all());
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
        return back()->withInput();
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
        $data['data'] = Menu::findOrFail($id);
        $data['app'] = $this->config->tabbable($data['data']);

        $validator = JsValidator::make(Component::_valid_construct($this->config, 'update', $id));
        View::share('validator', $validator);

        Breadcrumbs::register('admin.menu.edit', function($breadcrumbs, $data)
        {
            $breadcrumbs->parent('admin.'. $this->config->name .'.index');
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
        $validator = Validator::make($request->all(), Component::_valid_construct($this->config, 'update', $id));
        if($validator->fails()){
            return back()->withInput($request->except('password'))->withErrors($validator);
        }

        $data = Menu::find($id);

        $data->fill($request->all());
        $data->active = $request->input('active', 1);
        $data->position = $request->input('position', 0);
        if($request->get('parent') === ''){
            $data->parent = NULL;
        }

        if($data->save()){
            \Cache::flush();
            Alert::add('successAdmin', 'Пункт меню '. $request->input('title') .' изменен')->flash();
        }else{
            Alert::add('errorAdmin', 'Пункт меню '. $request->input('title') .' не изменен')->flash();
        }
        return back()->withInput();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        if($data = Menu::find($id)){
            $name = $data->title;
            $Component = new MenuComponent();
            $Component->removeDataPlugins($this->config);

            if($data->delete()){
                \Cache::flush();
                Alert::add('successAdmin', 'Пункт меню '. $name .' успешно удален')->flash();
            }else{
                Alert::add('errorAdmin', 'Пункт меню '. $name .' не удален')->flash();
            }
        }else{
            Alert::add('errorAdmin', 'Такого пункта больше нет')->flash();
        }

        return Redirect::to('/admin/'. $this->config->name);
    }
}