<script setup lang="ts">
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { Calendar } from 'lucide-vue-next'
import { useFormatters } from '@/Composables/useFormatters'

interface Preset {
    label: string
    days: number
}

const props = withDefaults(
    defineProps<{
        from?: string
        to?: string
        placeholder?: string
        fromLabel?: string
        toLabel?: string
        presets?: Preset[]
    }>(),
    {
        from: '',
        to: '',
        placeholder: 'Semua Tanggal',
        fromLabel: 'Dari Tanggal',
        toLabel: 'Sampai Tanggal',
        presets: () => [
            { label: 'Hari Ini', days: 0 },
            { label: '7 Hari Terakhir', days: 6 },
            { label: '30 Hari Terakhir', days: 29 },
        ],
    },
)

const emit = defineEmits<{
    (e: 'update:from', value: string): void
    (e: 'update:to', value: string): void
}>()

const { formatDate } = useFormatters()

const root = ref<HTMLElement | null>(null)
const open = ref(false)

function toIsoDate(date: Date): string {
    return date.toISOString().slice(0, 10)
}

function applyPreset(days: number): void {
    const to = new Date()
    const from = new Date()
    from.setDate(from.getDate() - days)

    emit('update:from', toIsoDate(from))
    emit('update:to', toIsoDate(to))
}

function reset(): void {
    emit('update:from', '')
    emit('update:to', '')
}

function onFromChange(e: Event): void {
    emit('update:from', (e.target as HTMLInputElement).value)
}

function onToChange(e: Event): void {
    emit('update:to', (e.target as HTMLInputElement).value)
}

function toggle(): void {
    open.value = !open.value
}

function close(): void {
    open.value = false
}

function onClickOutside(e: MouseEvent): void {
    if (root.value && !root.value.contains(e.target as Node)) {
        close()
    }
}

onMounted(() => document.addEventListener('click', onClickOutside))
onBeforeUnmount(() => document.removeEventListener('click', onClickOutside))

const hasValue = computed(() => Boolean(props.from || props.to))

const displayLabel = computed(() => {
    if (props.from && props.to) {
        return `${formatDate(props.from)} - ${formatDate(props.to)}`
    }
    if (props.from) {
        return `Sejak ${formatDate(props.from)}`
    }
    if (props.to) {
        return `Sampai ${formatDate(props.to)}`
    }
    return props.placeholder
})
</script>

<template>
    <div ref="root" class="relative">
        <button
            type="button"
            class="flex w-full items-center gap-2 rounded-lg border border-slate-300 bg-white px-3 py-2 text-left text-sm transition hover:border-slate-400 focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30 sm:w-auto sm:min-w-[11rem]"
            @click="toggle"
        >
            <Calendar class="h-4 w-4 shrink-0 text-slate-400" />

            <span
                class="truncate"
                :class="hasValue ? 'font-medium text-slate-900' : 'text-slate-400'"
            >
                {{ displayLabel }}
            </span>
        </button>

        <Transition
            enter-active-class="transition duration-150"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition duration-100"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
        >
            <div
                v-if="open"
                class="absolute right-0 z-50 mt-2 w-80 overflow-hidden rounded-xl border border-slate-200 bg-white p-4 shadow-xl"
            >
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="mb-1 block text-xs font-medium text-slate-500">
                            {{ fromLabel }}
                        </label>
                        <input
                            type="date"
                            :value="from"
                            :max="to || undefined"
                            class="w-full rounded-lg border border-slate-300 px-2.5 py-1.5 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                            @change="onFromChange"
                        />
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-medium text-slate-500">
                            {{ toLabel }}
                        </label>
                        <input
                            type="date"
                            :value="to"
                            :min="from || undefined"
                            class="w-full rounded-lg border border-slate-300 px-2.5 py-1.5 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                            @change="onToChange"
                        />
                    </div>
                </div>

                <div v-if="presets.length" class="mt-3 flex flex-wrap gap-1.5">
                    <button
                        v-for="preset in presets"
                        :key="preset.label"
                        type="button"
                        class="rounded-full border border-slate-200 px-2.5 py-1 text-xs text-slate-600 transition hover:border-pink-300 hover:text-pink-600"
                        @click="applyPreset(preset.days)"
                    >
                        {{ preset.label }}
                    </button>
                </div>

                <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-3">
                    <button
                        type="button"
                        class="text-xs font-medium text-slate-400 hover:text-slate-600"
                        @click="reset"
                    >
                        Reset
                    </button>

                    <!-- <button
                        type="button"
                        class="rounded-lg bg-pink-600 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-pink-700"
                        @click="close"
                    >
                        Terapkan
                    </button> -->
                </div>
            </div>
        </Transition>
    </div>
</template>