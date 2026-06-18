<?php

/**
 * Todos os direitos reservados. Licenciado pela MUCRM.
 * Em caso de dúvidas, acesse: https://mucrm.com.br/docs
 */

namespace MUCRM\Http\Controllers\Web;

use MUCRM\Http\Controllers\Controller;
use MUCRM\Models\Download;

class DownloadController extends Controller
{
    protected string $layout = "components.layouts.app";

    /**
     * Exibe a página de downloads disponíveis.
     *
     * @return mixed
     */
    public function index()
    {
        $downloads = Download::get();

        return $this->view("download", compact('downloads'))->title(__lang('web.download.page_title'));
    }
}
