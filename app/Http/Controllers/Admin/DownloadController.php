<?php

/**
 * Todos os direitos reservados. Licenciado pela MUCRM.
 * Em caso de dúvidas, acesse: https://mucrm.com.br/docs
 */

namespace MUCRM\Http\Controllers\Admin;

use MUCRM\Engine\Support\{Request};

use MUCRM\Http\Controllers\Controller;
use MUCRM\Models\Download;

class DownloadController extends Controller
{
    protected string $layout = "panels.admin.components.layouts.app";

    /**
     * Exibe a lista de todos os downloads cadastrados.
     *
     * @return mixed
     */
    protected array $messages = [];

    public function __construct()
    {
        $this->messages = [
            'name.required'    => __lang('validation.download.name_required'),
            'name.min'         => __lang('validation.download.name_min'),
            'name.max'         => __lang('validation.download.name_max'),
            'name.unique'      => __lang('validation.download.name_unique'),
            'description.min'  => __lang('validation.download.description_min'),
            'description.max'  => __lang('validation.download.description_max'),
            'url.required'     => __lang('validation.download.url_required'),
            'url.url'          => __lang('validation.download.url_invalid'),
            'version.required' => __lang('validation.download.version_required'),
            'size.required'    => __lang('validation.download.size_required'),
            'size.integer'     => __lang('validation.download.size_integer'),
            'active.boolean'   => __lang('validation.download.active_boolean'),
        ];
    }

    /**
     * Exibe a lista de todos os downloads cadastrados.
     *
     * @return mixed
     */
    public function __invoke()
    {
        $downloads = Download::get();

        return $this->view("panels.admin.download.index", compact('downloads'))->title(__lang('admin.download.title'));
    }

    /**
     * Exibe o formulário para criar um novo link de download.
     *
     * @return mixed
     */
    public function create()
    {
        return $this->view("panels.admin.download.create")->title(__lang('admin.download.create'));
    }

    /**
     * Valida e armazena um novo download no banco de dados.
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
        $validate = $request->validate([
            'name'        => 'required|string|min:10|max:150|unique:mucrm_downloads,name',
            'description' => 'nullable|string|min:10|max:150',
            'url'         => 'required|url',
            'version'     => 'required|string',
            'size'        => 'required|integer',
            'active'      => 'boolean',
        ], $this->messages);

        $download = Download::create($validate);

        $request->message(__lang('admin.download.created'), "success")->back();
    }

    /**
     * Exibe o formulário de edição de um download específico.
     *
     * @param Download $download
     * @return mixed
     */
    public function edit(Download $download)
    {
        return $this->view("panels.admin.download.edit", compact('download'))->title(__lang('admin.download.editing', ['name' => $download->name]));
    }

    /**
     * Atualiza os dados de um download existente.
     *
     * @param Request $request
     * @param Download $download
     * @return void
     */
    public function update(Request $request, Download $download)
    {
        $validate = $request->validate([
            'name'        => 'required|string|min:10|max:150|unique:mucrm_downloads,name,' . $download->id,
            'description' => 'nullable|string|min:10|max:150',
            'url'         => 'required|url',
            'version'     => 'required|string',
            'size'        => 'required|integer',
            'active'      => 'boolean',
        ], $this->messages);

        $download->update($validate);

        $request->message(__lang('admin.download.updated'), "success")->back();
    }

    /**
     * Exclui um download do banco de dados.
     *
     * @param Request $request
     * @param Download $download
     * @return mixed
     */
    public function delete(Request $request, Download $download)
    {
        if (!$download) {
            return $request->message(__lang('admin.download.not_found'), "error")->back();
        }

        $download->delete();

        $request->message(__lang('admin.download.deleted'), "success")->back();
    }

    /**
     * Alterna o status de visibilidade/atividade do download.
     *
     * @param Request $request
     * @param Download $download
     * @return void
     */
    public function toggleActive(Request $request, Download $download)
    {
        $download->active = !$download->active;
        $download->save();

        $request->message(__lang('admin.download.updated'), "success")->back();
    }
}
