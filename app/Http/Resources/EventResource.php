<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            'id' => $this->id, //$this refers to the current event resource (Modal)
            'name' => $this->name,
            'description' => $this->description,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            // whenLoaded is a method that checks if the user relationship is loaded
            // and if it is, it returns the user resource
            'user' => new UserResource($this->whenLoaded('user')), 
            // AttendeeResource is a resource for the attendees relationship
            // and if it is, it returns a collection of attendees resources
            'attendees' => AttendeeResource::collection(
                $this->whenLoaded('attendees')
            )
        ];
    }
}
