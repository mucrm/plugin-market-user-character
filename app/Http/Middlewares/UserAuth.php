<?php

namespace MUCRM\Http\Middlewares;

use MUCRM\Engine\Support\Auth\Auth;

/**
 * Todos os direitos reservados. Licenciado pela MUCRM.
 * Em caso de dúvidas, acesse: https://mucrm.com.br/docs
 */

class UserAuth
{
    /**
     * Valida se o usuário está autenticado no sistema.
     *
     * @return bool
     */
    public function handle(): bool
    {
        if (!Auth::check('user')) {
            request()->redirect('home');

            return false;
        }

        return true;
    }
}
