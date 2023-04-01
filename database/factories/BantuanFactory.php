<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BantuanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'image' => 'assets/bantuan/kV7OmyjXqYMVZYa5SL4auKaLASI3O04G97iLenxj.png',
            'title' => $this->faker->sentence(mt_rand(1,2)),
            'price' => mt_rand(100000,1500000),
            'desc' => $this->faker->sentence(mt_rand(3,5)),
            'location' => '100,200|'.$this->faker->country()." ".$this->faker->city(),
            'user_id' => mt_rand(1,3),
            'pay_type' => 'cash',
            'category_id' => mt_rand(1,5),
            'status' => 'active',
        ];
    }
}
