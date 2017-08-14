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
                $menu[$type->type] = LarrockMenu::getModel()->whereActive(1)->whereType($type->type)->get();
            }
            return $menu;
        });
        foreach ($menu as $key => $type){
            View::share('menu_'. $key, $type);
        }

        return $next($request);
    }
}
