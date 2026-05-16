import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                backgroud: {
                    DEFAULT: '#111116',
                    panel: '#1e1e24',
                    button: '#262629',
                    input: '#313135',
                    text: '#212121',
                },
                border: {
                    DEFAULT: '#ff3b30',
                    green: '#ccff00',
                },
                sup:{
                    DEFAULT: '',
                    white:'#e1e1e1',
                    wheat:'#F5DEB3',
                }
            }
        },
    },

    plugins: [forms],
};
