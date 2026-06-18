<?php

/**
 * Todos os direitos reservados. Licenciado pela MUCRM.
 * Em caso de dúvidas, acesse: https://mucrm.com.br/docs
 */

namespace MUCRM\Http\Controllers\Admin;

use MUCRM\Engine\Support\Request;
use MUCRM\Http\Controllers\Controller;
use MUCRM\Models\User;

/**
 * Class AccountController
 *
 * Controlador responsável pela gestão de contas de usuários no painel de administração.
 *
 * @package MUCRM\Http\Controllers\Admin
 */

class AccountController extends Controller
{
    protected string $layout = "panels.admin.components.layouts.app";

    /**
     * Exibe a listagem de contas de usuários, permitindo a busca e paginação.
     *
     * @param Request $request
     * @return void
     */
    public function index(Request $request)
    {
        $accounts = User::query()->select("memb___id", "mail_addr", "bloc_code");

        if ($search = $request->search) {
            $accounts->where("memb___id", "like", "%{$search}%")
                ->orWhere("mail_addr", "like", "%{$search}%");
        }

        $accounts = $accounts->paginate(12);

        $this->view("panels.admin.account.index", compact("accounts"))->title(__lang('admin.accounts'));
    }

    /**
     * Exibe a interface de edição para uma conta específica.
     *
     * @param Request $request
     * @param User $account
     * @return void
     */
    public function edit(Request $request, User $account)
    {
        $account->load("characters");
        $this->view("panels.admin.account.edit", compact("account"))->title(__lang('admin.manage_account', ['id' => $account->memb___id]));
    }

    /**
     * Valida e atualiza os dados principais de uma conta (senha, e-mail e status de bloqueio).
     *
     * @param Request $request
     * @param User $account
     * @return mixed
     */
    public function update(Request $request, User $account)
    {
        $validate = $request->validate([
            "memb__pwd" => "required",
            "mail_addr" => "required|email|unique:MEMB_INFO,mail_addr,{$account->memb___id},memb___id",
            "bloc_code" => "required|in:0,1",
        ], [
            "memb__pwd.required" => __lang('validation.password_required'),
            "mail_addr.required" => __lang('validation.email_required'),
            "mail_addr.unique"   => __lang('validation.email_unique'),
            "mail_addr.email"    => __lang('validation.email_invalid'),
            "bloc_code.in"       => __lang('validation.block_invalid'),
            "bloc_code.required" => __lang('validation.block_required'),
        ]);

        $account->memb__pwd = $validate['memb__pwd'];
        $account->mail_addr = $validate['mail_addr'];
        $account->bloc_code = $validate['bloc_code'];
        $account->save();

        return $request->message(__lang('admin.msg.account_updated_success'))->back();
    }

    /**
     * Atualiza o saldo de uma moeda (coin) específica para a conta selecionada.
     *
     * @param Request $request
     * @param User $account
     * @param string $coin
     * @return mixed
     */
    public function updateCoin(Request $request, User $account, string $coin)
    {
        $validate = $request->validate([
            "value" => "required|integer",
        ], [
            "value.required" => __lang('validation.value_required'),
            "value.integer"  => __lang('validation.value_integer'),
        ]);

        $coinConfig = config("user.coins.{$coin}");
        $coinColumn = $coinConfig['column'];

        if ($coinConfig['table'] == 'CashShopData' && is_null($account->cashShopData)) {
            $account->cashShopData()->create([
                "AccountID" => $account->memb___id,
                $coinColumn => $validate['value'],
            ]);

        } else if ($coinConfig['table'] == 'CashShopData' && !is_null($account->cashShopData)) {
            $account->cashShopData->{$coinColumn} = $validate['value'];
            $account->cashShopData->save();
        } else {
            $account->{$coinColumn} = $validate['value'];
            $account->save();
        }

        return $request->message(__lang('admin.msg.account_coins_updated_success'))
            ->back();
    }

    /**
     * Alterna o status de bloqueio da conta (bloqueia se estiver ativa, ativa se estiver bloqueada).
     *
     * @param Request $request
     * @param User $account
     * @return mixed
     */
    public function toggleBlock(Request $request, User $account)
    {
        $account->bloc_code = $account->bloc_code == 1 ? 0 : 1;
        $account->save();

        return $request->message($account->bloc_code == 1 ? __lang('admin.msg.account_banned_success') : __lang('admin.msg.account_unbanned_success'))
            ->back();
    }
}
