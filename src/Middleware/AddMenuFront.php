<?php

namespace Larrock\ComponentMenu\Middleware;

use Larrock\ComponentMenu\Facades\LarrockMenu;
use Larrock\ComponentMenu\MenuComponent;
use Larrock\ComponentMenu\Models\Menu;
use Cache;
use Closure;
use View;

class AddMenuFront
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $menu = Cache::remember('menu_front', 1440, function() {
            $get_types = LarrockMenu::getModel()->whereActive(1)->groupBy('type')->get();
            $menu = [];
            foreach ($get_types as $type){
                $menu[$type->type] = LarrockMenu::getModel()->whereActive(1)->whereType($type->type)->orderBy('position', 'DESC')->get();
            }
            return $menu;
        });

        $current_url = parse_url(\URL::current());
        if( !array_key_exists('path', $current_url)){
            $current_url['path'] = '/';
        }

        foreach ($menu as $key => $type){
            $current_selected_key = NULL;
            $current_selected_url = NULL;
            foreach ($type as $key_item => $item){
                if('/'. \Route::current()->uri() === $item->url ||
                    \Route::current()->uri() === $item->url ||
                    \Route::current()->getActionName() === $item->connect ||
                    starts_with($current_url['path'], $item->connect) ||
                    starts_with($current_url['path'], $item->url)){
                    if(strlen($item->url) >= strlen($current_url['path']) || strlen($current_url['path']) >= strlen($item->connect)){
                        if($current_selected_key){
                            if($current_selected_url <= $item->url){
                                $current_selected_key = $key_item;
                                $current_selected_url = $item->url;
                            }
                        }else{
                            $current_selected_key = $key_item;
                            $current_selected_url = $item->url;
                        }
                    }
                }
                if($current_selected_key !== NULL){
                    $type[$current_selected_key]->selected = TRUE;
                }
            }
            View::share('menu_'. $key, $type);
        }

        return $next($request);
    }
}