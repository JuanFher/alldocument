<?php
namespace Domain\Admin\Controllers\Documents;

use App\Documents\Models\Document;
use Illuminate\Http\Response;
use App\Documents\DTO\CreateDocumentData;
use App\Http\Controllers\Controller;
use App\Documents\Resources\DocumentResource;
use App\Documents\Resources\DocumentsCollection;
use App\Documents\Actions\CreateNewDocumentAction;
use Domain\Admin\Requests\CreateDocumentByAdmin;

class DocumentController extends Controller
{
    public function __construct(
        protected CreateNewDocumentAction $createNewDocument,
    ) {
    }

    /**
     * Get all Documents
     */
    public function index(): UsersCollection
    {
        $documents = Document::sortable(['created_at', 'DESC'])
            ->paginate(15);

        return new DocumentsCollection($documents);
    }

    /**
     * Get user details
     */
    public function show(Documents $document): DocumentResource
    {
        return new DocumentResource($document);
    }

    /**
     * Create new user by admin
     */
    public function store(CreateDocumentByAdmin $request): Response
    {
        // Map user data
        $data = CreateDocumentData::fromArray([
            'fields'     => $request->input('fields'),
            'name'     => $request->input('name'),
        ]);

        // Register user
        try {
            $document = ($this->createNewDocument)($data);
        } catch (\Exception $e) {
            return response([
                'type'    => 'error',
                'message' => 'Documents registrations are temporarily disabled',
            ], 409);
        }

        $document->save();

        return response(new DocumentResource($document), 201);
    }
}
