<template>
  <v-card
    class="dashboard-card ma-1"
    elevation="2"
    @click="$emit('click')"
    v-bind="$attrs"
  >
    <div class="d-flex flex-column align-center justify-center fill-height pa-4 text-center">
      <v-avatar
        :color="color"
        size="64"
        class="mb-3 elevation-4 icon-avatar"
      >
        <v-icon size="32" color="white">{{ icon }}</v-icon>
      </v-avatar>
      
      <h3 class="text-h6 font-weight-bold mb-1">{{ title }}</h3>
      
      <p v-if="description" class="text-caption text-medium-emphasis">
        {{ description }}
      </p>
    </div>
  </v-card>
</template>

<script setup>
defineProps({
  title: String,
  icon: String,
  color: String,
  description: String
});

defineEmits(['click']);
</script>

<style scoped>
.dashboard-card {
  background-color: #1E293B !important; /* Surface color */
  border: 1px solid #334155;
  border-radius: 16px;
  transition: all 0.3s ease;
  height: 180px;
  cursor: pointer;
}

.dashboard-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3) !important;
  border-color: var(--v-theme-primary);
}

.icon-avatar {
  background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 100%);
}
</style>
