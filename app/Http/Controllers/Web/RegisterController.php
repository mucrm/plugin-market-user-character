<?php

/**
 * Todos os direitos reservados. Licenciado pela MUCRM.
 * Em caso de dúvidas, acesse: https://mucrm.com.br/docs
 */

namespace MUCRM\Http\Controllers\Web;

use MUCRM\Engine\DateTime\Time;
use MUCRM\Engine\Support\{RateLimiter, Request};
use MUCRM\Http\Controllers\Controller;
use MUCRM\Models\{User, ViCurrInfo};

class RegisterController extends Controller
{
    protected string $layout = "components.layouts.app";

    /**
     * Exibe o formulário de criação de conta (registro).
     *
     * @return mixed
     */
    public function index()
    {
        return $this->view("register")->title(__lang('web.register.page_title'));
    }

    protected array $message = [
        'account.required'               => 'validation.account_required',
        'account.unique'                 => 'validation.account_unique',
        'email.unique'                   => 'validation.email_unique',
        'personal_id.unique'             => 'validation.pid_unique',
        'name.required'                  => 'validation.name_required',
        'name.alpha'                     => 'validation.name_alpha',
        'name.min'                       => 'validation.name_min',
        'name.max'                       => 'validation.name_max',
        'account.alphanumeric'           => 'validation.account_alphanumeric',
        'account.min'                    => 'validation.account_min',
        'account.max'                    => 'validation.account_max',
        'email.email'                    => 'validation.email_invalid',
        'email.required'                 => 'validation.email_required',
        'password.required'              => 'validation.password_required',
        'password.min'                   => 'validation.password_min',
        'password.max'                   => 'validation.password_max',
        'password_confirmation.required' => 'validation.password_confirm_required',
        'password_confirmation.min'      => 'validation.password_confirm_min',
        'password_confirmation.max'      => 'validation.password_confirm_max',
        'personal_id.required'           => 'validation.pid_required',
        'personal_id.numeric'            => 'validation.pid_numeric',
        'personal_id.min'                => 'validation.pid_min',
        'personal_id.max'                => 'validation.pid_max',
        'password_confirmation.match'    => 'validation.password_mismatch',
    ];

    /**
     * Valida os dados e realiza o cadastro de uma nova conta no servidor.
     *
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        $rateLimiter = new RateLimiter();

        $key = 'register_attempts:' . $request->ip();

        if ($rateLimiter->tooManyAttempts($key, 1)) {
            $segundos = $rateLimiter->availableIn($key);

            $tempoMsg = $segundos >= 60 ? floor($segundos / 60) . " " . __lang('user.msg.minutes') : $segundos . " " . __lang('user.msg.seconds');

            return $request->messageError(__lang('user.msg.try_again_in', ['time' => $tempoMsg]))->back();
        }

        // Resolve translation keys to actual messages
        $translatedMessages = [];

        foreach ($this->message as $rule => $key) {
            $translatedMessages[$rule] = __lang($key);
        }

        $validate = $request->validate([
            "name"                  => "required|string|min:2|alpha|max:10",
            "account"               => "required|string|alphanumeric|min:4|max:10|unique:MEMB_INFO,memb___id",
            "email"                 => "email|unique:MEMB_INFO,mail_addr",
            "password"              => "required|string|min:6|max:10",
            "password_confirmation" => "required|string|min:6|max:10|match:password",
            'personal_id'           => 'required|numeric|min:7|max:7',
        ], $translatedMessages);

        $sign_up    = config('app.sign_up_bonus');
        $config_vip = config('app.vip');

        if ($sign_up['active']) {
            $validate[$config_vip['column']] = $sign_up['vip_type'];
            $name_vip                        = $config_vip['types'][$sign_up['vip_type']];

            $validate['AccountExpireDate'] = Time::now()->addDays($sign_up['time'])->toSql();
        }

        $this->createUser($validate, $config_vip);
        $this->createCurrInfo($validate);

        $message = __lang('user.msg.account_created');

        if ($sign_up['active']) {
            $message .= "\n" . __lang('user.msg.vip_bonus', ['days' => $sign_up['time'], 'vip' => $name_vip]);
        }

        $rateLimiter->hit($key, 5);

        return $request->message($message, "success")->back();
    }

    /**
     * Cria os dados monetários/iniciais na tabela ViCurrInfo (se aplicável).
     *
     * @param array $validate
     * @return void
     */
    private function createCurrInfo(array $validate): void
    {
        if (config('app.curr_info')) {
            $curr_info = new ViCurrInfo();
            $curr_info->create([
                'memb___id'      => $validate['account'],
                'memb_name'      => $validate['name'],
                'memb_guid'      => 1,
                'sno__numb'      => 7,
                'chek_code'      => 1,
                'used_time'      => 1234,
                'Bill_Section'   => 6,
                'Bill_Value'     => 3,
                'Bill_Hour'      => 6,
                'Surplus_Point'  => 6,
                'Surplus_Minute' => '2005-01-01 00:00:00.000',
                'Increase_Days'  => 0,
                'ends_days'      => 2005,
            ]);
        }
    }

    /**
     * Cria o registro principal da conta do usuário.
     *
     * @param array $validate
     * @param array $config_vip
     * @return void
     */
    private function createUser(array $validate, array $config_vip): void
    {
        $user = new User();
        $user->create([
            'memb___id'           => $validate['account'],
            'memb__pwd'           => $validate['password'],
            'memb_name'           => $validate['name'],
            'sno__numb'           => '111111' . $validate['personal_id'],
            'bloc_code'           => 0,
            'ctl1_code'           => 1,
            'mail_addr'           => $validate['email'],
            'appl_days'           => '2003-01-01 00:00:00',
            'mail_chek'           => 1,
            'AccountExpireDate'   => $validate['AccountExpireDate'] ?? null,
            $config_vip['column'] => $validate[$config_vip['column']] ?? 0,
        ]);
    }
}
