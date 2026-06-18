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

use MUCRM\Engine\Support\Request;
use MUCRM\Http\Controllers\Controller;
use MUCRM\Models\{MarketCharacter, User};

/**
 * Class WebController
 * Gerencia a listagem pública do mercado e o processo de compra dos personagens.
 */
class WebController extends Controller
{
    protected string $layout = "components.layouts.app";

    public function __construct(protected $coin)
    {
        if (!config('app.plugins.market_character.active')) {
            request()->back('home');
        }

        $coins      = config('user.coins');
        $this->coin = $coins[config('app.plugins.market_character.coin')];
    }

    /**
     * Exibe a listagem pública de personagens à venda com filtros de busca e ordenação
     */
    public function index(Request $request)
    {
        $search = $request->input('search', null);
        $order  = $request->input('order', 'price_asc');

        $query = MarketCharacter::query()->with('character');

        if ($search) {
            $query->where('character_name', 'like', "%{$search}%");
        }

        if ($order == 'price_asc') {
            $query->orderBy("price", "asc");
        } elseif ($order == 'price_desc') {
            $query->orderBy("price", "desc");
        }

        $characters = $query->paginate(12);
        $coin       = $this->coin;

        return $this->view('Market.character.index', compact('characters', 'coin'))
            ->title(__lang('plugin.market.character.index'));
    }

    /**
     * Processa a compra do personagem utilizando travas de segurança pessimista (lockForUpdate)
     */
    public function buy(Request $request)
    {
        $adId = $request->route('id');

        try {
            \DB::transaction(function () use ($adId, $request) {

                // Bloqueia o anúncio para evitar concorrência ou compras simultâneas do mesmo char
                $ad = MarketCharacter::query()->with('character')->where('id', $adId)->lockForUpdate()->first();

                if (!$ad || !$ad->character) {
                    throw new \Exception(__lang('plugin.market.character.ad_not_found'));
                }

                $user = auth();

                if ($ad->username === $user->memb___id) {
                    throw new \Exception(__lang('plugin.market.character.cannot_buy_own'));
                }

                if ($user->accountIsConnected()) {
                    throw new \Exception(__lang('plugin.market.character.must_logout_game'));
                }

                $user->load('accountCharacter');
                $gameIDC = $this->getGameIDCEmpty($user->accountCharacter);

                if (!$gameIDC) {
                    throw new \Exception(__lang('plugin.market.character.account_full'));
                }

                $coin = $this->coin;

                // Executa o softLock no saldo do comprador dependendo da tabela configurada
                if ($coin['table'] === 'CashShopData') {
                    $userCoin = $user->cashShopData()->lockForUpdate()->first();
                } else {
                    $userCoin = get_class($user)::where('memb___id', $user->memb___id)->lockForUpdate()->first();
                }

                if (!$userCoin || $userCoin->{$coin['column']} < $ad->price) {
                    throw new \Exception(__lang('plugin.market.character.insufficient_funds'));
                }

                $seller = User::where('memb___id', $ad->username)->first();

                if (!$seller) {
                    throw new \Exception(__lang('user.msg.character_not_found'));
                }

                // Executa o softLock no saldo do vendedor (cria o registro no CashShop se não existir)
                if ($coin['table'] === 'CashShopData') {
                    $sellerCoin = $seller->cashShopData()->lockForUpdate()->first()
                        ?: $seller->cashShopData()->create(['AccountID' => $seller->memb___id]);
                } else {
                    $sellerCoin = User::where('memb___id', $seller->memb___id)->lockForUpdate()->first();
                }

                // Deduz os valores do comprador e bonifica o vendedor
                $userCoin->{$coin['column']} -= $ad->price;
                $userCoin->save();

                // Deduz a taxa percentual configurada e força o saldo final como número inteiro puro
                $discountPercentage = config('plugins.market_character.discount_percentage', 0);

                $finalSellerReceive = $ad->price - ($ad->price * ($discountPercentage / 100));
                $sellerCoin->{$coin['column']} += (int) $finalSellerReceive;
                $sellerCoin->save();

                // Altera a propriedade do personagem na DB do jogo e libera o acesso (CtlCode 0)
                $character            = $ad->character;
                $character->AccountID = $user->memb___id;
                $character->CtlCode   = 0;
                $character->save();

                // Vincula o nome do personagem no slot livre encontrado da conta do comprador
                $accountCharacter = $user->accountCharacter;

                if ($accountCharacter) {
                    $accountCharacter->$gameIDC = $character->Name;
                    $accountCharacter->save();
                }

                // Finaliza removendo o anúncio do mercado público
                $ad->delete();

                return true;
            });

            return $request->message(__lang('plugin.market.character.buy_success'), "success")->back();

        } catch (\Exception $e) {
            return $request->message($e->getMessage(), "error")->back();
        }
    }

    /**
     * Varre os slots de personagens da conta para encontrar um espaço vazio (GameID1 a GameID5)
     */
    private function getGameIDCEmpty(mixed $accountCharacter)
    {
        if (!$accountCharacter) {
            return 'GameID1';
        }

        $slots = ['GameID1', 'GameID2', 'GameID3', 'GameID4', 'GameID5'];

        foreach ($slots as $slot) {
            if (empty(trim($accountCharacter->$slot))) {
                return $slot;
            }
        }

        return false;
    }
}
