<?php

/**
 * Todos os direitos reservados. Licenciado pela MUCRM.
 * Em caso de dúvidas, acesse: https://mucrm.com.br/docs
 */

namespace MUCRM\Http\Controllers\Admin;

use MUCRM\Engine\Support\{Request};
use MUCRM\Engine\Support\Session;
use MUCRM\Http\Controllers\Controller;

class AuthController extends Controller
{
    protected string $layout = "panels.admin.components.layouts.auth";

    /**
     * Exibe a página de login do painel administrativo.
     *
     * @return mixed
     */
    public function index()
    {
        $this->view("panels.admin.auth.login")->title('Login');
    }

    /**
     * Processa a tentativa de login do administrador.
     *
     * @param Request $request
     * @return mixed
     */
    public function login(Request $request)
    {

        $validate = $request->validate([
            "account"  => "required|string|min:4|max:150",
            "password" => "required|string|min:4|max:150",
        ], [
            "account.required"  => __lang('validation.login_required'),
            "account.min"       => __lang('validation.login_min'),
            "account.max"       => __lang('validation.login_max'),
            "password.required" => __lang('validation.password_required'),
            "password.min"      => __lang('validation.password_min'),
            "password.max"      => __lang('validation.password_max'),
        ]);

        $config = config("app.admin");

        if ($validate['account'] == $config['username'] && $validate['password'] == $config['password']) {
            Session::set('admin_session', true);

            $request->message(__lang('admin.dashboard.welcome'), "success")->redirect("admin.dashboard");
        }

        $request->withInput("password", __lang('admin.invalid_credentials'))->back("admin.login");
    }

    /**
     * Realiza o logout do administrador e destrói a sessão.
     *
     * @param Request $request
     * @return mixed
     */
    public function logout(Request $request)
    {
        Session::regenerate();
        Session::forget("admin_session");

        return $request->redirect("admin.auth");
    }
}
