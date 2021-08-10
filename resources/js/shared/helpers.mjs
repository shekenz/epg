// Select classes to array
export function arrayByClass(className) {
	return(Array.from(document.getElementsByClassName(className)));
};

/**
 * Executes callback(param) only after a cool down time. If coolDown() is called before, timer is reset.
 * Also executes init(param) every time coolDown() is called, with no delay.
 * @param init Function to be executed at every call. 
 * @param callback Function to be executed after cool down time.
 * @param timeout Cool down time.
 */
export const coolDown = (init, callback, timeout) => {
	let timeoutId = false;
	return param => {
		init(param);
		if(timeoutId) { clearTimeout(timeoutId); }
		timeoutId = setTimeout(() => {
			callback(param);
		}, timeout);
	}
};

// Generates a random string with alphanumeric values, excluding confusing characters like "O"
export const randomString = (stringLength = 8) => {
	let allowedGenerationChar = '';
	let randomLabel = '';
	for(let i = 49; i <= 57; i++) { allowedGenerationChar += String.fromCharCode(i); }
	for(let i = 65; i <= 78; i++) { allowedGenerationChar += String.fromCharCode(i); }
	for(let i = 80; i <= 90; i++) { allowedGenerationChar += String.fromCharCode(i); }

	for( let i = 0; i < stringLength; i++ ) {
		randomLabel += allowedGenerationChar[Math.round(Math.random() * (allowedGenerationChar.length - 1))];
	}
	return randomLabel;
}

// That I forgot what it does and why and where I needed it
// I think it generates an array of a given length with values returned by fn callback
export const arrayOf = (length, fn) => {
	return Array.apply(null, Array(length)).map(fn);
}

// Round a value with 2 decimal, fixing rounding imprecisions (Mostly for price)
export const roundPrice = (price) => {
	return Math.round(price * 100) / 100;
}

// Applies a bounding rectangle to an element with possible offset
export const matchDimension = (el, boundingRect, xOffset = 0, yOffset = 0) => {
	el.style.left = `${boundingRect.left - xOffset}px`;
	el.style.top = `${boundingRect.top - yOffset}px`;
	el.style.width = `${boundingRect.width + (xOffset*2) }px`;
	el.style.height = `${boundingRect.height + 2 + (yOffset*2) }px`;
}
