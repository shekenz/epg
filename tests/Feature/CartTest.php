<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
// use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Http\Services\Cart;

class CartTest extends TestCase
{
    /**
     * Test if cart exists and is an array.
     *
     * @return void
		 * @test
     */
    public function testHasCart()
    {
        $cart = new Cart;
        $this->assertIsArray($cart->getCart());
    }
}
