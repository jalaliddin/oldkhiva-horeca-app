import { createVuetify } from 'vuetify'
import { aliases, mdi } from 'vuetify/iconsets/mdi'
import '@mdi/font/css/materialdesignicons.css'
import 'vuetify/styles'

export default createVuetify({
  icons: { defaultSet: 'mdi', aliases, sets: { mdi } },
  theme: {
    defaultTheme: 'oldkhivaTheme',
    themes: {
      oldkhivaTheme: {
        dark: false,
        colors: {
          primary: '#1A2744',
          secondary: '#2C3E6B',
          accent: '#C8941A',
          success: '#2E7D32',
          warning: '#F57C00',
          error: '#C62828',
          info: '#0277BD',
          background: '#F5F6FA',
          surface: '#FFFFFF',
        }
      }
    }
  },
  defaults: {
    VBtn: { rounded: 'lg', elevation: 0 },
    VCard: { rounded: 'xl', elevation: 2 },
    VTextField: { variant: 'outlined', density: 'comfortable' },
    VSelect: { variant: 'outlined', density: 'comfortable' },
    VDataTable: { hover: true },
  }
})
