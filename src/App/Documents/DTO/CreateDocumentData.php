<?php
namespace App\Documents\DTO;

use Spatie\DataTransferObject\DataTransferObject;

class CreateDocumentData extends DataTransferObject
{
    public string $fields;
    public string $name;

    public static function fromRequest($request): self
    {
        return new self([
            'name'            => $request->input('name'),
            'fields'           => $request->input('fields'),

        ]);
    }

    public static function fromArray(array $array): self
    {
        return new self([
            'name'            => $array['name'] ?? null,
            'email'           => $array['fields'] ?? null,
        ]);
    }
}
