<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\ShippingMethod;
use App\Http\Helpers\CartHelper;
use Illuminate\Database\Eloquent\Collection;

class CartController extends Controller
{
	protected $validation = [
		'lastname' => ['required', 'string', 'max:64'],
		'firstname' => ['required', 'string', 'max:64'],
		'company' => ['nullable', 'string', 'max:64'],
		'phone' => ['required', 'string'],
		'email' => ['required', 'email'],
		'shipping-address-1' => ['required', 'string', 'max:128'],
		'shipping-address-2' => ['nullable', 'string', 'max:128'],
		'shipping-city' => ['required', 'string', 'max:96'],
		'shipping-postcode' => ['required', 'string'],
		'shipping-country' => ['required', 'string'],
		'invoice-address-1' => ['required', 'string', 'max:128'],
		'invoice-address-2' => ['nullable', 'string', 'max:128'],
		'invoice-city' => ['required', 'string', 'max:96'],
		'invoice-postcode' => ['required', 'string'],
		'invoice-country' => ['required', 'string'],
		'sale-conditions' => ['accepted'],
	];

	protected $cartUpdated = false;

	/**
	 * Check if cart articles are still in available and in stock
	 *
	 * @return Collection Filtered out book collection from cart.
	 */
	//TODO Make cart a property of CartController
	public function checkCart() {

		$cart = session()->get('cart', false);

		if($cart) {
			// Find all books from cart without archived books (In 1 call)
			$books = Book::with([
				'media' => function($q) { $q->orderBy('pivot_order', 'asc'); }
			])->findMany(array_keys($cart));

			// Filter out if books has no price or no media
			$books = $books->filter(function($book) {
				return ($book->media->isNotEmpty() && isset($book->price));
			});

			// remap $books collection with books id as key
			$books = $books->keyBy('id');

			// Update cart according to filtered books array
			// In case book has been removed or is not available for sale while in client's cart
			$this->cartUpdated = (count($cart) - $books->count() > 0);
			$cart = array_intersect_key($cart, $books->toArray());
			
			// Check stock limits
			array_walk($cart, function(&$article, $id) use ($books, &$quantityUpdated) {
				if($article['quantity'] > $books[$id]->quantity && !$books[$id]->pre_order) {
					$article['quantity'] = $books[$id]->quantity;
					$this->cartUpdated = true;
				}
			});

			// Updates $books cartQuantity and filter out book with quantity of 0
			$books = $books->filter(function($book, $id) use ($cart) {
				$book->cartQuantity = $cart[$id]['quantity'];
				return ($book->cartQuantity > 0);
			});

			// Update session cart
			session(['cart' => $cart]);

			return $books;

		} else {
			return new Collection();
		}
	}
    
    /**
     * viewCart
     *
     * @param  mixed $request
     * @return void
     */
    public function viewCart(Request $request) {

		$books = $this->checkCart();
		$shippingMethods = ShippingMethod::with('priceStops')->orderBy('price', 'ASC')->get();

		if($this->cartUpdated) {
			session()->now('flash', __('flash.cart.stockUpdated'));
			session()->now('flash-type', 'warning');
			return view('index.cart.cart', compact('books', 'shippingMethods'));
		} else {
			return view('index.cart.cart', compact('books', 'shippingMethods'));
		}	
	}
	
