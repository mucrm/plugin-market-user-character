<?php

/**
 * Todos os direitos reservados. Licenciado pela MUCRM.
 * Em caso de dúvidas, acesse: https://mucrm.com.br/docs
 */

namespace MUCRM\Http\Controllers\User;

use MUCRM\Http\Controllers\Controller;

class InfoController extends Controller
{
    protected string $layout = "components.layouts.app";

    /**
     * Exibe as informações gerais da conta do usuário.
     *
     * @return mixed
     */
    public function index()
    {
        return $this->view("user.info")->title(__lang('user.my_info_title'));
    }
}
