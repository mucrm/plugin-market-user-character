<?php

/**
 * Todos os direitos reservados. Licenciado pela MUCRM.
 * Em caso de dúvidas, acesse: https://mucrm.com.br/docs
 */

namespace MUCRM\Http\Controllers\User\Character;

use MUCRM\Engine\Support\Request;
use MUCRM\Http\Controllers\Controller;
use MUCRM\Models\Character;

class CharacterController extends Controller
{
    protected string $layout = "components.layouts.app";

    /**
     * Exibe a página principal de gerenciamento de um personagem específico.
     *
     * @param Request $request
     * @param string $name
     * @return mixed
     */
    public function index(Request $request, string $name)
    {
        $character = Character::verifyAndGet($name);

        if (!$character) {
            return $request->redirect("home");
        }

        return $this->view("user.character.index", compact('character'))->title($character->name);
    }
}
