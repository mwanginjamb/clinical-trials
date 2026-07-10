/** @type {import('tailwindcss').Config} */
module.exports = {
    darkMode: "class",
    content: [
        "./frontend/views/**/*.php",
        "./frontend/web/js/**/*.js",
        "./frontend/widgets/**/*.php",
    ],
    theme: {
        extend: {
            "colors": {
                "on-tertiary-fixed-variant": "#004f4f",
                "surface-tint": "#2a6481",
                "surface-container": "#edeeef",
                "tertiary-fixed-dim": "#84d4d3",
                "on-secondary-fixed-variant": "#394951",
                "secondary-fixed": "#d4e5f0",
                "on-secondary-container": "#576770",
                "primary-container": "#135370",
                "on-primary": "#ffffff",
                "secondary-fixed-dim": "#b8c9d3",
                "inverse-surface": "#2e3132",
                "on-primary-fixed-variant": "#054c68",
                "surface": "#f8f9fa",
                "on-primary-fixed": "#001e2c",
                "on-background": "#191c1d",
                "on-surface-variant": "#40484d",
                "surface-bright": "#f8f9fa",
                "on-surface": "#191c1d",
                "on-error-container": "#93000a",
                "tertiary-fixed": "#a0f0f0",
                "tertiary-container": "#005757",
                "background": "#f8f9fa",
                "on-secondary": "#ffffff",
                "surface-container-highest": "#e1e3e4",
                "error-container": "#ffdad6",
                "on-secondary-fixed": "#0d1d25",
                "surface-variant": "#e1e3e4",
                "outline-variant": "#c0c8cd",
                "surface-container-low": "#f3f4f5",
                "primary": "#003b53",
                "tertiary": "#003e3e",
                "on-error": "#ffffff",
                "surface-dim": "#d9dadb",
                "on-tertiary": "#ffffff",
                "on-tertiary-fixed": "#002020",
                "primary-fixed-dim": "#97cdef",
                "secondary-container": "#d4e5f0",
                "inverse-primary": "#97cdef",
                "outline": "#70787d",
                "secondary": "#51616a",
                "inverse-on-surface": "#f0f1f2",
                "surface-container-lowest": "#ffffff",
                "on-tertiary-container": "#7ccccc",
                "error": "#ba1a1a",
                "on-primary-container": "#90c6e7",
                "primary-fixed": "#c4e7ff",
                "surface-container-high": "#e7e8e9",
                "brand": {
                    primary: '#005470', // Primary Clinical Curator Brand Color
                    accent: '#007bff',
                    light: '#f0f7ff',
                }
            },
            "borderRadius": {
                "DEFAULT": "0.125rem",
                "lg": "0.25rem",
                "xl": "0.5rem",
                "full": "0.75rem"
            },
            "fontFamily": {
                "headline": ["Manrope"],
                "body": ["Inter"],
                "label": ["Inter"]
            }
        },
    },
    plugins: [
        require("@tailwindcss/forms"),
        require("@tailwindcss/container-queries"),
    ],
};