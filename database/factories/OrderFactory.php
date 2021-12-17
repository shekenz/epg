<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Book;
use App\Models\ShippingMethod;
use App\Models\Coupon;
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
	 * booksCount
	 *
	 * @var int
	 */
	public $booksCount;

	/**
	 * booksIDS
	 *
	 * @var array
	 */
	public $booksIDs;

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
		switch($randomStatus)
		{
			case(0) : $status = 'FAILED'; break;
			case(1) : $status = 'CREATED'; break;
			default : 
				$status = 'COMPLETED';
				break;
		}

		$shippingMethodsArray = [];
		$shippingMethods = ShippingMethod::get();
		$shippingMethods->each(function($shippingMethod) use (&$shippingMethodsArray) {
			array_push($shippingMethodsArray, $shippingMethod->id);
		});
		
		$couponsArray = [];
		$coupons = Coupon::get();
		$coupons->each(function($shippingMethod) use (&$couponsArray) {
			array_push($couponsArray, $shippingMethod->id);
		});
		array_unshift($couponsArray, null);

		return [
			'order_id' => $randomId,
			'transaction_id' => $randomId2,
			'payer_id' => $randomIdPayer,
			'surname' => $surname,
			'given_name' => $givenName,
			'full_name' => $givenName.' '.$surname,
			'phone_number' => $this->faker->e164PhoneNumber(),
			'contact_email' => $this->faker->email(),
			'email_address' => $this->faker->email(),
			'address_line_1' => $this->faker->streetAddress(),
			'address_line_2' => null,
			'admin_area_2' => $this->faker->city(),
			'admin_area_1' => (config('app.lang') == 'fr_FR') ? $this->faker->departmentName() : $this->faker->state(),
			'postal_code' => $this->faker->postcode(),
			'country_code' => strtoupper(config('app.locale')),
			'coupon_id' => $this->faker->randomElement($couponsArray),
			'shipping_method_id' => $this->faker->randomElement($shippingMethodsArray),
			'total_weight' => 0,
			'shipped_at' => null,
			'tracking_url' => null,
			'status' => $status,
			'pre_order' => 0,
			'read' => $this->faker->numberBetween(0, 1),
			'created_at' => $this->faker->dateTimeBetween($startDate = '-1 years', $endDate = 'now', $timezone = null)
		];
	}

	public function configure()
	{
		$this->booksCount = Book::count();

		return $this->afterCreating(function(Order $order) {
			$preOrder = 0;
			$totalWeight = 0;

			$this->booksIDs = Book::all()->random(rand(1, 5));

			$this->booksIDs->each(function($book) use (&$preOrder, &$totalWeight) {
				if($book->pre_order) {
					$preOrder = 1;
				}
				$totalWeight += $book->weight;
			});

			$this->booksIDs = $this->booksIDs->keyBy('id')->transform(function() {
				return ['quantity' => rand(1,3)];
			})->toArray();

			$order->total_weight = $totalWeight;
			$order->pre_order = $preOrder;
			$order->save();

			$order->books()->attach($this->booksIDs);

		});
	}
}
