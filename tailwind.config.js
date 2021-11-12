const defaultTheme = require('tailwindcss/defaultTheme');
const colors = require('tailwindcss/colors');

module.exports = {
	darkMode: 'class',
	mode: 'jit',
		purge: {
		content: [
			'./storage/framework/views/*.php',
			'./resources/views/**/*.blade.php',
			'./resources/js/**/*.js',
			'./safelist.txt'
		],
		options: {
			safelist: ['black-square', 'animated'],
		},
	},

	theme: {
		extend: {
			fontFamily: {
				sans: ['"Monument Grotesk"', 'Nunito', ...defaultTheme.fontFamily.sans],
				dashboard: ['Nunito', ...defaultTheme.fontFamily.sans],
			},
			fontSize: {
				custom: ['1.1rem', '1.35rem'],
				'custom-sm': ['1em', '1.15em'],
			},
			colors: {
				dark: colors.gray,
				green: {
					pale: 'rgb(144, 255, 134)',
				},
				inherit: 'inherit',
			},
			cursor: {
				grab: 'grab',
			},
			outline: {
				light: [`1px solid ${colors.black}`, '2px'],
				dark: [`1px solid ${colors.gray[200]}`, '2px'],
				}
			},
		},

		variants: {
			extend: {
				opacity: ['disabled'],
				display: ['dark'],
			},
		},

		plugins: [require('@tailwindcss/forms')],
};