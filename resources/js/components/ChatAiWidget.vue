<template>
  <div class="fixed bottom-6 right-6 z-[999]">

    <!-- Floating Toggle Button -->
    <button 
      @click="toggleChat"
      class="relative w-14 h-14 rounded-full shadow-2xl flex items-center justify-center transition-all duration-300 transform hover:scale-110 active:scale-95 group"
      :class="isOpen 
        ? 'bg-gray-700 hover:bg-gray-800 rotate-0' 
        : 'bg-gradient-to-br from-gray-500 to-gray-600 hover:shadow-gray-300/50'"
    >
      <!-- Chat Icon -->
      <transition name="icon-swap" mode="out-in">
        <svg v-if="!isOpen" key="chat" class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
        </svg>
        <svg v-else key="close" class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </transition>

      <!-- Pulse ring when closed -->
      <span v-if="!isOpen" class="absolute inset-0 rounded-full bg-gray-400 animate-ping opacity-20"></span>
    </button>

    <!-- Chat Window -->
    <transition name="chat-window">
      <div 
        v-if="isOpen" 
        class="absolute bottom-20 right-0 w-[400px] max-h-[600px] bg-white rounded-2xl shadow-2xl border border-gray-200/60 overflow-hidden flex flex-col"
        style="box-shadow: 0 25px 60px -12px rgba(0, 0, 0, 0.25);"
      >
        <!-- Header -->
        <div class="bg-gradient-to-r from-gray-600 to-gray-700 px-5 py-4 flex items-center justify-between flex-shrink-0">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
              <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
              </svg>
            </div>
            <div>
              <h3 class="font-bold text-white text-sm">AI Assistant RSUK</h3>
              <div class="flex items-center gap-1.5">
                <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                <p class="text-[11px] text-gray-300">Online • {{ selectedCharacter.name }}</p>
              </div>
            </div>
          </div>
          <div class="flex items-center gap-1">
            <!-- Character Selector -->
            <div class="relative" ref="charDropdownRef">
              <button 
                @click.stop="showCharDropdown = !showCharDropdown"
                class="w-8 h-8 flex items-center justify-center text-white/70 hover:text-white hover:bg-white/10 rounded-lg transition-all"
                title="Pilih Karakter"
              >
                <span class="text-lg">{{ selectedCharacter.avatar }}</span>
              </button>
              <transition name="dropdown-pop">
                <div v-if="showCharDropdown" class="absolute right-0 bottom-full mb-2 w-56 bg-white rounded-xl shadow-2xl border border-gray-100 overflow-hidden z-50">
                  <div class="p-2.5 border-b border-gray-100 bg-gray-50">
                    <p class="text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Pilih Karakter AI</p>
                  </div>
                  <div class="p-1.5 max-h-48 overflow-y-auto">
                    <button 
                      v-for="char in characters" 
                      :key="char.id"
                      @click="selectCharacter(char)"
                      :class="[
                        'w-full flex items-center gap-2.5 px-3 py-2 rounded-lg text-left transition-all text-sm',
                        selectedCharacter.id === char.id 
                          ? 'bg-indigo-50 text-indigo-700' 
                          : 'hover:bg-gray-50 text-gray-700'
                      ]"
                    >
                      <span class="text-lg">{{ char.avatar }}</span>
                      <div class="min-w-0">
                        <p class="font-medium text-xs">{{ char.name }}</p>
                        <p class="text-[10px] text-gray-400 truncate">{{ char.description }}</p>
                      </div>
                    </button>
                  </div>
                </div>
              </transition>
            </div>
            <!-- New Chat -->
            <button 
              @click="startNewChat"
              class="w-8 h-8 flex items-center justify-center text-white/70 hover:text-white hover:bg-white/10 rounded-lg transition-all"
              title="Chat Baru"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
              </svg>
            </button>
          </div>
        </div>
        <!-- Chat Body (Messages & Input with Coming Soon Lock) -->
        <div class="relative flex-1 flex flex-col overflow-hidden">
          <!-- Messages -->
          <div ref="messageContainer" class="flex-1 overflow-y-auto px-4 py-4 space-y-4 bg-gray-50/80 min-h-[320px] max-h-[400px]">
            
            <!-- Welcome State -->
            <div v-if="messages.length === 0" class="flex flex-col items-center justify-center h-full py-8">
              <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center mb-4 shadow-xl shadow-indigo-200/50 animate-float">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
              </div>
              <h4 class="text-base font-bold text-gray-800 mb-1">Halo! 👋</h4>
              <p class="text-xs text-gray-500 text-center mb-5 px-4">Ada yang bisa saya bantu seputar rekam medis?</p>
              <div class="grid grid-cols-2 gap-2 w-full px-2">
                <button 
                  v-for="sug in suggestions" 
                  :key="sug"
                  @click="sendSuggestion(sug)"
                  class="p-2.5 bg-white rounded-xl border border-gray-200 hover:border-indigo-300 hover:shadow-md transition-all text-left group"
                >
                  <p class="text-[11px] text-gray-600 group-hover:text-indigo-700 transition-colors leading-snug">{{ sug }}</p>
                </button>
              </div>
            </div>

            <!-- Message Bubbles -->
            <template v-else>
              <div 
                v-for="(msg, index) in messages" 
                :key="index"
                :class="['flex', msg.role === 'user' ? 'justify-end' : 'justify-start']"
                class="animate-msg"
              >
                <!-- AI -->
                <div v-if="msg.role === 'ai'" class="flex items-start gap-2 max-w-[85%]">
                  <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                  </div>
                  <div class="bg-white rounded-2xl rounded-tl-md px-4 py-3 shadow-sm border border-gray-100">
                    <div class="text-[13px] text-gray-800 leading-relaxed whitespace-pre-wrap" v-html="formatMessage(msg.text)"></div>
                    <p class="text-[9px] text-gray-400 mt-1.5">{{ msg.time }}</p>
                  </div>
                </div>
                <!-- User -->
                <div v-else class="max-w-[80%]">
                  <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-2xl rounded-tr-md px-4 py-3 shadow-md">
                    <p class="text-[13px] leading-relaxed">{{ msg.text }}</p>
                    <p class="text-[9px] text-indigo-200 mt-1.5">{{ msg.time }}</p>
                  </div>
                </div>
              </div>

              <!-- Typing -->
              <div v-if="isLoading" class="flex items-start gap-2 animate-msg">
                <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center flex-shrink-0">
                  <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                  </svg>
                </div>
                <div class="bg-white rounded-2xl rounded-tl-md px-4 py-3.5 shadow-sm border border-gray-100">
                  <div class="flex items-center gap-1">
                    <span class="w-1.5 h-1.5 bg-indigo-400 rounded-full animate-bounce"></span>
                    <span class="w-1.5 h-1.5 bg-indigo-400 rounded-full animate-bounce" style="animation-delay: 0.15s"></span>
                    <span class="w-1.5 h-1.5 bg-indigo-400 rounded-full animate-bounce" style="animation-delay: 0.3s"></span>
                  </div>
                </div>
              </div>
            </template>
          </div>

          <!-- Input -->
          <div class="px-4 py-3 border-t border-gray-100 bg-white flex-shrink-0">
            <div class="relative">
              <textarea 
                ref="inputRef"
                v-model="userInput"
                @keydown="handleKeyDown"
                rows="1"
                placeholder="Ketik pesan..."
                class="w-full pl-4 pr-12 py-2.5 bg-gray-100 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white outline-none transition-all text-sm resize-none overflow-hidden leading-relaxed"
                :style="{ height: textareaHeight }"
              ></textarea>
              <button 
                @click="sendMessage"
                :disabled="!userInput.trim() || isLoading || isTyping"
                class="absolute right-1.5 bottom-1.5 w-9 h-9 flex items-center justify-center bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-lg hover:shadow-lg transition-all disabled:opacity-30 disabled:cursor-not-allowed"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                </svg>
              </button>
            </div>
            <p class="text-[9px] text-center text-gray-400 mt-1.5">
              Powered by YuuLabs AI • <kbd class="px-1 py-0.5 bg-gray-100 rounded text-gray-500 font-mono text-[8px]">Enter</kbd> kirim
            </p>
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

