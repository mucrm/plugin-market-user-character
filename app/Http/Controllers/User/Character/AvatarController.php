<?php

/**
 * Todos os direitos reservados. Licenciado pela MUCRM.
 * Em caso de dúvidas, acesse: https://mucrm.com.br/docs
 */

namespace MUCRM\Http\Controllers\User\Character;

use MUCRM\Engine\Support\{Request, UploadedFile};
use MUCRM\Http\Controllers\Controller;
use MUCRM\Models\Character;

class AvatarController extends Controller
{
    protected string $layout = "components.layouts.app";

    /**
     * Exibe o formulário de alteração de avatar do personagem.
     *
     * @param Request $request
     * @param string $name
     * @return mixed
     */
    public function index(Request $request, string $name)
    {
        if (!config('user.character.previlegy.avatar.active')) {
            return $request->redirect("user.panel");
        }

        $character = Character::verifyAndGet($name);

        if (!$character) {
            return $request->redirect("user.panel");
        }

        return $this->view("user.character.avatar", compact('character'))->title(__lang('user.avatar.page_title'));
    }

    /**
     * Valida e atualiza a imagem de avatar do personagem selecionado.
     *
     * @param Request $request
     * @return mixed
     */
    public function update(Request $request)
    {
        $config = config('user.character.previlegy.avatar');

        if (!$config['active']) {
            return $request->message(__lang('user.msg.feature_disabled'), "warning")->back();
        }

        $character = Character::verifyAndGet($request->name);

        if (!$character) {
            return $request->message(__lang('user.msg.character_not_found'), "error")->back();
        }

        $vipTypes = config('app.vip.types');

        if ($config['vip'] > auth()->accountPlan()) {
            return $request->message(__lang('user.msg.available_from_plan', ['plan' => $vipTypes[$config['vip']]]), "warning")->back();
        }

        if (UploadedFile::has("avatar_image")) {
            $file = UploadedFile::resolve("avatar_image");

            if (!$file->isValidMime(['image/gif', 'image/jpeg', 'image/png', 'image/webp'])) {
                return $request->withInput('avatar_image', __lang('user.msg.invalid_file_type'))->back();
            }

            if (!$file->isValidSize(1)) {
                return $request->withInput('avatar_image', __lang('user.msg.invalid_file_size'))->back();
            }

            ($character->Avatar)
                ? UploadedFile::delete("platform/avatar/{$character->Avatar}")
                : null;

            $extension = ($file->getMimeTypeOriginal() === 'image/gif') ? 'gif' : 'webp';

            $fileName = md5($file->getClientOriginalName() . time()) . '.' . $extension;

            ($file->getMimeTypeOriginal() == 'image/gif')
                ? $file->store("platform/avatar", $fileName)
                : $file->storeAsWebp("platform/avatar", $fileName, 300, 300, 80);

            $character->Avatar = $fileName;
            $character->save();

            return $request->message(__lang('user.msg.avatar_updated'), "success")->back();
        }

        return $request->message(__lang('user.msg.select_avatar_image'), "error")->back();
        ;
    }
}
