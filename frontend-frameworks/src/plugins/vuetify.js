// Styles
import '@mdi/font/css/materialdesignicons.css'
import 'vuetify/styles'

// Vuetify
import { createVuetify } from 'vuetify'

export default createVuetify({
  theme: {
    defaultTheme: 'myCustomDarkTheme',
    themes: {
      myCustomDarkTheme: {
        dark: true,
        colors: {
          background: '#0F172A', // Dark Navy
          surface: '#1E293B',    // Lighter Navy for cards
          primary: '#2563EB',    // Bright Blue
          secondary: '#64748B',
          error: '#EF4444',
          info: '#3B82F6',
          success: '#10B981',
          warning: '#F59E0B',
        },
      },
    },
  },
})
