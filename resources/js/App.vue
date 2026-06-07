<template>
  <!-- Public Login Layout (No Sidebar/Topbar/Chat widget) -->
  <div v-if="route.name === 'login'" class="min-h-screen w-screen bg-[#0b1329]">
    <router-view v-slot="{ Component }">
      <transition name="fade" mode="out-in">
        <component :is="Component" />
      </transition>
    </router-view>
  </div>

  <!-- Authenticated App Dashboard Layout -->
  <div v-else class="flex h-screen bg-gray-100 overflow-hidden">
    <Sidebar />
    <div class="flex-1 flex flex-col overflow-hidden">
      <Topbar />
      <main ref="mainContainer" class="flex-1 overflow-y-auto bg-[#f9fbff]">
        <router-view v-slot="{ Component }">
          <transition name="fade" mode="out-in">
            <component :is="Component" />
          </transition>
        </router-view>
      </main>
    </div>
    <!-- Floating Chat AI Widget -->
    <ChatAiWidget />
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import Sidebar from './components/Sidebar.vue'
import Topbar from './components/Topbar.vue'
import ChatAiWidget from './components/ChatAiWidget.vue'

const route = useRoute()
const mainContainer = ref(null)

// Smooth scroll reset on route path change
watch(() => route.path, () => {
  if (mainContainer.value) {
    mainContainer.value.scrollTop = 0
  }
})
</script>

<style>
/* Premium decelleration curve transition */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.24s cubic-bezier(0.16, 1, 0.3, 1),
              transform 0.24s cubic-bezier(0.16, 1, 0.3, 1);
}

.fade-enter-from {
  opacity: 0;
  transform: translateY(8px);
}

.fade-leave-to {
  opacity: 0;
  transform: translateY(-8px);
}
</style>
