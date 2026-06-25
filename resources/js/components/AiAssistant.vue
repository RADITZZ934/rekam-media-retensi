<template>
  <div class="fixed bottom-6 right-6 z-[100]">
    <!-- Toggle Button -->
    <button 
      @click="isOpen = !isOpen"
      class="w-14 h-14 bg-indigo-600 hover:bg-indigo-700 text-white rounded-full shadow-2xl flex items-center justify-center transition-all transform hover:scale-110 active:scale-95"
    >
      <svg v-if="!isOpen" class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
      <svg v-else class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>

    <!-- Chat Window -->
    <transition name="chat-fade">
      <div v-if="isOpen" class="absolute bottom-20 right-0 w-80 sm:w-96 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden flex flex-col max-h-[500px]">
        <!-- Header -->
        <div class="bg-indigo-600 p-4 text-white flex items-center gap-3">
          <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
          </div>
          <div>
            <h3 class="font-bold text-sm">RSUK AI Assistant</h3>
            <p class="text-[10px] opacity-75">Online | Didukung Gemini AI</p>
          </div>
        </div>

        <!-- Chat Body (Messages & Input with Coming Soon Lock) -->
        <div class="relative flex-1 flex flex-col overflow-hidden">
          <!-- Messages -->
          <div ref="messageContainer" class="flex-1 p-4 overflow-y-auto space-y-4 min-h-[300px] bg-gray-50">
            <div v-for="(msg, index) in messages" :key="index" :class="['flex', msg.role === 'user' ? 'justify-end' : 'justify-start']">
              <div :class="['max-w-[85%] p-3 rounded-2xl text-sm shadow-sm', msg.role === 'user' ? 'bg-indigo-600 text-white rounded-tr-none' : 'bg-white text-gray-800 border border-gray-100 rounded-tl-none']">
                {{ msg.text }}
              </div>
            </div>
            <div v-if="isLoading" class="flex justify-start">
              <div class="bg-white p-3 rounded-2xl border border-gray-100 flex gap-1">
                <span class="w-1.5 h-1.5 bg-gray-300 rounded-full animate-bounce"></span>
                <span class="w-1.5 h-1.5 bg-gray-300 rounded-full animate-bounce [animation-delay:0.2s]"></span>
                <span class="w-1.5 h-1.5 bg-gray-300 rounded-full animate-bounce [animation-delay:0.4s]"></span>
              </div>
            </div>
          </div>

          <!-- Input -->
          <div class="p-4 border-t border-gray-100 bg-white flex-shrink-0">
            <div class="relative">
              <input 
                v-model="userInput"
                @keyup.enter="sendMessage"
                type="text" 
                placeholder="Tanyakan sesuatu..."
                class="w-full pl-4 pr-12 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all text-sm"
              />
              <button 
                @click="sendMessage"
                :disabled="!userInput.trim() || isLoading"
                class="absolute right-2 top-1.5 p-1.5 text-indigo-600 hover:bg-indigo-50 rounded-lg disabled:opacity-30"
              >
                <svg class="w-5 h-5 translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
              </button>
            </div>
            <p class="text-[9px] text-center text-gray-400 mt-2">Gunakan konteks dokumen jika terbuka di layar.</p>
          </div>

          <!-- Coming Soon Overlay (Styled with Space Cat Lottie Animation) -->
          <div class="absolute inset-0 bg-white flex flex-col items-center justify-center p-6 text-center z-[60] select-none">
            <!-- Lottie Animation -->
            <div class="w-64 h-64 mb-4 flex items-center justify-center">
              <DotLottieVue 
                v-if="isOpen"
                src="/SpaceCat.lottie" 
                style="width: 240px; height: 240px;" 
                loop 
                autoplay
              />
            </div>
            
            <h3 class="text-xl font-bold text-gray-800 mb-1.5">Coming soon!</h3>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script>
import { ref, nextTick, watch } from 'vue';
import { DotLottieVue } from '@lottiefiles/dotlottie-vue';

export default {
  name: 'AiAssistant',
  components: {
    DotLottieVue
  },
  props: {
    context: {
      type: String,
      default: ''
    }
  },
  setup(props) {
    const isOpen = ref(false);
    const userInput = ref('');
    const isLoading = ref(false);
    const messages = ref([
      { role: 'ai', text: 'Halo! Saya AI Assistant RSUK. Ada yang bisa saya bantu terkait rekam medis?' }
    ]);
    const messageContainer = ref(null);

    const scrollToBottom = async () => {
      await nextTick();
      if (messageContainer.value) {
        messageContainer.value.scrollTop = messageContainer.value.scrollHeight;
      }
    };

    const sendMessage = async () => {
      if (!userInput.value.trim() || isLoading.value) return;

      const userText = userInput.value;
      messages.value.push({ role: 'user', text: userText });
      userInput.value = '';
      isLoading.value = true;
      scrollToBottom();

      try {
        const response = await fetch('/api/ai/chat', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
          },
          body: JSON.stringify({ 
            message: userText,
            context: props.context 
          }),
        });

        const data = await response.json();
        if (data.success) {
          messages.value.push({ role: 'ai', text: data.response });
        } else {
          messages.value.push({ role: 'ai', text: 'Maaf, terjadi kesalahan: ' + data.message });
        }
      } catch (error) {
        messages.value.push({ role: 'ai', text: 'Maaf, jaringan bermasalah.' });
      } finally {
        isLoading.value = false;
        scrollToBottom();
      }
    };

    return {
      isOpen,
      userInput,
      isLoading,
      messages,
      messageContainer,
      sendMessage
    };
  }
}
</script>

<style scoped>
.chat-fade-enter-active, .chat-fade-leave-active {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.chat-fade-enter-from, .chat-fade-leave-to {
  opacity: 0;
  transform: translateY(20px) scale(0.95);
}
</style>
