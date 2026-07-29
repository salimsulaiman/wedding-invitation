<script setup lang="ts">
import { Signal, Wifi, BatteryFull, RefreshCw } from 'lucide-vue-next'
import { ref, computed } from 'vue'

const props = defineProps<{
    invitationId: number | string
    version?: string | number
}>()

const iframeRef = ref<HTMLIFrameElement | null>(null)
const isLoading = ref(true)

const previewUrl = computed(() => {
    const base = route('builder.preview', props.invitationId)
    const v = props.version ?? Date.now()
    return `${base}?v=${encodeURIComponent(v)}`
})

function onIframeLoad() {
    isLoading.value = false
}

function reload() {
    isLoading.value = true
    if (iframeRef.value) {
        iframeRef.value.src = `${route('builder.preview', props.invitationId)}?v=${Date.now()}`
    }
}

defineExpose({ reload })
</script>

<template>
    <div class="mx-auto w-full max-w-95">
        <div class="relative rounded-[3rem] border-14 border-slate-900 bg-slate-900 shadow-2xl">

            <div class="absolute -left-4.25 top-24 h-8 w-0.75 rounded-l-sm bg-slate-800"></div>
            <div class="absolute -left-4.25 top-36 h-12 w-0.75 rounded-l-sm bg-slate-800"></div>
            <div class="absolute -left-4.25 top-52 h-12 w-0.75 rounded-l-sm bg-slate-800"></div>
            <div class="absolute -right-4.25 top-32 h-16 w-0.75 rounded-r-sm bg-slate-800"></div>

            <div class="absolute left-1/2 top-3 z-20 h-7 w-32 -translate-x-1/2 rounded-full bg-slate-900"></div>

            <div class="relative h-180 w-full overflow-hidden rounded-[2.2rem] bg-white">

                <button
                    type="button"
                    class="absolute right-3 top-3 z-20 flex h-7 w-7 items-center justify-center rounded-full bg-white/80 text-slate-600 shadow-sm backdrop-blur-sm hover:bg-white"
                    title="Muat ulang preview"
                    @click="reload"
                >
                    <RefreshCw class="h-3.5 w-3.5" :class="{ 'animate-spin': isLoading }" />
                </button>

                <div
                    v-if="isLoading"
                    class="absolute inset-0 z-10 flex items-center justify-center bg-white"
                >
                    <div class="flex flex-col items-center gap-2 text-slate-400">
                        <RefreshCw class="h-5 w-5 animate-spin" />
                        <p class="text-xs">Memuat preview&hellip;</p>
                    </div>
                </div>

                <iframe
                    ref="iframeRef"
                    :src="previewUrl"
                    class="h-full w-full border-0"
                    allow="autoplay"
                    @load="onIframeLoad"
                ></iframe>

                <div class="absolute bottom-1.5 left-1/2 z-20 h-1 w-32 -translate-x-1/2 rounded-full bg-slate-900/80"></div>
            </div>
        </div>
    </div>
</template>