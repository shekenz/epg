<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class OrderBookCollection extends ResourceCollection
{
	/**
	 * Collection underlying ressource (Different from OrderRessource)
	 */
	public $collects = OrderBookCollectionResource::class;

	/**
	 * Transform the resource collection into an array.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array
	 */
	public function toArray($request)
	{
			return $this->collect();
	}
}
