<?php

/**
 * Todos os direitos reservados. Licenciado pela MUCRM.
 * Em caso de dúvidas, acesse: https://mucrm.com.br/docs
 */

namespace MUCRM\Models;

use MUCRM\Engine\Eloquent\Model;

class Character extends Model
{
    protected string $table = "Character";

    protected bool $timestamps = false;

    protected string $primaryKey = "Name";

    public function account()
    {
        return $this->belongsTo(User::class, 'AccountID', 'memb___id');
    }

    public static function verifyAndGet(string $name)
    {
        $columns = config('user.character.columns_profile');

        return auth()->characters()
            ->select("Name", "Avatar", "Strength", "Dexterity", "Energy", "Class", "Inventory", "cLevel", "Vitality", $columns['resets'], $columns['mresets'], "Leadership", "MapNumber", "MapPosX", "MapPosY", $columns['pk'], $columns['hero'])
            ->where("Name", $name)
            ->first();
    }

    public static function getStaffs()
    {
        return self::select("Character.Name, MEMB_STAT.ConnectStat, AccountCharacter.GameIDC")
            ->join("AccountCharacter", "AccountCharacter.Id", "=", "Character.AccountID")
            ->join("MEMB_STAT", "MEMB_STAT.memb___id", "=", "Character.AccountID")
            ->where("Character.CtlCode", "=", config('user.code_staff'))
            ->get();
    }

}
