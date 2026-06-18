<?php

use MUCRM\Engine\Support\Facades\Router;
use MUCRM\Http\Middlewares\UserAuth;
use MUCRM\Plugins\Market\Controllers\Character\{UserController, WebController};

Router::get("/market/characters", [WebController::class, 'index'])->name('plugins.market.character');
Router::post("/market/character/{id}", [WebController::class, 'buy'])->name('plugins.market.character.buy');

Router::group(['prefix' => 'user', 'middleware' => [UserAuth::class]], function () {
    Router::get("/market/characters", [UserController::class, 'index'])->name('plugins.market.character.index');
    Router::post("/market/characters", [UserController::class, 'store'])->name('plugins.market.character.store');

    Router::get("/market/character/ads", [UserController::class, 'ads'])->name('plugins.market.character.ads');
    Router::delete("/market/character/ads/{id}", [UserController::class, 'removeAds'])->name('plugins.market.character.delete');
});
