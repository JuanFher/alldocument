<?php
namespace App\Documents\Actions;

use App\Documents\Models\Document;
use App\Documents\DTO\CreateDocumentData;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Registered;

class CreateNewDocumentAction extends Controller
{

    /**
     * Validate and create a new user.
     *
     * @throws MeteredBillingPlanDoesntExist
     */
    public function __invoke(CreateDocumentData $data): Document
    {
    
        // Create document
        $document = $this->createDocument($data);
        event(new Registered($document));
        return $document;
    }



    private function createDocument(CreateDocumentData $data): Document
    {
        $document = Document::create([
            'fields' => $data->fields,
            'name'          => $data->name,
        ]);

        return $document;
    }
}
