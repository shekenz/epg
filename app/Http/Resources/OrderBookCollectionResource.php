<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderBookCollectionResource extends JsonResource
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
					'title' => $this->bookInfo->title,
					'variation' => $this->label,
					'author' => $this->bookInfo->author,
					'pre_order' => (bool) $this->pre_order,
					'unit_price' => $this->price,
					'quantity' => $this->pivot->quantity,
					'total_price' => round($this->price * $this->pivot->quantity, 2),
				];
    }
}
