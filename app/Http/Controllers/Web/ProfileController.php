<?php

/**
 * ============================================================================
 * MUCRM Ecosystem - Core Web Component
 * ============================================================================
 *
 * @package    MUCRM\Http\Controllers\Web
 * @author     MUCRM Team
 * @copyright  Todos os direitos reservados.
 * @link       https://mucrm.com.br/docs
 *
 * ============================================================================
 */

namespace MUCRM\Http\Controllers\Web;

use MUCRM\Engine\Support\Request;
use MUCRM\Http\Controllers\Controller;
use MUCRM\Models\{Character, Guild};

/**
 * Class ProfileController
 * Renderiza as páginas públicas de perfil para Personagens e Guildas.
 */
class ProfileController extends Controller
{
    protected string $layout = "components.layouts.app";

    /**
     * Direciona a requisição para o perfil correto com base no tipo informado na rota
     */
    public function show(Request $request, string $type, string $name)
    {
        if ($type !== "char" && $type !== "guild") {
            return $request->redirect("home");
        }

        $profile = $type === "guild" ? $this->profileGuild($name) : $this->profileChar($name);

        if (!$profile) {
            return $request->redirect("home");
        }

        $title = $type === "guild" ? "Guild: " . $profile->G_Name : "Char: " . $profile->Name;

        return $this->view("profile.{$type}", compact('profile'))->title($title);
    }

    /**
     * Retorna os dados do personagem vinculando informações de sua respectiva guilda
     */
    private function profileChar(string $name)
    {
        return Character::select("Character.*", "Guild.G_Name", "Guild.G_Mark")
            ->where("Character.Name", $name)
            ->leftJoin("GuildMember", "GuildMember.Name", "=", "Character.Name")
            ->leftJoin("Guild", "Guild.G_Name", "=", "GuildMember.G_Name")
            ->first();
    }

    /**
     * Retorna os dados da guilda, calcula os totais e popula a lista de membros ordenada por cargo
     */
    private function profileGuild(string $name)
    {
        $columnResets = config('user.character.columns_profile.resets');

        // Busca informações gerais da guilda e faz o cálculo de agregados da DB
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
            ->groupBy("Guild.G_Name", "Guild.G_Mark", "Guild.G_Master", "Guild.G_Score")
            ->first();

        // Se a guilda existir, busca a lista completa de membros ativos
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