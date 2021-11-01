<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Mail\Markdown;
use App\Models\Acronym;

class IndexController extends Controller
{

  public function about() {

		$acronyms = Acronym::all()->pluck('label')->toArray();

		$abouts = [
			Markdown::parse((Storage::disk('raw')->exists('about_0.txt')) ? nl2br(Storage::disk('raw')->get('about_0.txt')) : ''),
			Markdown::parse((Storage::disk('raw')->exists('about_1.txt')) ? nl2br(Storage::disk('raw')->get('about_1.txt')) : ''),
		];

		return view('index.about', compact('abouts', 'acronyms'));

	}

	public function terms() {

		$terms = Markdown::parse(nl2br(Storage::disk('raw')->get('terms.txt')));

		return view('index.terms', compact('terms'));

	}


}
