<?php
namespace App\Documents\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Domain\Notifications\Resources\NotificationCollection;


class DocumentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {

        return [
            'data' => [
                'id'            => $this->id,
                'type'          => 'document',
                'attributes'    => [
                    'name'                      => $this->name,
                    'fields'                      => $this->fields,
                    'meta_data'                 => $this->meta_data(),
                    'created_at'                => format_date($this->created_at, 'd. M. Y'),
                    'updated_at'                => format_date($this->updated_at, 'd. M. Y'),
                ],
                'relationships' => [ ],
                'meta'          => [],
            ],
        ];
    }


}
