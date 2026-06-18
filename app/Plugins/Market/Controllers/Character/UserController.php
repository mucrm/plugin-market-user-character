<?php
/**
 * ============================================================================
 * MUCRM Ecosystem - Official Plugin
 * ============================================================================
 *
 * @package    MUCRM\Plugins\Market\Character
 * @author     MUCRM Team
 * @copyright  Todos os direitos reservados.
 * @link       https://mucrm.com.br/docs
 *
 * ============================================================================
 */

namespace MUCRM\Plugins\Market\Controllers\Character;

use MUCRM\Engine\Support\{Request};
use MUCRM\Http\Controllers\Controller;
use MUCRM\Models\Character;

/**
 * Class UserController
 * Gerencia as ações do usuário dentro do Mercado de Personagens.
 */
class UserController extends Controller
{
    protected string $layout = "components.layouts.app";

    public function __construct()
    {
        if (!config('app.plugins.market_character.active')) {
            request()->back('user.panel');
        }
    }

    /**
     * Lista os personagens da conta aptos para venda (CtlCode = 0)
     */
    public function index(Request $request)
    {
        $characters = auth()->characters->filter(fn($character) => $character->CtlCode == 0);

        return $this->view('Market.character.user.index', compact('characters'))
            ->title(__lang('plugin.market.character.sell_character'));
    }

    /**
     * Exibe os anúncios ativos do próprio usuário
     */
    public function ads(Request $request)
    {
        $characters = auth()->marketCharacters()->with('character')->get();

        return $this->view('Market.character.user.ads', compact('characters'))
            ->title(__lang('plugin.market.character.my_ads'));
    }

    /**
     * Cria o anúncio do personagem no mercado
     */
    public function store(Request $request)
    {
        $request->validate([
            'character' => 'required',
            'price'     => 'required|numeric',
        ]);

        $character = Character::verifyAndGet($request->character);

        if (!$character) {
            return $request->message(__lang('user.msg.character_not_found'), "error")->back();
        }

        // Conta precisa estar deslogada para não bugar o CtlCode
        if (auth()->accountIsConnected()) {
            return $request->message(__lang('plugin.market.character.must_logout_game'), "warning")->back();
        }

        // Bloqueia se o char estiver equipado ou com itens no inventário
        if ($this->verifyInventory($character->Inventory)) {
            return $request->message(__lang('plugin.market.character.character_contain_item'), "warning")->back();
        }

        // Altera o CtlCode para 1 para ocultar o personagem na seleção do jogo
        $character->CtlCode = 1;
        $character->save();

        auth()->marketCharacters()->create([
            'character_name' => $character->Name,
            'price'          => $request->price,
        ]);

        return $request->message(__lang('plugin.market.character.ads_success'), "success")->back();
    }

    /**
     * Cancela o anúncio e devolve o personagem para a conta
     */
    public function removeAds(Request $request)
    {
        $marketCharacter = auth()->marketCharacters()->find($request->id);

        if (!$marketCharacter) {
            return $request->message(__lang('plugin.market.character.character_not_found'), "error")->back();
        }

        // Libera o personagem de volta para o jogo
        $marketCharacter->character->CtlCode = 0;
        $marketCharacter->character->save();

        $marketCharacter->delete();

        return $request->message(__lang('plugin.market.character.character_removed'), "success")->back();
    }

    /**
     * Verifica se o inventário possui itens reais ignorando os hex vazios padrão
     */
    private function verifyInventory($inventory)
    {
        $hex      = bin2hex($inventory);
        $cleanHex = str_replace(['ffffffffff', '0000000000000000000000', 'f', 'F', '0'], '', $hex);

        if (!empty($cleanHex)) {
            return true;
        }

        return false;
    }
}
