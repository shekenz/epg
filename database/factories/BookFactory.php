<?php

namespace Database\Factories;

use App\Models\Book;
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
			'title' => $this->faker->sentence(4),
			'author' => $this->faker->firstName().' '.$this->faker->lastName(),
			'user_id' => 1,
			'width' => rand(150, 300),
			'height' => rand(150, 300),
			'cover' => $this->faker->randomElement(['Souple', 'Flex', 'Rigide', 'Magazine']),
			'pages' => rand(20, 400),
			'weight' => $this->faker->randomElement([50, 80, 120, 150, 175, 230, 255]),
			'copies' => rand(10,300),
			'quantity' => rand(10, 300),
			'pre_order' => $this->faker->numberBetween(0, 1),
			'year' => $this->faker->year(),
			'price' => round(rand(1000,10000)/100, 2),
			'description' => $this->faker->paragraph(15, true),
		];
    }
	
	/**
	 * configure
	 *
	 * @return void
	 */
	public function configure()
    {
		$this->mediaIDs = Medium::pluck('id');
		$this->mediaCount = Medium::count();

        return $this->afterCreating(function (Book $book) {
            $book->media()->attach($this->faker->randomElements($this->mediaIDs, rand(1, $this->mediaCount)));
        });
    }
}
