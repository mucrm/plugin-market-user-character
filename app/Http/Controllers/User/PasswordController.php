<?php

/**
 * Todos os direitos reservados. Licenciado pela MUCRM.
 * Em caso de dúvidas, acesse: https://mucrm.com.br/docs
 */

namespace MUCRM\Http\Controllers\User;

use MUCRM\Engine\Support\{Request};
use MUCRM\Http\Controllers\Controller;

class PasswordController extends Controller
{
    protected string $layout = "components.layouts.app";

    /**
     * Exibe o formulário de alteração de senha do usuário.
     *
     * @return mixed
     */
    public function index()
    {

        return $this->view("user.password")->title(__lang('user.password.page_title'));
    }

    /**
     * Valida e atualiza a senha da conta do usuário.
     *
     * @param Request $request
     * @return mixed
     */
    public function update(Request $request)
    {
        $validate = $request->validate([
            'old_password' => 'required|min:1|alphanumeric|max:10|string',
            'password'     => 'required|min:1|alphanumeric|max:10|string|',
        ], [
            'old_password.required'     => __lang('validation.old_password_required'),
            'old_password.min'          => __lang('validation.old_password_min'),
            'old_password.max'          => __lang('validation.old_password_max'),
            'old_password.alphanumeric' => __lang('validation.old_password_alphanumeric'),
            'old_password.string'       => __lang('validation.old_password_string'),
            'password.required'         => __lang('validation.new_password_required'),
            'password.min'              => __lang('validation.new_password_min'),
            'password.max'              => __lang('validation.new_password_max'),
            'password.alphanumeric'     => __lang('validation.new_password_alphanumeric'),
            'password.string'           => __lang('validation.new_password_string'),
        ]);

        $user = auth();

        if ($validate['old_password'] != $user->memb__pwd) {
            return $request->message(__lang('user.msg.old_password_wrong'), "error")->back();
        }

        if ($validate['old_password'] == $validate['password']) {
            return $request->message(__lang('user.msg.password_same_as_old'), "warning")->back();
        }

        $user->memb__pwd = $validate["password"];
        $user->save();

        return $request->message(__lang('user.msg.password_updated'), "success")->back();
    }
}
