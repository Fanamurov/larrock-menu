<?php

namespace Larrock\ComponentMenu\Middleware;

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
            return Menu::whereActive(1)->orderBy('position', 'DESC')->get();
        });
        View::share('menu', $menu);

        return $next($request);
    }
}