<script setup>
import { ref, nextTick, onMounted, onBeforeUnmount, watch } from 'vue'
import { DotLottieVue } from '@lottiefiles/dotlottie-vue'

const isOpen = ref(false)
const messages = ref([])
const userInput = ref('')
const isLoading = ref(false)
const conversationId = ref('')
const messageContainer = ref(null)
const inputRef = ref(null)
const textareaHeight = ref('40px')
const showCharDropdown = ref(false)
const charDropdownRef = ref(null)
const isTyping = ref(false) // New state

const characters = ref([
  { id: 'unlimited', name: 'AI Assistant', description: 'Asisten pintar serba bisa', avatar: '🤖' },
])
const selectedCharacter = ref(characters.value[0])

const suggestions = [
  '📋 Apa itu retensi rekam medis?',
  '🏥 Prosedur alih media dokumen',
  '📄 Syarat pemusnahan berkas',
  '⏰ Jangka waktu penyimpanan',
]

const toggleChat = () => {
  isOpen.value = !isOpen.value
  if (isOpen.value) {
    nextTick(() => inputRef.value?.focus())
  }
}

const formatTime = () => {
  return new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })
}

const formatMessage = (text) => {
  if (!text) return ''
  const str = typeof text === 'string' ? text : String(text)
  return str
    .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
    .replace(/\*(.*?)\*/g, '<em>$1</em>')
    .replace(/`(.*?)`/g, '<code class="px-1 py-0.5 bg-gray-100 rounded text-indigo-600 text-[11px] font-mono">$1</code>')
    .replace(/\n/g, '<br>')
}

const scrollToBottom = async () => {
  await nextTick()
  if (messageContainer.value) {
    messageContainer.value.scrollTop = messageContainer.value.scrollHeight
  }
}

