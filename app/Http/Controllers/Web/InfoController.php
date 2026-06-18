<?php

/**
 * Todos os direitos reservados. Licenciado pela MUCRM.
 * Em caso de dúvidas, acesse: https://mucrm.com.br/docs
 */

namespace MUCRM\Http\Controllers\Web;

use MUCRM\Http\Controllers\Controller;

class InfoController extends Controller
{
    protected string $layout = "components.layouts.app";

    /**
     * Exibe a página de informações do servidor.
     *
     * @return mixed
     */
    public function index()
    {
        return $this->view("info")->title(__lang('web.info.page_title'));
    }
}
