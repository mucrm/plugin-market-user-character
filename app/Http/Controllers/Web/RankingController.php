<?php

/**
 * Todos os direitos reservados. Licenciado pela MUCRM.
 * Em caso de dúvidas, acesse: https://mucrm.com.br/docs
 */

namespace MUCRM\Http\Controllers\Web;

use Config;

use MUCRM\Engine\Support\Request;
use MUCRM\Http\Controllers\Controller;
use MUCRM\Models\{Character, Guild};

class RankingController extends Controller
{
    protected string $layout = "components.layouts.app";

    /**
     * Exibe a página principal de rankings.
     *
     * @return mixed
     */
    public function index()
    {
        return $this->view("ranking.index")->title(__lang('web.ranking.page_title'));
    }

    /**
     * Exibe um ranking específico com base no slug.
     *
     * @param Request $request
     * @param string $slug
     * @return mixed
     */
    public function show(Request $request, string $slug)
    {
        $config = Config::get("rankings.geral_stats");

        if (!$config || !($config['enable'] ?? false)) {
            return $request->redirect("rankings");
        }

        $targetRanking = current(array_filter($config['rankings'], fn ($item) => isset($item['slug']) && $item['slug'] === $slug));

        if (empty($targetRanking)) {
            return $request->redirect("rankings");
        }

        $config['rankings'] = $targetRanking;

        $ranking = self::generateRankings($config['limit'], $config['rankings']);

        return $this->view("ranking.{$config['style']}", compact('ranking'))->title($ranking['title']);
    }

    /**
     * Gera os dados do ranking solicitado com base na configuração.
     *
     * @param int $limit
     * @param array $ranking
     * @return array
     */
    private static function generateRankings(int $limit = 5, array $ranking): array
    {
        $table      = $ranking['table'];
        $column     = $ranking['column'];
        $setRanking = [];

        if ($table === 'Character') {
            $rows = Character::select("Character.Name as name, Character.cLevel as level, Character.Avatar as avatar, Guild.G_Name as guild, Guild.G_Mark as gmark, Character.{$column} as score")
                ->leftJoin("GuildMember", "GuildMember.Name", "=", "Character.Name")
                ->leftJoin("Guild", "Guild.G_Name", "=", "GuildMember.G_Name")
                ->where("Character.CtlCode", 0)
                ->orderBy("Character.{$column}", "DESC")
                ->limit($limit)
                ->get();
        } elseif ($table === 'Guild') {
            $rows = Guild::select("G_Name as name, G_Master as master, G_Mark as mark, Character.Avatar as avatar, {$column} as score")
                ->join("Character", "Character.Name", "=", "Guild.G_Master")
                ->orderBy($column, "DESC")
                ->limit($limit)
                ->get();
        } else {
            $rows = Character::select("Character.Name as name, Character.cLevel as level, Character.Avatar, Guild.G_Name as guild, Guild.G_Mark as gmark, r.{$column} as score")
                ->join($table . " as r", "r.Name", "=", "Character.Name")
                ->leftJoin("GuildMember", "GuildMember.Name", "=", "Character.Name")
                ->leftJoin("Guild", "Guild.G_Name", "=", "GuildMember.G_Name")
                ->where("Character.CtlCode", 0)
                ->orderBy("r.{$column}", "DESC")
                ->limit($limit)
                ->get();
        }

        foreach ($rows as $index => $row) {
            if ($table === 'Guild') {
                $setRanking[] = array_merge(['count' => $index + 1], self::mapGuildData($row));
            } else {
                $setRanking[] = array_merge(['count' => $index + 1], self::mapCharData($row));
            }
        }

        return [
            'title'    => $ranking['title'],
            "tag"      => ucfirst($ranking['tag']),
            'rankings' => $setRanking,
        ];
    }

    /**
     * Mapeia os dados do banco para o formato de ranking de personagens.
     *
     * @param object $row
     * @return array
     */
    private static function mapCharData($row)
    {
        return [
            "name"   => $row->name,
            "level"  => $row->level,
            "avatar" => avatar($row->avatar),
            "guild"  => $row->guild ?? null,
            "gmark"  => $row->gmark,
            "score"  => $row->score,
            "type"   => 'char',
        ];
    }

    /**
     * Mapeia os dados do banco para o formato de ranking de guildas.
     *
     * @param object $row
     * @return array
     */
    private static function mapGuildData($row)
    {
        return [
            "guild"  => $row->name,
            "master" => $row->master,
            "avatar" => avatar($row->avatar),
            "gmark"  => $row->mark,
            "score"  => $row->score,
            "type"   => 'guild',
        ];
    }
}
