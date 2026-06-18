<?php

/**
 * Todos os direitos reservados. Licenciado pela MUCRM.
 * Em caso de dúvidas, acesse: https://mucrm.com.br/docs
 */

namespace MUCRM\Http\Controllers\User;

use MUCRM\Engine\Support\Auth\Auth;
use MUCRM\Engine\Support\{RateLimiter, Request};
use MUCRM\Http\Controllers\Controller;
use MUCRM\Models\User;

class AuthController extends Controller
{
    /**
     * Processa a requisição de login do usuário, aplicando rate limit.
     *
     * @param Request $request
     * @return mixed
     */
    public function login(Request $request)
    {
        $rateLimiter = new RateLimiter();

        $key = 'login_attempts:' . $request->ip();

        if ($rateLimiter->tooManyAttempts($key, 5)) {
            $segundos = $rateLimiter->availableIn($key);

            $tempoMsg = $segundos >= 60 ? floor($segundos / 60) . " " . __lang('user.msg.minutes') : $segundos . " " . __lang('user.msg.seconds');

            return $request->message(__lang('user.msg.try_again_in', ['time' => $tempoMsg]), "error")->back();
        }

        $credentials = $request->validate([
            "username"  => "required|string|min:4|max:10",
            "upassword" => "required|string|max:10",
        ], [
            "username.required"  => __lang('user.msg.login_required'),
            "upassword.required" => __lang('user.msg.password_required'),
        ]);

        $user = User::where("memb___id", $credentials['username'])
            ->where("memb__pwd", $credentials['upassword'])
            ->first();

        if (!$user) {
            $rateLimiter->hit($key, 2);

            return $request->message(__lang('user.msg.invalid_credentials'), "error")->back();
        }

        $rateLimiter->clear($key);

        Auth::login($user);

        return $request->redirect('user.panel');
    }
    /**
     * Realiza o logout do usuário e redireciona para a página inicial.
     *
     * @param Request $request
     * @return mixed
     */
    public function logout(Request $request)
    {
        Auth::logout();

        return $request->redirect("home");
    }
}
