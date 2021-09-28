<?php

function findStopPrice(int $totalWeight, float $defaultPrice, Illuminate\Support\Collection $priceList) {
	$priceList->each( function($priceStop) use ($totalWeight, &$defaultPrice) {
		if($totalWeight >= $priceStop->weight) {
			$defaultPrice = $priceStop->price;
		}
	});
	return $defaultPrice;
}

function mb_ucfirst($string) {
	return mb_strtoupper(mb_substr($string, 0, 1)).mb_substr($string, 1);
}

function ___($string) {
	return mb_ucfirst(__($string));
}