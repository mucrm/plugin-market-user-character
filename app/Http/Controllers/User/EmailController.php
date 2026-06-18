<?php

/**
 * Todos os direitos reservados. Licenciado pela MUCRM.
 * Em caso de dúvidas, acesse: https://mucrm.com.br/docs
 */

namespace MUCRM\Http\Controllers\User;

use MUCRM\Engine\Support\{ Request};
use MUCRM\Http\Controllers\Controller;

class EmailController extends Controller
{
    protected string $layout = "components.layouts.app";

    /**
     * Exibe o formulário de alteração de e-mail do usuário.
     *
     * @return mixed
     */
    public function index()
    {

        return $this->view("user.email")->title(__lang('user.email.page_title'));
    }

    /**
     * Valida e atualiza o e-mail da conta do usuário.
     *
     * @param Request $request
     * @return mixed
     */
    public function update(Request $request)
    {
        $validate = $request->validate([
            'old_email' => 'required|email|min:4|max:200|string',
            'email'     => 'required|email|min:4|max:200|string|unique:MEMB_INFO,mail_addr,' . auth()->memb___id . ',memb___id',
        ], [
            'old_email.required' => __lang('validation.old_email_required'),
            'old_email.email'    => __lang('validation.old_email_valid'),
            'old_email.min'      => __lang('validation.old_email_min'),
            'old_email.max'      => __lang('validation.old_email_max'),
            'old_email.string'   => __lang('validation.old_email_string'),
            'email.required'     => __lang('validation.new_email_required'),
            'email.email'        => __lang('validation.new_email_valid'),
            'email.min'          => __lang('validation.new_email_min'),
            'email.max'          => __lang('validation.new_email_max'),
            'email.string'       => __lang('validation.new_email_string'),
            'email.unique'       => __lang('validation.new_email_unique'),
        ]);

        $user = auth();

        if ($validate['old_email'] != $user->mail_addr) {
            return $request->message(__lang('user.msg.old_email_wrong'), "error")->back();
        }

        if ($validate['old_email'] == $validate['email']) {
            return $request->message(__lang('user.msg.email_same_as_old'), "warning")->back();
        }

        $user->mail_addr = $validate["email"];
        $user->save();

        return $request->message(__lang('user.msg.email_updated'), "success")->back();
    }
}
