<?php

namespace App\Actions;

use App\Exceptions\AvatarException;
use App\Models\User;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class SetAvatarForUserAction
{
    private function deleteOldAvatarFile(?string $avatarUrl)
    {
        $avatarParts = explode('/', $avatarUrl);
        $oldAvatarFileName = end($avatarParts); // Get the last part directly

        if ($oldAvatarFileName != null) {
            $oldAvatarFilePath = 'avatars/'.$oldAvatarFileName;

            try {
                if (Storage::disk('public')->exists($oldAvatarFilePath)) {
                    Storage::disk('public')->delete($oldAvatarFilePath);
                }
            } catch (Exception $e) {
                throw AvatarException::cantDelete();
            }
        }

    }

    private function storeAvatarFile(UploadedFile $file, string $filename)
    {
        try {
            Storage::putFileAs('public/avatars', $file, $filename);
            $url = URL::to(Storage::url('avatars/'.$filename));
        } catch (Exception $e) {
            throw AvatarException::cantStore();
        }

        return $url;
    }

    public function handle(User $user, UploadedFile $file)
    {

        $this->deleteOldAvatarFile($user->avatar_url);

        $filename = $file->hashName();

        $url = $this->storeAvatarFile($file, $filename);

        $user->update([
            'avatar_url' => $url,
        ]);

    }
}
