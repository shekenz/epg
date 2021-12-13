<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\BookInfo;
use App\Models\Medium;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookFactory extends Factory
{
		/**
		 * The name of the factory's corresponding model.
		 *
		 * @var string
		 */
		protected $model = Book::class;
	
	/**
	 * mediaIDs
	 *
	 * @var array
	 */
	public $mediaIDs;	

	/**
	 * mediaCount
	 *
	 * @var int
	 */
	public $mediaCount;

	/**
	 * Define the model's default state.
	 *
	 * @return array
	 */
	public function definition()
	{		
		return [
			'book_info_id' => 0,
			'label' => ucfirst($this->faker->word()),
			'weight' => $this->faker->randomElement([50, 80, 120, 150, 175, 230, 255]),
			'stock' => rand(10, 300),
			'pre_order' => $this->faker->randomElement([0, 0, 1, 0, 0]),
			'price' => round(rand(1000,10000)/100, 2),
		];
	}
	
	/**
	 * configure
	 *
	 * @return void
	 */
	public function configure()
	{

		$this->bookInfoIDs = BookInfo::pluck('id');
		$this->mediaIDs = Medium::pluck('id');

		return $this->afterCreating(function (Book $book) {

			$book->book_info_id = $this->faker->randomElement($this->bookInfoIDs);

			if($book->pre_order) {

				$book->stock = 0;

			}

			$book->save();
			$book->media()->attach($this->faker->randomElements($this->mediaIDs, rand(2, 10)));

		});
	}
}
