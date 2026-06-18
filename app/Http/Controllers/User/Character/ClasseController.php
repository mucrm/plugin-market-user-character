<?php

/**
 * Todos os direitos reservados. Licenciado pela MUCRM.
 * Em caso de dúvidas, acesse: https://mucrm.com.br/docs
 */

namespace MUCRM\Http\Controllers\User\Character;

use MUCRM\Engine\Support\{Request};
use MUCRM\Http\Controllers\Controller;
use MUCRM\Models\Character;

class ClasseController extends Controller
{
    protected string $layout = "components.layouts.app";

    /**
     * Exibe o formulário de alteração de classe do personagem.
     *
     * @param Request $request
     * @param string $name
     * @return mixed
     */
    public function index(Request $request, string $name)
    {
        if (!config('user.character.previlegy.classe.active')) {
            return $request->redirect("user.panel");
        }

        $character = Character::verifyAndGet($name);

        if (!$character) {
            return $request->redirect("home");
        }

        return $this->view("user.character.classe", compact('character'))->title(__lang('user.classe.page_title'));
    }

    /**
     * Valida e atualiza a classe do personagem selecionado.
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

        $config = config('user.character.previlegy.classe');

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
            'classe' => 'numeric',
        ], [
            'classe.numeric' => __lang('validation.classe_numeric'),
        ]);

        $classes = array_filter(config('character.classes'), fn ($q) => $q['active'] === true);

        $classSelected = $classes[$validate['classe']] ?? null;

        if (!$classSelected) {
            return $request->message(__lang('user.msg.invalid_class'), "warning")->back();
        }

        $character->Class = $validate['classe'];
        $character->save();

        return $request->message(__lang('user.msg.class_changed', ['class' => $classSelected['name']]), "success")->back();
    }
}
