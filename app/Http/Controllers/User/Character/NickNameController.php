<?php

/**
 * Todos os direitos reservados. Licenciado pela MUCRM.
 * Em caso de dúvidas, acesse: https://mucrm.com.br/docs
 */

namespace MUCRM\Http\Controllers\User\Character;

use MUCRM\Engine\Support\{Request};
use MUCRM\Http\Controllers\Controller;
use MUCRM\Models\{CashShopData, Character, User};

class NickNameController extends Controller
{
    protected string $layout = "components.layouts.app";

    /**
     * Exibe o formulário de alteração de apelido (nickname) do personagem.
     *
     * @param Request $request
     * @param string $name
     * @return mixed
     */
    public function index(Request $request, string $name)
    {
        if (!config('user.character.previlegy.nickname.active')) {
            return $request->redirect("user.panel");
        }

        $character = Character::verifyAndGet($name);

        if (!$character) {
            return $request->redirect("home");
        }

        return $this->view("user.character.nickname", compact('character'))->title(__lang('user.nickname.page_title'));
    }

    /**
     * Valida e atualiza o apelido do personagem, cobrando o valor se necessário.
     *
     * @param Request $request
     * @param string $name
     * @return mixed
     */
    public function update(Request $request, string $name)
    {
        if (auth()->accountIsConnected()) {
            return $request->message(__lang('web.shop.must_logout_game'), "warning")->back();
        }

        $config = config('user.character.previlegy.nickname');

        if (!$config['active']) {
            return $request->message(__lang('user.msg.feature_disabled'), "warning")->back();
        }

        $vipTypes = config('app.vip.types');

        if ($config['vip'] > auth()->accountPlan()) {
            return $request->message(__lang('user.msg.available_from_plan', ['plan' => $vipTypes[$config['vip']]]), "warning")->back();
        }

        $character = Character::verifyAndGet($name);

        if (!$character) {
            return $request->message(__lang('user.msg.character_not_found'), "error")->back();
        }

        $validate = $request->validate([
            'nickname' => 'required|min:4|alphanumeric|max:10|string|unique:Character,Name,' . auth()->memb___id . ',AccountID',
        ], [
            'nickname.required'     => __lang('validation.nickname_required'),
            'nickname.min'          => __lang('validation.nickname_min'),
            'nickname.max'          => __lang('validation.nickname_max'),
            'nickname.string'       => __lang('validation.nickname_string'),
            'nickname.unique'       => __lang('validation.nickname_unique'),
            'nickname.alphanumeric' => __lang('validation.nickname_alphanumeric'),
        ]);

        if ($validate['nickname'] == $character->Name) {
            return $request->message(__lang('user.msg.nickname_same'), "error")->back();
        }

        $collect = config('user.character.previlegy.nickname.collect');

        if ($collect['active']) {
            $price  = $collect['price'];
            $coin   = config('user.coins')[$collect['coin']];
            $column = $coin['column'];

            $coinDiscount = ($coin['table'] == 'CashShopData')
                ? CashShopData::select("AccountID, {$column}")->where("AccountID", auth()->memb___id)->first()
                : User::select("memb___id, {$column}")->where("memb___id", auth()->memb___id)->first();

            if (!$coinDiscount) {
                return $request->message(__lang('user.msg.coin_not_found'), "error")->back();
            }

            if ($coinDiscount->$column < $price) {
                return $request->message(__lang('user.msg.insufficient_funds', ['price' => $price, 'coin' => $coin['name']]), "error")->back();
            }

            $coinDiscount->$column -= $price;
            $coinDiscount->save();
        }

        $accountCharacter = auth()->accountCharacter;

        $gameIDC = array_search($character->Name, $accountCharacter->toArray()) ?: '';

        if ($gameIDC) {
            $accountCharacter->$gameIDC = $validate['nickname'];
            $accountCharacter->save();
        }

        $character->setAttribute('Name', $validate['nickname']);
        $character->save();

        return $request->message(__lang('user.msg.nickname_updated'), "success")->back("user.character.profile", ['name' => $character->Name]);
    }
}
