<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Services\NewOrdersCache;

class NewOrdersCacheTest extends TestCase
{
	
	/**
	 * Testing id newOrders cache value is an integer, and therfore exists.
	 *
	 * @return void
	 */
	public function testCacheValueIsInteger()
	{
		$this->assertIsInt(NewOrdersCache::read());
	}
	
}
