<?php

/**
 * Todos os direitos reservados. Licenciado pela MUCRM.
 * Em caso de dúvidas, acesse: https://mucrm.com.br/docs
 */

namespace MUCRM\Http\Controllers\Admin;

use MUCRM\Engine\Support\Request;
use MUCRM\Http\Controllers\Controller;
use MUCRM\Models\Character;

class CharacterController extends Controller
{
    protected string $layout = "panels.admin.components.layouts.app";

    /**
     * Exibe a listagem paginada de personagens, com suporte a busca.
     *
     * @param Request $request
     * @return void
     */
    public function index(Request $request)
    {
        $characters = Character::query()->select("Name", "AccountID", "cLevel", "CtlCode");

        if ($search = $request->search) {
            $characters->where("Name", "like", "%{$search}%")
                ->orWhere("AccountID", "like", "%{$search}%");
        }

        $characters = $characters->paginate(12);

        $this->view("panels.admin.character.index", compact("characters"))->title(__lang('admin.characters'));
    }

    /**
     * Exibe o formulário de edição de um personagem.
     *
     * @param Request $request
     * @param Character $character
     * @return void
     */
    public function edit(Request $request, Character $character)
    {
        $this->view("panels.admin.character.edit", compact("character"))->title(__lang('admin.manage_character', ['name' => $character->Name]));
    }

    /**
     * Valida e atualiza os dados e status (pontos e nível) do personagem.
     *
     * @param Request $request
     * @param Character $character
     * @return mixed
     */
    public function update(Request $request, Character $character)
    {
        $validate = $request->validate([
            "Strength"  => "required|integer",
            "Dexterity" => "required|integer",
            "Vitality"  => "required|integer",
            "Energy"    => "required|integer",
            "Command"   => "nullable|integer",
            "Class"     => "required|integer",
            "cLevel"    => "required|integer",
            "CtlCode"   => "required|in:0,1," . config('user.code_staff'),
        ], [
            "Strength.required"  => __lang('validation.strength_required'),
            "Dexterity.required" => __lang('validation.agility_required'),
            "Vitality.required"  => __lang('validation.vitality_required'),
            "Energy.required"    => __lang('validation.energy_required'),
            "Class.required"     => __lang('validation.class_required'),
            "cLevel.required"    => __lang('validation.level_required'),
        ]);

        if (!(config('character.classes')[$validate['Class']]['active'] ?? false)) {
            return $request->messageError(__lang('admin.class_disabled'))->back();
        }

        $maxPoints = (int) str_replace(['.', ','], '', config('app.server.max_points'));
        $maxLevel  = (int) str_replace(['.', ','], '', config('app.server.max_level'));

        $levelError = match (true) {
            $validate['cLevel'] < 1         => __lang('admin.invalid_level'),
            $validate['cLevel'] > $maxLevel => __lang('admin.max_level_reached'),
            default                         => null
        };

        if ($levelError) {
            return $request->messageError($levelError)->back();
        }

        $stats = ['Strength' => 'Força', 'Dexterity' => 'Agilidade', 'Vitality' => 'Vitalidade', 'Energy' => 'Energia', 'Command' => 'Comando'];

        foreach ($stats as $key => $label) {
            if (($validate[$key] ?? 0) > $maxPoints) {
                return $request->messageError(__lang('admin.max_stat_reached', ['stat' => $label]))->back();
            }
        }

        $character->Strength  = $validate['Strength'];
        $character->Dexterity = $validate['Dexterity'];
        $character->Vitality  = $validate['Vitality'];
        $character->Energy    = $validate['Energy'];
        $character->Class     = $validate['Class'];
        $character->cLevel    = $validate['cLevel'];
        $character->CtlCode   = $validate['CtlCode'];

        if (in_array($character->Class, [64, 65])) {
            $character->Command = $validate['Command'];
        }

        $character->save();

        return $request->message(__lang('admin.msg.character_updated_success'))->back();
    }

    /**
     * Alterna o status de bloqueio do personagem (CtlCode).
     *
     * @param Request $request
     * @param Character $character
     * @return mixed
     */
    public function toggleBlock(Request $request, Character $character)
    {
        $character->CtlCode = $character->CtlCode == 1 ? 0 : 1;
        $character->save();

        return $request->message($character->CtlCode == 1 ? __lang('admin.msg.character_blocked_success') : __lang('admin.msg.character_activated_success'))
            ->back();
    }
}
