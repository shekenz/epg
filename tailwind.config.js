const defaultTheme = require('tailwindcss/defaultTheme');
const colors = require('tailwindcss/colors');

const blueGray = {
	primary: {
		dark: colors.coolGray[800],
		DEFAULT: 'rgb(80, 93, 123)',
		light: 'rgb(114, 134, 179)',
		'super-light': 'rgb(194, 212, 251)',
		'ultra-light': colors.coolGray[200],
	},
	secondary: {
		DEFAULT: 'rgb(145, 234, 255)',
	},
	background: {
		DEFAULT: 'rgb(221, 221, 227)',
		warm: 'rgb(227, 221, 221)',
	},
}

module.exports = {
	darkMode: 'class',
	mode: 'jit',
	purge:
	{
		content:
		[
			'./storage/framework/views/*.php',
			'./resources/views/**/*.blade.php',
			'./resources/js/**/*.js',
			'./safelist.txt'
		],
		options:
		{
			safelist: ['black-square', 'animated'],
		},
	},

		theme:
		{
			extend:
			{
				fontFamily:
				{
					sans: ['Monument Grotesk', 'Nunito', ...defaultTheme.fontFamily.sans],
					dashboard: ['Roboto', ...defaultTheme.fontFamily.sans],
				},
				flex:
				{
					'50-50': '0 1 50%',
				},
				fontSize:
				{
					custom: ['1.1rem', '1.35rem'],
					'custom-sm': ['1em', '1.15em'],
				},
				colors:
				{
					dark: colors.gray,
					green:
					{
						pale: 'rgb(144, 255, 134)',
					},
					inherit: 'inherit',
					...blueGray,
					lime: colors.lime,
				},
				cursor:
				{
					grab: 'grab',
				},
				outline:
				{
					light: [`1px solid ${colors.black}`, '2px'],
					dark: [`1px solid ${colors.gray[200]}`, '2px'],
				},
				boxShadow:
				{
					'dark': '0px 1px 5px -1px rgba(0,0,0,0.4)',
					'tight': '0px 1px 3px -1px rgba(0,0,0,0.7)',
					'tight-window': '0px 4px 10px -10px rgba(0,0,0,0.7)',
					'blue': '0px 0px 5px 1px rgba(37, 99, 235)',
					'green': '0px 0px 5px 1px rgba(5, 150, 105)',
				},
				backgroundImage:
				{
					'date-inverted': 'url("data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'16\' height=\'15\' viewBox=\'0 0 24 24\'%3E%3Cpath fill=\'white\' d=\'M20 3h-1V1h-2v2H7V1H5v2H4c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 18H4V8h16v13z\'/%3E%3C/svg%3E")',
					'time-inverted': 'url("data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'14\' height=\'14\' viewBox=\'0 0 24 24\'%3E%3Cpath fill=\'white\' d=\'M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8z\'/%3E%3Cpath d=\'M0 0h24v24H0z\' fill=\'none\'/%3E%3Cpath fill=\'white\' d=\'M12.5 7H11v6l5.25 3.15.75-1.23-4.5-2.67z\'/%3E%3C/svg%3E")',
				},
				width:
				{
					md: '768px',
					'md+': '800px',
				}
			},
		},

		variants:
		{
			extend:
			{
				opacity: ['disabled'],
				display: ['dark'],
				backgroundOpacity: ['dark'],
			},
		},

		plugins: [require('@tailwindcss/forms')],
};