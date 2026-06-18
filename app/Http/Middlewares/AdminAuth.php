<?php

namespace MUCRM\Http\Middlewares;

use MUCRM\Engine\Support\Session;

/**
 * Todos os direitos reservados. Licenciado pela MUCRM.
 * Em caso de dúvidas, acesse: https://mucrm.com.br/docs
 */

class AdminAuth
{
    /**
     * Valida se o usuário está autenticado no painel administrativo.
     *
     * @return bool
     */
    public function handle(): bool
    {
        if (!Session::has("admin_session")) {
            request()->redirect('admin.auth');

            return false;
        }

        return true;
    }
}
