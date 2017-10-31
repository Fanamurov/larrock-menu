<?php

use Larrock\ComponentMenu\AdminMenuController;

Route::group(['prefix' => 'admin', 'middleware'=> ['web', 'level:2', 'LarrockAdminMenu', 'SaveAdminPluginsData', 'SiteSearchAdmin']], function(){
    Route::resource('menu', AdminMenuController::class);
});