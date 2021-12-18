
		<div id="img-popup-wrapper" class="text-gray-500 hidden bg-opacity-90 bg-black backdrop-blur-lg fixed top-0 left-0 w-full h-[100vh] z-[9002] flex justify-between items-center">
			<div id="img-popup-title" class="fixed top-8 text-2xl text-gray-100 font-bold text-center w-full">
			</div>
			<a id="previous-img-popup" class="hover:text-gray-100 transition cursor-pointer"><x-tabler-chevron-left class="h-20 w-20" /></a>
			<img id="img-popup-content" alt="main picture" class="hidden" />
			<x-loader id="img-popup-loader" alt="loading animation" />
			<a id="next-img-popup" class="hover:text-gray-100 transition cursor-pointer"><x-tabler-chevron-right class="h-20 w-20" /></a>
			<a href="#" id="close-img-popup" class="hover:text-gray-100 transition fixed top-4 right-4"><x-tabler-x class="h-16 w-16" /></a>
			<div class="fixed bottom-6 text-center w-full">
				<div class="inline-block border border-gray-500 rounded-lg px-6 py-2 text-lg">
					{{ __('app.img-popup-help') }}
				</div>
			</div>
		</div>