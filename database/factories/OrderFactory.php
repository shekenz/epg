<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\ShippingMethod;
use Illuminate\Database\Eloquent\Factories\Factory;


class OrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
		$randomId = implode($this->faker->randomElements(str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'), 17));
		$randomId2 = implode($this->faker->randomElements(str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'), 17));
		$randomIdPayer = implode($this->faker->randomElements(str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'), 13));
		$surname = $this->faker->lastName();
		$givenName = $this->faker->firstName();
		$randomStatus = rand(0,20);
		switch($randomStatus) {
			case(0) : $status = 'FAILED'; break;
			case(1) : $status = 'CREATED'; break;
			default : 
				if($randomStatus > 15) {
					$status = 'SHIPPED';
				} else {
					$status = 'COMPLETED';
				}
				break;
		}

		$shippingMethodsArray = [];
		$shippingMethods = ShippingMethod::get();
		$shippingMethods->each(function($shippingMethod) use (&$shippingMethodsArray) {
			array_push($shippingMethodsArray, $shippingMethod->id);
		});

        return [
            'order_id' => $randomId,
            'transaction_id' => $randomId2,
            'payer_id' => $randomIdPayer,
			'surname' => $surname,
			'given_name' => $givenName,
			'full_name' => $givenName.' '.$surname,
			'phone' => null,
			'email_address' => $this->faker->email(),
			'address_line_1' => $this->faker->streetAddress(),
			'address_line_2' => null,
			'admin_area_2' => $this->faker->state(),
			'admin_area_1' => $this->faker->city(),
			'postal_code' => $this->faker->postcode(),
			'country_code' => 'FR',
			'coupon_id' => null,
			'shipping_method_id' => $this->faker->randomElement($shippingMethodsArray),
			'shipped_at' => null,
			'tracking_url' => null,
			'status' => $status,
			'pre_order' => $this->faker->numberBetween(0, 1),
			'read' => $this->faker->numberBetween(0, 1),
			'hidden' => 0,
        ];
    }
}
