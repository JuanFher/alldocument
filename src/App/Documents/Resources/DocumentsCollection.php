<?php
namespace App\Documents\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class DocumentsCollection extends ResourceCollection
{
    public $collects = DocumentResource::class;

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => $this->collection,
        ];
    }
}
