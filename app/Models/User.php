<?php

/**
 * Todos os direitos reservados. Licenciado pela MUCRM.
 * Em caso de dúvidas, acesse: https://mucrm.com.br/docs
 */

namespace MUCRM\Models;

use MUCRM\Engine\Eloquent\Model;

/**
 * @property string $memb___id
 * @property string $memb__pwd
 * @property int $bloc_code
 */
class User extends Model
{
    protected string $table = "MEMB_INFO";

    protected bool $timestamps = false;

    protected string $primaryKey = "memb___id";

    protected array $guarded = [];

    protected $casts = [
        'AccountExpireDate' => 'datetime',
    ];

    protected $hidden = [
        'memb___pwd',
    ];

    public function characters()
    {
        return $this->hasMany(Character::class, 'AccountID', 'memb___id');
    }

    public function warehouse()
    {
        return $this->hasOne(Warehouse::class, 'AccountID', 'memb___id');
    }

    public function cashShopData()
    {
        return $this->hasOne(CashShopData::class, 'AccountID', 'memb___id');
    }

    public function membStat()
    {
        return $this->hasOne(MembStat::class, 'memb___id', 'memb___id');
    }

    public function accountCharacter()
    {
        return $this->hasOne(AccountCharacter::class, 'Id', 'memb___id');
    }

    public function accountPlan(): int
    {
        $vipColumn = config('app.vip.column');

        return (int) ($this->attributes[$vipColumn] ?? 0);
    }

    public function accountIsConnected()
    {
        return (bool) $this->membStat->ConnectStat == 1 ? true : false;
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'memb___id', 'memb___id');
    }

    public function getCoins()
    {

        $coinsConfig = config('user.coins') ?: [];
        $results     = [];
        $columnsMemb = [];

        foreach ($coinsConfig as $coin) {
            if ($coin['table'] === 'MEMB_INFO') {
                $columnsMemb[] = $coin['column'];
            }
        }

        $columnsString = implode(', ', $columnsMemb);
        $membInfoData  = !empty($columnsMemb)
            ? User::query()->select($columnsString)->where("memb___id", $this->memb___id)->first()
            : null;

        $cashShopData = $this->cashShopData;

        foreach ($coinsConfig as $key => $coin) {
            $value = 0;

            if ($coin['table'] === 'CashShopData' && $cashShopData) {
                $value = $cashShopData->{$coin['column']} ?? 0;
            } elseif ($coin['table'] === 'MEMB_INFO' && $membInfoData) {
                $value = $membInfoData->{$coin['column']} ?? 0;
            }

            $results[] = [
                'name'  => $coin['name'],
                'value' => $value,
                'coin'  => $key,
            ];
        }

        return $results;

    }

    public function donations()
    {
        return $this->hasMany(Donation::class, 'account', 'memb___id');
    }

    public function marketCharacters()
    {
        return $this->hasMany(MarketCharacter::class, 'username', 'memb___id');
    }
}
