<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { ChevronDown, Check } from 'lucide-vue-next'

const props = defineProps({
    modelValue: {
        type: [String, Number, null],
        default: '',
    },
    // Array bebas: bisa [{ value, label }] atau array object apa saja
    // (misal categories dari API) selama valueKey/labelKey diarahkan dengan benar.
    options: {
        type: Array,
        default: () => [],
    },
    // Key yang dipakai untuk ambil value & label dari tiap item options.
    // Default 'value'/'label' cocok untuk options statis.
    // Untuk data dari API (mis. categories: [{ id, name }]) tinggal set
    // value-key="id" label-key="name".
    valueKey: {
        type: String,
        default: 'value',
    },
    labelKey: {
        type: String,
        default: 'label',
    },
    // Label untuk opsi "semua" di baris paling atas
    placeholder: {
        type: String,
        default: '',
    },

    includeAllOption: {
        type: Boolean,
        default: true,
    },

    allLabel: {
        type: String,
        default: 'Semua',
    },

    topOption: {
        type: Object,
        default: null,
    },

    emptyText: {
        type: String,
        default: 'Tidak ada data',
    },
})

const emit = defineEmits(['update:modelValue'])

const root = ref(null)
const open = ref(false)
const activeIndex = ref(-1)

function getValue(option) {
    return typeof option === 'object' && option !== null ? option[props.valueKey] : option
}

function getLabel(option) {
    return typeof option === 'object' && option !== null ? option[props.labelKey] : option
}

const normalizedOptions = computed(() =>
    props.options.map((option) => ({
        value: getValue(option),
        label: getLabel(option),
    })),
)

const allOptions = computed(() => {
    const options = [...normalizedOptions.value]

    if (props.topOption) {
        options.unshift(props.topOption)
    } else if (props.includeAllOption) {
        options.unshift({
            value: '',
            label: props.allLabel,
        })
    }

    return options
})

const selected = computed(() =>
    allOptions.value.find(
        option => option.value === props.modelValue
    )
)

const displayLabel = computed(() => {
    if (selected.value) {
        return selected.value.label
    }

    return props.placeholder
})

function toggle() {
    open.value = !open.value

    if (open.value) {
        activeIndex.value = allOptions.value.findIndex(
            (option) => option.value === props.modelValue,
        )
    }
}

function close() {
    open.value = false
    activeIndex.value = -1
}

function select(option) {
    emit('update:modelValue', option.value)
    close()
}

function onKeydown(e) {
    if (!open.value) {
        if (e.key === 'Enter' || e.key === ' ' || e.key === 'ArrowDown') {
            e.preventDefault()
            toggle()
        }
        return
    }

    if (e.key === 'Escape') {
        e.preventDefault()
        close()
    } else if (e.key === 'ArrowDown') {
        e.preventDefault()
        activeIndex.value = Math.min(activeIndex.value + 1, allOptions.value.length - 1)
    } else if (e.key === 'ArrowUp') {
        e.preventDefault()
        activeIndex.value = Math.max(activeIndex.value - 1, 0)
    } else if (e.key === 'Enter') {
        e.preventDefault()
        const option = allOptions.value[activeIndex.value]
        if (option) select(option)
    }
}

function onClickOutside(e) {
    if (root.value && !root.value.contains(e.target)) {
        close()
    }
}

onMounted(() => document.addEventListener('click', onClickOutside))
onBeforeUnmount(() => document.removeEventListener('click', onClickOutside))
</script>

<template>
    <div ref="root" class="relative" @keydown="onKeydown">
        <button type="button"
            class="flex w-full items-center justify-between gap-2 rounded-lg border border-slate-300 bg-white px-3 py-2 text-left text-sm text-slate-700 transition hover:border-slate-400 focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30 sm:min-w-40"
            @click="toggle">
            <span class="truncate">{{ displayLabel }}</span>

            <ChevronDown class="h-4 w-4 shrink-0 text-slate-400" />
        </button>

        <Transition enter-active-class="transition duration-150" enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100" leave-active-class="transition duration-100"
            leave-from-class="opacity-100 scale-100" leave-to-class="opacity-0 scale-95">
            <div v-if="open"
                class="absolute z-50 mt-2 w-full min-w-40 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl">
                <div class="max-h-72 overflow-y-auto py-1">
                    <button v-for="(option, index) in allOptions" :key="option.value" type="button"
                        class="flex w-full items-center justify-between gap-3 px-4 py-2.5 text-left text-sm transition"
                        :class="[
                            index === activeIndex ? 'bg-pink-50' : 'hover:bg-slate-50',
                            option.value === modelValue ? 'font-medium text-pink-600' : 'text-slate-700',
                        ]" @click="select(option)">
                        <span class="truncate">{{ option.label }}</span>

                        <Check v-if="option.value === modelValue" class="h-4 w-4 shrink-0 text-pink-600" />
                    </button>

                    <div v-if="!allOptions.length" class="py-8 text-center text-sm text-slate-400">
                        {{ emptyText }}
                    </div>
                </div>
            </div>
        </Transition>
    </div>
</template>