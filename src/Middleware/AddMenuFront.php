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
                $menu[$type->type] = LarrockMenu::getModel()->whereActive(1)->whereType($type->type)
                    ->whereParent(NULL)->with(['get_childActive'])->orderBy('position', 'DESC')->get();
            }
            return $menu;
        });

        $current_url = parse_url(\URL::current());
        if( !array_key_exists('path', $current_url)){
            $current_url['path'] = '/';
        }

        foreach ($menu as $key_menu => $type){
            $selected_key = NULL;
            $selected_diff = NULL;
            foreach ($type as $key_item => $item){
                $parse_url = parse_url(\URL::current());
                $explode_url = explode('/', array_get($parse_url, 'path'));

                $parse_url_item = parse_url($item->url);
                $explode_url_item = explode('/', array_get($parse_url_item, 'path'));

                $diff = array_diff($explode_url_item, $explode_url);
                if(count($diff) === 0){
                    if( !$selected_key){
                        $selected_key = $key_item;
                        $selected_diff = count($explode_url) - count($explode_url_item);
                    }else{
                        if($selected_key <= (count($explode_url) - count($explode_url_item))){
                            $selected_key = $key_item;
                            $selected_diff = count($explode_url) - count($explode_url_item);
                        }
                    }
                }
            }

            if($selected_key){
                $type[$selected_key]->selected = TRUE;
            }

            $selected_key_child = NULL;
            $selected_diff_child = NULL;
            if($type[$selected_key]->get_childActive){
                foreach ($type[$selected_key]->get_childActive as $child_key => $child){
                    $parse_url_child = parse_url($child->url);
                    $explode_url_child = explode('/', array_get($parse_url_child, 'path'));

                    $diff = array_diff($explode_url_child, $explode_url);
                    if(count($diff) === 0){
                        if( !$selected_key_child){
                            $selected_key_child = $child_key;
                            $selected_diff_child = count($explode_url) - count($explode_url_child);
                        }else{
                            if($selected_key_child <= (count($explode_url) - count($explode_url_child))){
                                $selected_key_child = $child_key;
                                $selected_diff_child = count($explode_url) - count($explode_url_child);
                            }
                        }
                    }
                }
            }

            if($selected_key_child){
                $type[$selected_key]->get_childActive[$selected_key_child]->selected = TRUE;
            }

            View::share('menu_'. $key_menu, $type);
        }

        return $next($request);
    }
}