<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Pagination from '@/Components/Admin/Pagination.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import { Search, Users, MessageSquare, ChevronRight } from 'lucide-vue-next'
import { route } from 'ziggy-js'
import debounce from 'lodash/debounce'
import { useFormatters } from '@/Composables/useFormatters'

defineOptions({ layout: AdminLayout })

interface User {
    username: string
}

interface Invitation {
    id: number
    name: string
    user: User
    created_at: string
    guests_count: number
    wishes_count: number
}

interface PaginationLink {
    url: string | null
    label: string
    active: boolean
}

interface PaginationData<T> {
    data: T[]
    links: PaginationLink[]
    current_page: number
    last_page: number
    per_page: number
    total: number
}

interface Filters {
    search?: string
}

const props = defineProps<{
    invitations: PaginationData<Invitation>
    filters: Filters
}>()

const { formatDate } = useFormatters()

const search = ref<string>(props.filters.search ?? '')

const applyFilters = (): void => {
    router.get(
        route('admin.guests.index'),
        { search: search.value },
        {
            preserveState: true,
            replace: true,
        },
    )
}

watch(search, debounce(applyFilters, 350))
</script>

<template>
    <Head title="Tamu & Ucapan" />

    <div class="space-y-6">
        <div>
            <h2 class="text-lg font-semibold text-slate-900">Tamu &amp; Ucapan</h2>
            <p class="mt-1 text-sm text-slate-500">Pilih undangan untuk melihat rekap tamu dan ucapannya.</p>
        </div>

        <div class="relative">
            <Search class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
            <input
                v-model="search"
                type="text"
                placeholder="Cari nama undangan..."
                class="w-full max-w-md rounded-lg border border-slate-300 bg-white py-2 pl-9 pr-3 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
            />
        </div>

        <div
            v-if="invitations.data.length === 0"
            class="rounded-xl border border-slate-200 bg-white px-5 py-16 text-center text-sm text-slate-400"
        >
            Belum ada undangan.
        </div>

        <div v-else class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <Link
                v-for="invitation in invitations.data"
                :key="invitation.id"
                :href="route('admin.guests.show', invitation.id)"
                class="group flex flex-col justify-between rounded-xl border border-slate-200 bg-white p-5 transition hover:border-pink-300"
            >
                <div>
                    <p class="font-semibold text-slate-900 transition group-hover:text-pink-600">
                        {{ invitation.name }}
                    </p>
                    <p class="mt-1 text-xs text-slate-400">Dibuat {{ formatDate(invitation.created_at) }}</p>
                    <p class="mt-1 text-xs text-slate-700">{{ invitation.user.username }}</p>
                </div>

                <div class="mt-4 flex items-center justify-between">
                    <div class="flex items-center gap-4 text-sm text-slate-500">
                        <span class="flex items-center gap-1.5">
                            <Users class="h-4 w-4 text-slate-400" />
                            {{ invitation.guests_count }} tamu
                        </span>
                        <span class="flex items-center gap-1.5">
                            <MessageSquare class="h-4 w-4 text-slate-400" />
                            {{ invitation.wishes_count }} ucapan
                        </span>
                    </div>

                    <ChevronRight
                        class="h-4 w-4 text-slate-300 transition group-hover:text-pink-500"
                    />
                </div>
            </Link>
        </div>

        <Pagination :links="invitations.links" />
    </div>
</template>