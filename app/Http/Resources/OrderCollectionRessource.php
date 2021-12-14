<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderCollectionRessource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
					'id' => (string) $this->id,
					'order_id' => $this->order_id,
					'name' => $this->full_name,
					'email' => $this->contact_email,
					'pre_order' => (boolean) $this->pre_order,
					'status' => $this->status,
					'read' => (bool) $this->read,
					'created_at' => $this->created_at,
					'locale' => [
						'created_date' => $this->created_at_fdate,
						'created_time' => $this->created_at_ftime,
					],
				];
    }
}
