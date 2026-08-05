module.exports = {
  content: [
    './index.php',
    './routes/**/*.php',
    './app/**/*.php',
    './js/**/*.js'
  ],
  safelist: [
    'hidden',
    'overflow-hidden',
    'bg-green-500',
    'bg-red-500',
    'bg-blue-500',
    'text-white',
    'translate-y-8',
    'translate-y-0',
    'opacity-0',
    'opacity-100',
    'grid-cols-1',
    'grid-cols-2',
    'grid-cols-3',
    'grid-cols-4'
  ],
  theme: {
    extend: {
      colors: {
        brand: {
          green: {
            DEFAULT: '#006838',
            50: '#f0f9f0',
            100: '#dcf2dc',
            200: '#bce4bc',
            300: '#8fcf8f',
            400: '#5ab35a',
            500: '#3a9c3a',
            600: '#2b7f2b',
            700: '#256525',
            800: '#215121',
            900: '#1d441d',
            950: '#0b240b',
            light: '#2E8B57',
            dark: '#004d26'
          },
          gold: '#FFD700'
        }
      },
      fontFamily: {
        heading: ['Inter', 'system-ui', 'sans-serif'],
        body: ['Inter', 'system-ui', 'sans-serif']
      },
      animation: {
        'fade-in': 'fadeIn 0.5s ease-in-out',
        'fade-in-up': 'fadeInUp 0.8s ease-out both',
        'fade-in-down': 'fadeInDown 0.8s ease-out both',
        'slide-in': 'slideIn 0.3s ease-out'
      },
      keyframes: {
        fadeIn: {
          from: { opacity: '0' },
          to: { opacity: '1' }
        },
        fadeInUp: {
          from: { opacity: '0', transform: 'translateY(24px)' },
          to: { opacity: '1', transform: 'translateY(0)' }
        },
        fadeInDown: {
          from: { opacity: '0', transform: 'translateY(-24px)' },
          to: { opacity: '1', transform: 'translateY(0)' }
        },
        slideIn: {
          from: { transform: 'translateX(-100%)' },
          to: { transform: 'translateX(0)' }
        }
      }
    }
  },
  plugins: [
    require('@tailwindcss/typography')
  ]
};
