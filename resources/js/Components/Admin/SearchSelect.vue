<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue'
import { Check, ChevronDown, LoaderCircle, Search } from 'lucide-vue-next'

export interface SearchSelectOption {
    id: number | string
    title: string
    subtitle?: string
}

const props = withDefaults(
    defineProps<{
        modelValue: number | string | null
        options: SearchSelectOption[]
        loading?: boolean
        placeholder?: string
        emptyText?: string
    }>(),
    {
        loading: false,
        placeholder: 'Cari...',
        emptyText: 'Tidak ada data.',
    },
)

const emit = defineEmits<{
    (e: 'update:modelValue', value: number | string | null): void
    (e: 'search', value: string): void
}>()

const root = ref<HTMLElement | null>(null)
const input = ref<HTMLInputElement | null>(null)

const open = ref(false)
const keyword = ref('')
const activeIndex = ref(-1)

const selected = computed(() =>
    props.options.find((item) => item.id === props.modelValue),
)

watch(keyword, (value) => {
    emit('search', value)
})

watch(open, (value) => {
    if (value) {
        requestAnimationFrame(() => {
            input.value?.focus()
        })
    } else {
        keyword.value = ''
        activeIndex.value = -1
    }
})

const toggle = () => {
    open.value = !open.value
}

const select = (option: SearchSelectOption) => {
    emit('update:modelValue', option.id)

    open.value = false
}

const onClickOutside = (event: MouseEvent) => {
    if (!root.value) {
        return
    }

    if (!root.value.contains(event.target as Node)) {
        open.value = false
    }
}

const onKeydown = (event: KeyboardEvent) => {
    if (!open.value) {
        return
    }

    if (event.key === 'ArrowDown') {
        event.preventDefault()

        activeIndex.value =
            activeIndex.value < props.options.length - 1
                ? activeIndex.value + 1
                : 0
    }

    if (event.key === 'ArrowUp') {
        event.preventDefault()

        activeIndex.value =
            activeIndex.value > 0
                ? activeIndex.value - 1
                : props.options.length - 1
    }

    if (event.key === 'Enter') {
        event.preventDefault()

        if (activeIndex.value >= 0) {
            select(props.options[activeIndex.value])
        }
    }

    if (event.key === 'Escape') {
        open.value = false
    }
}

onMounted(() => {
    document.addEventListener('click', onClickOutside)
})

onBeforeUnmount(() => {
    document.removeEventListener('click', onClickOutside)
})
</script>

<template>
    <div ref="root" class="relative" @keydown="onKeydown">
        <button
            type="button"
            class="flex w-full items-center justify-between rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-left transition hover:border-slate-400 focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
            @click="toggle"
        >
            <div class="min-w-0">
                <p
                    v-if="selected"
                    class="truncate text-sm font-medium text-slate-900"
                >
                    {{ selected.title }}
                </p>

                <p
                    v-else
                    class="text-sm text-slate-400"
                >
                    {{ placeholder }}
                </p>

                <p
                    v-if="selected?.subtitle"
                    class="truncate text-xs text-slate-500"
                >
                    {{ selected.subtitle }}
                </p>
            </div>

            <ChevronDown
                class="h-4 w-4 shrink-0 text-slate-400"
            />
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
                class="absolute z-50 mt-2 w-full overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl"
            >
                <div class="border-b border-slate-100 p-3">
                    <div class="relative">
                        <Search
                            class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"
                        />

                        <input
                            ref="input"
                            v-model="keyword"
                            type="text"
                            :placeholder="placeholder"
                            class="w-full rounded-lg border border-slate-300 py-2 pl-9 pr-3 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                        />
                    </div>
                </div>

                <div class="max-h-72 overflow-y-auto">
                    <div
                        v-if="loading"
                        class="flex items-center justify-center gap-2 py-6 text-sm text-slate-500"
                    >
                        <LoaderCircle class="h-4 w-4 animate-spin" />
                        Memuat...
                    </div>

                    <template v-else>
                        <button
                            v-for="(option, index) in options"
                            :key="option.id"
                            type="button"
                            class="flex w-full items-center justify-between px-4 py-3 text-left transition"
                            :class="[
                                index === activeIndex
                                    ? 'bg-pink-50'
                                    : 'hover:bg-slate-50',
                            ]"
                            @click="select(option)"
                        >
                            <div class="min-w-0">
                                <p
                                    class="truncate text-sm font-medium text-slate-900"
                                >
                                    {{ option.title }}
                                </p>

                                <p
                                    v-if="option.subtitle"
                                    class="truncate text-xs text-slate-500"
                                >
                                    {{ option.subtitle }}
                                </p>
                            </div>

                            <Check
                                v-if="option.id === modelValue"
                                class="h-4 w-4 text-pink-600"
                            />
                        </button>

                        <div
                            v-if="!options.length"
                            class="py-8 text-center text-sm text-slate-400"
                        >
                            {{ emptyText }}
                        </div>
                    </template>
                </div>
            </div>
        </Transition>
    </div>
</template>