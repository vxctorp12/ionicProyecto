import type { CapacitorConfig } from '@capacitor/cli';

const config: CapacitorConfig = {
  appId: 'io.ionic.starter',
  appName: 'frontend-moviles',
  webDir: 'dist',
  server: {
    androidScheme: 'http',
    cleartext: true,
    allowNavigation: ['192.168.0.102']
  },
  // --- ESTA ES LA SECCIÓN QUE FALTABA ---
  plugins: {
    StatusBar: {
      // false = La app empieza DEBAJO de la barra (soluciona el solapamiento)
      overlay: false,

      // Color de fondo de la barra (Tu azul primario #2A67F1)
      backgroundColor: '#2A67F1',

      // 'DARK' en Capacitor significa "Iconos blancos para fondo oscuro"
      style: 'DARK'
    },
    Keyboard: {
      resize: 'none',
      resizeOnFullScreen: true
    }
  }
};

export default config;