	/**
	 * Add a new article to cart
	 *
	 * @param  Request $request
	 * @param  string $id
	 */
	public function add(Request $request, $id) {

		// Check if the request wants json
		if($request->wantsJson()) {

			$book = Book::with('media')->findOrFail($id);
			$bookReturnedDetails = [
				'book' => [
					'id' => $book->id,
					'price' => $book->price,
					'weight' => $book->weight,
					'modifier' => 1
				]
			];

			// If cart is empty, create new cart array
			if(!boolval(CartHelper::count())) {
				$cart = [];
			} else { // or retrieve existing cart
				$cart = session('cart');
			}
			
			// If book id found in cart, just update quantity
			if(array_key_exists($book->id, $cart)) {
				// Checking for stock
				// Adding book only if cartQuantity < stockQuantity or if book is in pre_order
				if($cart[$book->id]['quantity'] < $book->quantity || $book->pre_order) {
					$cart[$book->id]['quantity'] += 1;
				} else { // Redirect and inform the user book is not in stock anymore
					return response()->json($bookReturnedDetails)->setStatusCode(500, __('flash.cart.stockLimit'));
				}
			} else { // Else push new book id with an array with quantity of 1 and price
				// Checking for stock
				// Adding book only if stockQuantity > 0 or if book is in pre_order
				if($book->quantity > 0 || $book->pre_order) { // Check for stock
					$cart[$book->id] = [ 'price' => $book->price, 'quantity' => 1];
				} else {
					return response()->json($bookReturnedDetails)->setStatusCode(500, __('flash.cart.stockLimit'));
				}
			}

			// Save new cart in sesh and redirect
			session(['cart' => $cart]);
			return response()->json($bookReturnedDetails);
		
		} else {
			return abort(404);
		}
	}
	
	/**
	 * Removes 1 unit of a book from cart.
	 *
	 * @param Request $request
	 * @param  string $id
	 */
	public function remove(Request $request, $id) {

		// Check if the request wants json
		if($request->wantsJson()) {

			$book = Book::with('media')->findOrFail($id);
			$bookReturnedDetails = [
				'book' => [
					'id' => $book->id,
					'price' => $book->price,
					'weight' => $book->weight,
					'modifier' => -1
				]
			];

			// If cart is empty, or if book has no price or has no media, you shouldn't be here
			if(!boolval(CartHelper::count()) || !isset($book->price) || $book->media->isEmpty()) {
				return response()->json()->setStatusCode(404, 'Book not available');
			}

			// Retrieve cart
			$cart = session('cart');
			
			// If book id found in cart and is over 1, just update quantity
			if(array_key_exists($book->id, $cart) && $cart[$book->id]['quantity'] > 1) {		
				$cart[$book->id]['quantity'] -= 1;
			} elseif(array_key_exists($book->id, $cart) && $cart[$book->id]['quantity'] <= 1) { // If book id found in cart and is 1, just delete from cart
				unset($cart[$book->id]);
			} else { // If book is not in cart, you shouldn't be here neither
				return response()->json()->setStatusCode(404, 'Book not found in cart');
			}

			// Save new cart in sesh and redirect
			session(['cart' => $cart]);
			return response()->json($bookReturnedDetails);

		} else {
			return abort(404);
		}
	}
	
	/**
	 * Removes all units of a book in cart.
	 *
	 * @param  Request $request
	 * @param  string $id
	 */
	public function removeAll(Request $request, $id) {

		// Check if the request wants json
		if($request->wantsJson()) {

			$book = Book::with('media')->findOrFail($id);

			// If cart is empty, or if book has no price or has no media, you shouldn't be here
			if(!boolval(CartHelper::count()) || !isset($book->price) || $book->media->isEmpty()) {
				return response()->json()->setStatusCode(404, 'Book not available');
			}

			// Retrieve cart
			$cart = session('cart');
			
			// If book id found in cart, just delete it
			if(array_key_exists($book->id, $cart)) {
				$cartQuantity = $cart[$book->id]['quantity'];
				unset($cart[$book->id]);
			} else { // If book is not in cart, you shouldn't be here neither
				return response()->json()->setStatusCode(404, 'Book not found in cart');
			}

			// Save new cart in sesh and redirect
			session(['cart' => $cart]);
			return response()->json([
				'book' => [
					'id' => $book->id,
					'price' => $book->price,
					'weight' => $book->weight,
					'modifier' => -1 * $cartQuantity,
				]
			]);
		
		} else {
			return abort(404);
		}
	}

	public function clearCart() {
		session()->forget('cart');
		return redirect(route('index'));
	}

	public function success() {
		session()->forget('cart');
		return view('index.cart.confirmed');
	}
}
