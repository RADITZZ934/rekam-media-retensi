<template>
  <div class="relative w-full" ref="dropdownRef">
    <!-- Trigger Button -->
    <button
      type="button"
      @click="toggleDropdown"
      class="w-full flex items-center justify-between px-3 py-2.5 bg-gray-50/50 hover:bg-gray-50 border border-gray-200/80 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/10 focus:border-blue-500 text-xs font-bold text-gray-600 transition-all cursor-pointer text-left shadow-sm"
    >
      <span class="truncate">
        {{ selectedLabel }}
      </span>
      <svg
        class="w-4 h-4 text-gray-400 transition-transform duration-200 flex-shrink-0"
        :class="{ 'rotate-180': isOpen }"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
      >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
      </svg>
    </button>

    <!-- Dropdown Menu Options -->
    <transition
      enter-active-class="transition duration-100 ease-out"
      enter-from-class="transform scale-95 opacity-0"
      enter-to-class="transform scale-100 opacity-100"
      leave-active-class="transition duration-75 ease-in"
      leave-from-class="transform scale-100 opacity-100"
      leave-to-class="transform scale-95 opacity-0"
    >
      <div
        v-if="isOpen"
        class="absolute z-50 w-full mt-1.5 bg-white border border-gray-150 rounded-xl shadow-xl max-h-60 overflow-y-auto py-1.5 focus:outline-none scrollbar-thin scrollbar-thumb-gray-200"
      >
        <div
          v-for="opt in options"
          :key="opt.value"
          @click="selectOption(opt)"
          class="px-3.5 py-2 text-xs font-bold text-gray-600 hover:text-blue-600 hover:bg-blue-50/50 cursor-pointer transition-colors flex items-center justify-between"
          :class="{ 'bg-blue-50/30 text-blue-600': opt.value === modelValue }"
        >
          <span class="truncate">{{ opt.label }}</span>
          <!-- Checked checkmark icon -->
          <svg
            v-if="opt.value === modelValue"
            class="w-3.5 h-3.5 text-blue-600 flex-shrink-0"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
          </svg>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: ''
  },
  options: {
    type: Array,
    required: true // Array of { value: '...', label: '...' }
  },
  placeholder: {
    type: String,
    default: 'Pilih opsi'
  }
})

const emit = defineEmits(['update:modelValue', 'change'])

const isOpen = ref(false)
const dropdownRef = ref(null)

const selectedLabel = computed(() => {
  const selected = props.options.find(opt => opt.value === props.modelValue)
  return selected ? selected.label : props.placeholder
})

const toggleDropdown = () => {
  isOpen.value = !isOpen.value
}

const selectOption = (opt) => {
  emit('update:modelValue', opt.value)
  emit('change', opt.value)
  isOpen.value = false
}

const handleClickOutside = (event) => {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
    isOpen.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>
