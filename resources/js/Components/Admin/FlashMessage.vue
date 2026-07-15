<script setup>
import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import { CheckCircle2, XCircle } from 'lucide-vue-next'

const page = usePage()

const success = computed(() => page.props.flash?.success)
const error = computed(() => page.props.flash?.error)
</script>

<template>
    <div v-if="success" class="mb-5 flex items-center gap-2.5 rounded-lg bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
        <CheckCircle2 class="h-4 w-4 shrink-0" />
        {{ success }}
    </div>
    <div v-if="error" class="mb-5 flex items-center gap-2.5 rounded-lg bg-red-50 px-4 py-3 text-sm font-medium text-red-600">
        <XCircle class="h-4 w-4 shrink-0" />
        {{ error }}
    </div>
</template>