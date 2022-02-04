<?php

namespace App\Http\Services;

use App\Models\Book;

class Cart
{

	private $cart;
	private $count;
	private $fresh = false;
	
	/**
	 * __construct
	 *
	 * @return void
	 */
	public function __construct()
	{
		if(session()->has('cart'))
		{
			$this->cart = session('cart');
		}
		else
		{
			$this->cart = [];
			$this->fresh = true;
		}
		$this->count();
	}
	
	/**
	 * save
	 *
	 * @return void
	 */
	private function save()
	{
		session(['cart' => $this->cart]);
		session()->save();
	}
	
	/**
	 * getCart
	 *
	 * @return array
	 */
	public function getCart() :array
	{
		return $this->cart;
	}
	
	/**
	 * isEmpty
	 *
	 * @return bool
	 */
	public function isEmpty() :bool
	{
		return ($this->count === 0);
	}
	
	/**
	 * add
	 *
	 * @return void
	 */
	public function add(Book $book, int $quantity = 1)
	{
			// If book already in cart, just update quantity
			if(array_key_exists($book->id, $this->cart))
			{
				$this->cart[$book->id]['quantity'] += $quantity;
			}
			else
			{
				$this->cart[$book->id] = [
					'price' => $book->price,
					'quantity' => $quantity,
				];
			}
			$this->save();
	}
	
	/**
	 * remove
	 *
	 * @param  mixed $book
	 * @param  mixed $quantity
	 * @return void
	 */
	public function remove(Book $book, int $quantity = 1)
	{
		// Remove book only if already in cart
		if(array_key_exists($book->id, $this->cart))
		{
			// if we try to remove less books we already have, just update quantity
			if($quantity < $this->cart[$book->id]['quantity'])
			{
				$this->cart[$book->id]['quantity'] -= $quantity;
			}
			else // if we want to remove more or the same amount of books we already have, we just delete the book
			{
				unset($this->cart[$book->id]);
			}
			$this->save();
		}
	}

	
	/**
	 * count
	 *
	 * @return void
	 */
	private function count()
	{
		$this->count = array_reduce($this->cart, function($accumulation, $article) {
			return $accumulation + $article['quantity'];
		}) ?? 0;
	}
	
	/**
	 * getCount
	 *
	 * @return int
	 */
	public function getCount() :int
	{
		return $this->count;
	}
	
	/**
	 * isFresh
	 *
	 * @return bool
	 */
	public function isFresh() :bool
	{
		return $this->fresh;
	}


}