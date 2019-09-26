<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ImageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'         => $this->id,
            'user'       => $this->user,
            // todo: 'logo' => $this->logo,
            'filename'   => $this->filename,
            'width'      => $this->width,
            'height'     => $this->height,
            'created_at' => $this->created_at,
        ];
    }
}
