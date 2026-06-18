<?php

/**
 * Todos os direitos reservados. Licenciado pela MUCRM.
 * Em caso de dúvidas, acesse: https://mucrm.com.br/docs
 */

namespace MUCRM\Http\Controllers\User;

use MUCRM\Engine\Support\{Request};
use MUCRM\Http\Controllers\Controller;

class PIDController extends Controller
{
    protected string $layout = "components.layouts.app";

    /**
     * Exibe o formulário de alteração do Personal ID.
     *
     * @return mixed
     */
    public function index()
    {
        return $this->view("user.pid")->title(__lang('user.pid.page_title'));
    }

    /**
     * Valida e atualiza o Personal ID numérico da conta do usuário.
     *
     * @param Request $request
     * @return mixed
     */
    public function update(Request $request)
    {
        $validate = $request->validate([
            'pid' => 'required|min:7|numeric|max:7|string',
        ], [
            'pid.required' => __lang('validation.pid_field_required'),
            'pid.min'      => __lang('validation.pid_field_min'),
            'pid.max'      => __lang('validation.pid_field_max'),
            'pid.numeric'  => __lang('validation.pid_field_numeric'),
            'pid.string'   => __lang('validation.pid_field_string'),
        ]);

        $user            = auth();
        $user->sno__numb = "111111{$validate['pid']}";
        $user->save();

        return $request->message(__lang('user.msg.pid_updated'), "success")->back();
    }
}
