<?php

/**
 * Todos os direitos reservados. Licenciado pela MUCRM.
 * Em caso de dúvidas, acesse: https://mucrm.com.br/docs
 */

namespace MUCRM\Http\Controllers\Web;

use MUCRM\Http\Controllers\Controller;
use MUCRM\Models\Character;

class HomeController extends Controller
{
    protected string $layout = "components.layouts.app";

    /**
     * Exibe a página inicial (Home) do site.
     *
     * @return mixed
     */
    public function index()
    {
        $top_class = $this->getTopClass();

        return $this->view("home", compact('top_class'))->title(__lang('web.home.page_title'));
    }

    private function getTopClass()
    {
        if (!config('app.top_class.active')) {
            return null;
        }

        $classes = collect([
            'sm' => [0, 1],
            'bk' => [16, 17],
            'fe' => [32, 33],
            'mg' => [48],
            'dl' => [64, 65],
        ]);

        $column_resets = config('user.character.columns_profile.resets');

        return $classes->map(function ($class) use ($column_resets) {
            return Character::query()
                ->select('Name, Avatar,Class,' . $column_resets . ' as reset')
                ->whereIn('Class', $class)
                ->orderBy($column_resets, 'desc')
                ->first();
        });
    }
}
