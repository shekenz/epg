<?php

namespace App\Http\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Cache;

class NewOrdersCache
{

	private static $isCached;

	/**
	 * Check if new orders are already cached
	 *
	 * @return boolean
	 */
	private static function isCached()
	{
		if(self::$isCached === null)
		{
			self::$isCached = Cache::has('newOrders');
		}
		return self::$isCached;
	}
	
	/**
	 * Cache new orders count if they are not cached
	 *
	 * @return void
	 */
	private static function checkCache()
	{
		if(!self::isCached())
		{
			self::cache();
			self::$isCached = true;
		}
	}

	/**
	 * Resets new orders count cache
	 *
	 * @return void
	 */
	private static function cache()
	{
		$newOrdersCount = Order::where('read', 0)->count();
		Cache::put('newOrders', $newOrdersCount);
	}
	
	/**
	 * Returns new orders count cache
	 *
	 * @return integer
	 */
	public static function read()
	{
		self::checkCache();
		return Cache::get('newOrders');
	}
	
	/**
	 * Clears new orders count cache
	 *
	 * @return void
	 */
	public static function clear()
	{
		Cache::forget('newOrders');
		self::$isCached = null;
	}

	/**
	 * Increment new orders count cache
	 *
	 * @return void
	 */
	public static function increment()
	{
		self::checkCache();
		Cache::increment('newOrders');
	}
	
	/**
	 * Decrement new orders count cache
	 *
	 * @return void
	 */
	public static function decrement()
	{
		self::checkCache();
		Cache::decrement('newOrders');
	}

}