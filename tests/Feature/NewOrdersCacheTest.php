<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Services\NewOrdersCache;

class NewOrdersCacheTest extends TestCase
{

	public function testCacheValueIsInteger()
	{
		$this->assertIsInt(NewOrdersCache::read());
	}
}
