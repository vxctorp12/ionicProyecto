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
          background: '#121212', // Ionic Dark Background
          surface: '#1e1e1e',    // Ionic Dark Card
          primary: '#2A67F1',    // Ionic Blue
          secondary: '#00A693',  // Ionic Secondary
          error: '#eb445a',      // Ionic Danger
          info: '#2A67F1',       // Ionic Primary as Info
          success: '#28ba62',    // Ionic Success
          warning: '#ffc409',    // Ionic Warning
        },
      },
    },
  },
})
