<?php

/**
 * Todos os direitos reservados. Licenciado pela MUCRM.
 * Em caso de dúvidas, acesse: https://mucrm.com.br/docs
 */

namespace MUCRM\Http\Controllers\User\Character;

use MUCRM\Engine\Support\{Request};
use MUCRM\Http\Controllers\Controller;
use MUCRM\Models\Character;

class MoveController extends Controller
{
    protected string $layout = "components.layouts.app";

    /**
     * Exibe o formulário de teletransporte (mover) para o personagem.
     *
     * @param Request $request
     * @param string $name
     * @return mixed
     */
    public function index(Request $request, string $name)
    {
        if (!config('user.character.previlegy.move.active')) {
            return $request->redirect("user.panel");
        }

        $character = Character::verifyAndGet($name);

        if (!$character) {
            return $request->redirect("home");
        }

        return $this->view("user.character.move", compact('character'))->title(__lang('user.move.page_title'));
    }

    /**
     * Valida e move o personagem para o mapa especificado.
     *
     * @param Request $request
     * @param string $name
     * @return mixed
     */
    public function update(Request $request, string $name)
    {
        if (auth()->accountIsConnected()) {
            return $request->message(__lang('web.shop.must_logout_game'), "warning")->back();
        }

        $config = config('user.character.previlegy.move');

        if (!$config['active']) {
            return $request->message(__lang('user.msg.feature_disabled'), "warning")->back();
        }

        $vipTypes = config('app.vip.types');

        if ($config['vip'] > auth()->accountPlan()) {
            return $request->message(__lang('user.msg.available_from_plan', ['plan' => $vipTypes[$config['vip']]]), "warning")->back();
        }

        $character = Character::verifyAndGet($name);

        if (!$character) {
            return $request->message(__lang('user.msg.character_not_found'), "error")->back();
        }

        $validate = $request->validate([
            'move' => 'numeric',
        ], [
            'move.numeric' => __lang('user.msg.select_valid_map'),
        ]);

        $moves = array_filter(config('map'), fn ($q) => $q['active'] === true);

        $moveSelected = $moves[$validate['move']] ?? null;

        if (!$moveSelected) {
            return $request->message(__lang('user.msg.invalid_map'), "warning")->back();
        }

        $born = explode("x", $moveSelected['born']);

        $character->MapPosX   = $born[0];
        $character->MapPosY   = $born[1];
        $character->MapNumber = $validate['move'];
        $character->save();

        return $request->message(__lang('user.msg.character_moved', ['map' => $moveSelected['name']]), "success")->back();
    }
}
