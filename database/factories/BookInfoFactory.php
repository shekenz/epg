<?php

namespace Database\Factories;

use App\Models\BookInfo;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookInfoFactory extends Factory
{
	/**
	 * The name of the factory's corresponding model.
	 *
	 * @var string
	 */
	protected $model = BookInfo::class;

	/**
	 * Define the model's default state.
	 *
	 * @return array
	 */
	public function definition()
	{
		return [
			'title' => rtrim($this->faker->sentence(4), '.'),
			'author' => $this->faker->firstName().' '.$this->faker->lastName(),
			'user_id' => 1,
			'width' => rand(150, 300),
			'height' => rand(150, 300),
			'cover' => $this->faker->randomElement(['Souple', 'Flex', 'Rigide', 'Magazine']),
			'pages' => rand(20, 400),
			'copies' => rand(10,300),
			'year' => $this->faker->year(),
			'description' => $this->faker->paragraph(15, true),
		];
	}
}
