<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RealStateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'title' => $this->title,
            'description' => $this->description,
            'content'=> $this->content,
            'price' => $this->price,
            'bathrooms' => $this->bathrooms,
            'bedrooms' => $this->bedrooms,
            'property_area' => $this->property_area,
            'total_property_area' => $this->total_property_area,
            'slug' => $this->slug,
        ];
    }
}
