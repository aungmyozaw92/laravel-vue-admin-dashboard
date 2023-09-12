<?php

namespace App\Http\Resources\Admin\Appointment;

use App\Enums\AppointmentStatus;
use App\Http\Resources\Admin\Client\ClientResource;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'client' => ClientResource::make($this->whenLoaded('client')),
            'title' => $this->title,
            'description' => $this->description,
            // date('Y-m-d', strtotime($this->created_at)),
            'end_time' => $this->end_time->format(config('app.datetime_format')),
            'start_time' => $this->start_time->format(config('app.datetime_format')),
            'status' => [
                'name' => AppointmentStatus::from($this->status)->name,
                'color' => AppointmentStatus::from($this->status)->color(),
            ],
            
        ];
    }

     /**
     * Get additional data that should be returned with the resource array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function with($request)
    {
        return [
            'status' => true,
        ];
    }
}
