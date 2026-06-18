<?php

/**
 * Todos os direitos reservados. Licenciado pela MUCRM.
 * Em caso de dúvidas, acesse: https://mucrm.com.br/docs
 */

namespace MUCRM\Models;

use MUCRM\Engine\Eloquent\Concerns\HasUuids;
use MUCRM\Engine\Eloquent\Model;

class MarketCharacter extends Model
{
    use HasUuids;

    protected string $table = "mucrm_market_characters";

    protected string $primaryKey = "id";

    protected $fillable = ['username', 'price', 'character_name'];

    public function character()
    {
        return $this->hasOne(Character::class, 'Name', 'character_name');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'username', 'username');
    }

}
