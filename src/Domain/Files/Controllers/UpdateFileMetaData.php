<?php
namespace Domain\Files\Controllers;

use Domain\Files\Models\File;
use App\Http\Controllers\Controller;
use Domain\Files\Requests\UploadRequest;
use Domain\Files\Resources\FileResource;
use App\Users\Exceptions\InvalidUserActionException;

class UpdateFileMetaData extends Controller
{
    /**
     * Upload file for authenticated master|editor user
     */
    public function __invoke(Request $request)
    {

        try {
            return response([], 201);
        } catch (InvalidUserActionException $e) {
            return response([
                'type'    => 'error',
                'message' => $e->getMessage(),
            ], 401);
        }
    }
}
