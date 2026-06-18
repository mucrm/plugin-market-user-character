<?php

/**
 * Todos os direitos reservados. Licenciado pela MUCRM.
 * Em caso de dúvidas, acesse: https://mucrm.com.br/docs
 */

namespace MUCRM\Http\Controllers\Admin;

use MUCRM\Engine\Support\Request;
use MUCRM\Http\Controllers\Controller;
use MUCRM\Models\{Character, MembStat, User};

class DashboardController extends Controller
{
    protected string $layout = "panels.admin.components.layouts.app";

    /**
     * Exibe o painel de controle (Dashboard) com estatísticas gerais.
     *
     * @return mixed
     */
    public function __invoke()
    {
        $totalAccounts          = User::query()->count();
        $totalCharacters        = Character::query()->count();
        $playersOnline          = MembStat::query()->where('ConnectStat', 1)->count();
        $totalAccountsVip       = User::query()->where(config('app.vip.column'), '>', 0)->count();
        $totalAccountsBlocked   = User::query()->where('bloc_code', '=', 1)->count();
        $totalCharactersBlocked = Character::query()->where('CtlCode', '=', 1)->count();

        return $this->view("panels.admin.dashboard", compact('totalAccounts', 'totalCharacters', 'playersOnline', 'totalAccountsVip', 'totalAccountsBlocked', 'totalCharactersBlocked'))
            ->title(__lang('admin.dashboard.title'), __lang('admin.dashboard.welcome'));
    }

    /**
     * Limpa o cache de views e de framework do ecossistema.
     *
     * @param Request $request
     * @return mixed
     */
    public function clearCache(Request $request)
    {
        $pathViews     = base_path('storage/cache/views/*.php');
        $pathFramework = base_path('storage/framework/cache/*.{cache,php}');

        $files          = glob($pathViews);
        $filesFramework = glob($pathFramework, GLOB_BRACE);

        if ($files) {
            foreach ($files as $file) {
                @unlink($file);
            }
        }

        if ($filesFramework) {
            foreach ($filesFramework as $file) {
                @unlink($file);
            }
        }

        return $request->message(__lang('admin.dashboard.cache_cleared'))->back();
    }
}
