<?php

namespace App\Http\Controllers\Api;

use App\Api\ApiMessages;
use App\Http\Controllers\Controller;
use App\Models\RealStatePhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RealStatePhotoController extends Controller
{
    private $realStatePhoto;

    public function __construct(RealStatePhoto $realStatePhoto)
    {
        $this->realStatePhoto = $realStatePhoto;
    }

    public function setThumb($id, $realStateId)
    {
        try {
            $photo = $this->realStatePhoto->where('real_state_id', $realStateId)
                ->where('is_thumb', true);

            if ($photo->count()) $photo->first()->update(['is_thumb' => false]);

            $photo = $this->realStatePhoto->findOrFail($id);
            $photo->update(['is_thumb' => true]);

            return response()->json([
                'data' => [
                    'message' => 'thumb atualizada com sucesso!'
                ]
            ]);
        } catch (\Exception $e) {
            $message = new ApiMessages($e->getMessage());
            return response()->json($message->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $photo = $this->realStatePhoto->findOrFail($id);
            if ($photo->is_thumb) {
                $message = new ApiMessages('nao e possivel remover foto de thumb, selecione outra thumb e remova a imagem desejada!');
                return response()->json($message->getMessage());
            }
            Storage::disk('public')->delete($photo->photo);
            $photo->delete();

            return response()->json([
                'data' => [
                    'message' => 'foto removida com sucesso!'
                ]
            ]);
        } catch (\Exception $e) {
            $message = new ApiMessages($e->getMessage());
            return response()->json($message->getMessage());
        }
    }
}
