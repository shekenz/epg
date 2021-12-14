<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
					'payer' => [
						'id' => $this->payer_id,
						'first_name' => $this->surname,
						'last_name' => $this->given_name,
						'full_name' => $this->full_name,
						'phone_number' => $this->phone_number,
						'contact_email' => $this->contact_email,
						'paypal_address' => $this->email_address,
					],
					'shipping' => [
						'address' => [
							'line_1' => $this->address_line_1,
							'line_2' => $this->address_line_2,
							'admin_area_2' => $this->admin_area_2,
							'admin_area_1' => $this->admin_area_1,
							'postcode' => $this->postal_code,
							'country_code' => $this->country_code,
							'country' => config('countries.'.$this->country_code),
						],
						'method' => [],
						'total_weight' => $this->total_weight,
						'shipped_at' => $this->shipped_at,
						'tracking_url' => $this->tracking_url,
					],
					'order' => [
						'id' => $this->order_id,
						'transaction_id' => $this->transaction_id,
						'status' => $this->status,
						'pre_order' => (bool) $this->pre_order,
						'books' => [],
					],
					'meta' => [
						'read' => (bool) $this->read,
						'hidden' => (bool) $this->hidden,
						'created_at' => $this->created_at,
						'locale' => [
							'created_date' => $this->created_at_fdate,
							'created_time' => $this->created_at_ftime,
						],
					],
				];
    }
}
