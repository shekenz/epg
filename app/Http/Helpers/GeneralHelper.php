<?php

function findStopPrice(int $totalWeight, float $defaultPrice, Illuminate\Support\Collection $priceList) {
	$priceList->each( function($priceStop) use ($totalWeight, &$defaultPrice) {
		if($totalWeight >= $priceStop->weight) {
			$defaultPrice = $priceStop->price;
		}
	});
	return $defaultPrice;
}