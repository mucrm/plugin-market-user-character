<?php

/**
 * Todos os direitos reservados. Licenciado pela MUCRM.
 * Em caso de dúvidas, acesse: https://mucrm.com.br/docs
 */

namespace MUCRM\Http\Controllers\Web;

use MUCRM\Engine\Support\Request;
use MUCRM\Http\Controllers\Controller;
use MUCRM\Models\{Character, Guild};

class ProfileController extends Controller
{
    protected string $layout = "components.layouts.app";

    /**
     * Exibe o perfil de um personagem ou guilda.
     *
     * @param Request $request
     * @param string $type
     * @param string $name
     * @return mixed
     */
    public function show(Request $request, string $type, string $name)
    {
        if ($type != "char" && $type != "guild") {
            return $request->redirect("home");
        }

        $profile = $type == "guild" ? $this->profileGuild($name) : $this->profileChar($name);

        if (!$profile) {
            return $request->redirect("home");
        }

        return $this->view("profile.{$type}", compact('profile'))->title($type == "guild" ? "Guild: " . $profile->G_Name : "Char: " . $profile->Name);
    }

    /**
     * Busca e retorna as informações do perfil de um personagem.
     *
     * @param string $name
     * @return mixed
     */
    private function profileChar(string $name)
    {
        $character = Character::select("Character.*", "Guild.G_Name, Guild.G_Mark")
            ->where("Character.Name", $name)
            ->leftJoin("GuildMember", "GuildMember.Name", "=", "Character.Name")
            ->leftJoin("Guild", "Guild.G_Name", "=", "GuildMember.G_Name")
            ->first();

        return $character;
    }

    /**
     * Busca e retorna as informações do perfil de uma guilda.
     *
     * @param string $name
     * @return mixed
     */
    private function profileGuild(string $name)
    {
        $columnResets = config('user.character.columns_profile.resets');

        $guild = Guild::select(
            "Guild.G_Name",
            "Guild.G_Mark",
            "Guild.G_Master",
            "Guild.G_Score",
            "SUM(Character.{$columnResets}) as TotalResets",
            "COUNT(GuildMember.Name) as TotalMembers"
        )
            ->join("GuildMember", "GuildMember.G_Name", "=", "Guild.G_Name")
            ->join("Character", "Character.Name", "=", "GuildMember.Name")
            ->where("Guild.G_Name", $name)
            ->groupBy("Guild.G_Name, Guild.G_Mark, Guild.G_Master, Guild.G_Score")
            ->first();

        if ($guild) {
            $guild->members = Character::select(
                "Character.Name",
                "Character.cLevel",
                "Character.Class",
                "Character.{$columnResets} as Resets",
                "GuildMember.G_Status"
            )
                ->join("GuildMember", "GuildMember.Name", "=", "Character.Name")
                ->where("GuildMember.G_Name", $name)
                ->orderBy("GuildMember.G_Status", "DESC")
                ->orderBy("Character.{$columnResets}", "DESC")
                ->get();
        }

        return $guild;
    }
}
