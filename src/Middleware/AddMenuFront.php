<?php

namespace Larrock\ComponentMenu\Middleware;

use View;
use Cache;
use Closure;
use LarrockMenu;

class AddMenuFront
{
    /**
     * Handle an incoming request.
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $menu = Cache::rememberForever('menu_front', function () {
            $get_types = LarrockMenu::getModel()->whereActive(1)->groupBy('type')->get();
            $menu = [];
            foreach ($get_types as $type) {
                $menu[$type->type] = LarrockMenu::getModel()->whereActive(1)->whereType($type->type)
                    ->whereParent(null)->with(['getChildActive'])->orderBy('position', 'DESC')->get();
            }

            return $menu;
        });

        $current_url = parse_url(\URL::current());
        if (! array_key_exists('path', $current_url)) {
            $current_url['path'] = '/';
        }

        $parse_url = parse_url(\URL::current());
        $explode_url = explode('/', array_get($parse_url, 'path'));

        $inter = [];

        foreach ($menu as $key_menu => $type) {
            $selected_key = null;
            $selected_diff = null;
            foreach ($type as $key_item => $item) {
                $parse_url_item = parse_url($item->url);
                $explode_url_item = explode('/', array_get($parse_url_item, 'path'));

                if (\count($explode_url) >= \count($explode_url_item)) {
                    $inter[$key_item] = array_intersect_assoc($explode_url, $explode_url_item);
                }
            }

            if (\count($inter) > 0) {
                $selected_key = array_search(max($inter), $inter);
                if (isset($menu[$key_menu][$selected_key])) {
                    $menu[$key_menu][$selected_key]->selected = true;

                    //Выбираем активный пункт в выпадающем меню
                    if ($type[$selected_key]->getChildActive) {
                        $inter_child = [];
                        foreach ($type[$selected_key]->getChildActive as $child_key => $child) {
                            $parse_url_child = parse_url($child->url);
                            $explode_url_child = explode('/', array_get($parse_url_child, 'path'));

                            if (\count($explode_url) >= \count($explode_url_child)) {
                                $inter_child[$child_key] = array_intersect_assoc($explode_url, $explode_url_child);
                            }
                        }

                        if (\count($inter_child) > 0) {
                            $selected_key_child = array_search(max($inter_child), $inter_child);
                            if (array_key_exists($selected_key_child, $type[$selected_key]->getChildActive)) {
                                $menu[$key_menu][$selected_key]->getChildActive[$selected_key_child]->selected = true;
                            }
                        }
                    }
                }
            }
            View::share('menu_'.$key_menu, $type);
        }

        return $next($request);
    }
}