const autoResizeTextarea = () => {
  if (!inputRef.value) return
  inputRef.value.style.height = '40px'
  const newHeight = Math.min(inputRef.value.scrollHeight, 120)
  textareaHeight.value = newHeight + 'px'
}

const handleKeyDown = (e) => {
  if (e.key === 'Enter' && !e.shiftKey) {
    e.preventDefault()
    sendMessage()
  }
}

const sendSuggestion = (text) => {
  userInput.value = text
  sendMessage()
}

const selectCharacter = (char) => {
  selectedCharacter.value = char
  showCharDropdown.value = false
}

const startNewChat = () => {
  messages.value = []
  conversationId.value = ''
  userInput.value = ''
  textareaHeight.value = '40px'
}

const sendMessage = async () => {
  if (!userInput.value.trim() || isLoading.value) return

  const userText = userInput.value.trim()
  messages.value.push({ role: 'user', text: userText, time: formatTime() })
  userInput.value = ''
  textareaHeight.value = '40px'
  isLoading.value = true
  scrollToBottom()

  try {
    const response = await fetch('/api/chatai/send', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
      },
      body: JSON.stringify({
        message: userText,
      }),
    })

    if (!response.ok) throw new Error('Gagal terhubung')

    // REAL STREAMING (Read Reader)
    const reader = response.body.getReader()
    const decoder = new TextDecoder()
    let buffer = '' // Buffer for incomplete lines
    
    const messageIndex = messages.value.length
    messages.value.push({ role: 'ai', text: '', time: formatTime() })
    isTyping.value = true
    
    let fullText = ''

    while (true) {
      const { done, value } = await reader.read()
      if (done) break
      
      buffer += decoder.decode(value, { stream: true })
      const lines = buffer.split('\n')
      buffer = lines.pop() // Keep the last incomplete line
      
      for (const line of lines) {
        const cleanLine = line.trim()
        if (!cleanLine || !cleanLine.startsWith('data: ')) continue
        
        try {
          const jsonStr = cleanLine.replace('data: ', '').trim()
          if (!jsonStr) continue
          
          const data = JSON.parse(jsonStr)
          let newText = ''
          
          // Robust checking for Gemini response structure
          if (data.candidates?.[0]?.content?.parts?.[0]?.text) {
            newText = data.candidates[0].content.parts[0].text
          } else if (data.result && typeof data.result === 'string') {
            newText = data.result
          }
          
          if (newText) {
            fullText += newText
            messages.value[messageIndex].text = fullText
            scrollToBottom()
          }
        } catch (e) {
          // Ignore partial JSON errors
        }
      }
    }
    isTyping.value = false

  } catch (error) {
    console.error(error)
    messages.value.push({ role: 'ai', text: '⚠️ Gagal mendapatkan respons. Coba lagi.', time: formatTime() })
  } finally {
    isLoading.value = false
    scrollToBottom()
  }
}

const handleClickOutside = (e) => {
  if (charDropdownRef.value && !charDropdownRef.value.contains(e.target)) {
    showCharDropdown.value = false
  }
}

watch(userInput, () => nextTick(autoResizeTextarea))

onMounted(() => document.addEventListener('click', handleClickOutside))
onBeforeUnmount(() => document.removeEventListener('click', handleClickOutside))
</script>

<style scoped>
/* Float animation */
@keyframes float {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-8px); }
}
.animate-float {
  animation: float 3s ease-in-out infinite;
}

/* Message entrance */
@keyframes msgIn {
  from { opacity: 0; transform: translateY(8px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-msg {
  animation: msgIn 0.25s ease-out;
}

/* Chat window animation */
.chat-window-enter-active {
  transition: all 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.chat-window-leave-active {
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}
.chat-window-enter-from {
  opacity: 0;
  transform: translateY(20px) scale(0.9);
}
.chat-window-leave-to {
  opacity: 0;
  transform: translateY(10px) scale(0.95);
}

/* Icon swap */
.icon-swap-enter-active,
.icon-swap-leave-active {
  transition: all 0.2s ease;
}
.icon-swap-enter-from {
  opacity: 0;
  transform: rotate(90deg) scale(0.5);
}
.icon-swap-leave-to {
  opacity: 0;
  transform: rotate(-90deg) scale(0.5);
}

/* Dropdown pop */
.dropdown-pop-enter-active {
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}
.dropdown-pop-leave-active {
  transition: all 0.15s ease;
}
.dropdown-pop-enter-from,
.dropdown-pop-leave-to {
  opacity: 0;
  transform: translateY(4px) scale(0.95);
}

/* Scrollbar */
::-webkit-scrollbar { width: 4px; }
::-webkit-scrollbar-track { background: transparent; }
::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 8px; }
::-webkit-scrollbar-thumb:hover { background: #9ca3af; }

textarea {
  scrollbar-width: thin;
  scrollbar-color: #d1d5db transparent;
}
</style>
