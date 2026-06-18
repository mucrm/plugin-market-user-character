<?php

/**
 * Todos os direitos reservados. Licenciado pela MUCRM.
 * Em caso de dúvidas, acesse: https://mucrm.com.br/docs
 */

namespace MUCRM\Http\Controllers\Web;

use MUCRM\Engine\Support\{RateLimiter, Request};
use MUCRM\Http\Controllers\Controller;
use MUCRM\Jobs\SendEmailRecovery;
use MUCRM\Models\User;

class RecoveryController extends Controller
{
    protected string $layout = "components.layouts.app";

    /**
     * Exibe a página principal de recuperação de senha.
     *
     * @return mixed
     */
    public function index()
    {
        return $this->view("recovery_password")->title(__lang('web.recovery.page_title'));
    }

    public function send(Request $request)
    {
        $rateLimiter = new RateLimiter();

        $key = 'recovery_attempts:' . $request->ip();

        if ($rateLimiter->tooManyAttempts($key, 1)) {
            $segundos = $rateLimiter->availableIn($key);

            $tempoMsg = $segundos >= 60 ? floor($segundos / 60) . " " . __lang('user.msg.minutes') : $segundos . " " . __lang('user.msg.seconds');

            return $request->messageError(__lang('user.msg.try_again_in', ['time' => $tempoMsg]))->back();
        }

        $request->validate([
            "email" => "required|email",
        ]);

        $user = User::query()->where("mail_addr", $request->input("email"))->first();

        if (!$user) {
            return $request->withInput('email', __lang('user.msg.email_not_found'))->back();
        }

        SendEmailRecovery::dispatch([
            'email'     => $user->mail_addr,
            'memb_name' => $user->memb_name,
            'memb___id' => $user->memb___id,
            'memb__pwd' => $user->memb__pwd,
        ]);

        $rateLimiter->hit($key, 5);

        return $request->message(__lang('user.msg.recovery_email_sent'), "success")->back();
    }
}
