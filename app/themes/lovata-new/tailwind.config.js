module.exports = {
    content: [
        "./layouts/*.htm",
        "./pages/**/*.{html,htm,js}",
        "./assets/css/**/*.{html,htm,css}",
        "./partials/**/**/*.{html,htm,js}",
        "./partials/**/**/**/*.{html,htm,js}",

    ],
    darkMode: 'class',
    theme: {
        extend: {
            container: {
                center: true,
            },
            colors: {
                'black-white': "var(--black-white)",


                blue: {
                    '600-400': "var(--blue-600-400)",
                },
                white: {
                    'black': "var(--white-black)",
                    'natural-900': "var(--white-natural-900)",
                    'natural-800': "var(--white-natural-800)",
                    DEFAULT: '#ffffff',
                },
                gray: {
                    '50-natural-900': "var(--gray-50-natural-900)",
                    '200-natural-800': "var(--gray-200-natural-800)",
                    '100-natural-800': "var(--gray-100-natural-800)",
                    '200-700': "var(--gray-200-700)",
                    '300-white': "var(--gray-300-white)",
                    '300-600': "var(--gray-300-600)",
                    '300-800': "var(--gray-300-800)",
                    '400-300': "var(--gray-400-300)",
                    '400-500': "var(--gray-400-500)",
                    '400-600': "var(--gray-400-600)",
                    '500-300': "var(--gray-500-300)",
                    '500-white': "var(--gray-500-white)",
                    '600-white': "var(--gray-600-white)",
                    '600-300': "var(--gray-600-300)",
                    '600-500': "var(--gray-600-500)",

                },
                neutral: {
                    '800-white': "var(--gray-800-white)",
                },
            },
            fontFamily: {
                sans: [
                    'Circe, Arial, sans-serif',
                ]
            },
            padding: {
                menucalc: 'calc(( 50% / 3) - 166px + 18vw)',
            },
            height: {
                menucalcmob: 'calc(100% - 16px)',
            },
        }
    },
    plugins: [
        require('@tailwindcss/typography'),
    ],
}


