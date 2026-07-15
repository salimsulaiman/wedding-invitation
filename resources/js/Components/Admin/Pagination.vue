<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    links: Array,
})
</script>

<template>
    <div v-if="links.length > 3" class="flex flex-wrap items-center gap-1 px-5 py-4">
        <template v-for="(link, index) in links" :key="index">
            <Link
                v-if="link.url"
                :href="link.url"
                v-html="link.label"
                class="rounded-lg px-3 py-1.5 text-xs font-medium transition"
                :class="link.active ? 'bg-pink-600 text-white' : 'text-slate-500 hover:bg-slate-50'"
            />
            <span
                v-else
                v-html="link.label"
                class="rounded-lg px-3 py-1.5 text-xs font-medium text-slate-300"
            />
        </template>
    </div>
</template>