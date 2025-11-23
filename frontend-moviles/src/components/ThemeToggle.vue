<template>
  <ion-button @click="themeStore.toggleTheme" fill="clear" class="theme-toggle">
    <ion-icon slot="icon-only" :icon="themeStore.isDark ? sunny : moon"></ion-icon>
  </ion-button>
</template>

<script setup lang="ts">
import { useThemeStore } from '@/stores/theme';
import { moon, sunny } from 'ionicons/icons';
import { IonButton, IonIcon } from '@ionic/vue';

const themeStore = useThemeStore();
</script>

<style scoped>
.theme-toggle {
  --color: var(--ion-color-dark);
}
</style>
