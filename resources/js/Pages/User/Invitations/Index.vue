<script setup lang="ts">
import UserLayout from '@/Layouts/UserLayout.vue'
import { useFormatters } from '@/Composables/useFormatters'
import { Head, Link } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import { Mail, Users, MessageSquare, Plus, Search } from 'lucide-vue-next'
import { route } from 'ziggy-js'

defineOptions({ layout: UserLayout })

interface Theme {
    id: number
    name: string
    thumbnail: string | null
}

interface Invitation {
    id: number
    name: string
    status: string
    is_active: boolean
    expired_at: string | null
    max_guest: number | null
    theme: Theme | null
    guests_count: number
    wishes_count: number
}

const props = defineProps<{
    invitations: Invitation[]
}>()

const { formatDate } = useFormatters()

const search = ref('')

const filteredInvitations = computed(() => {
    if (!search.value.trim()) return props.invitations
    const keyword = search.value.toLowerCase()
    return props.invitations.filter((invitation) => invitation.name.toLowerCase().includes(keyword))
})

const invitationStatus = (invitation: Invitation) => {
    if (!invitation.is_active) {
        return { text: 'Nonaktif', class: 'bg-red-50 text-red-600' }
    }

    if (!invitation.expired_at) {
        return { text: 'Aktif · Tanpa batas waktu', class: 'bg-emerald-50 text-emerald-700' }
    }

    const expired = new Date(invitation.expired_at) < new Date()

    return expired
        ? { text: 'Kedaluwarsa', class: 'bg-red-50 text-red-600' }
        : { text: `Aktif hingga ${formatDate(invitation.expired_at)}`, class: 'bg-emerald-50 text-emerald-700' }
}
</script>

<template>

    <Head title="Undangan Saya" />

    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Undangan Saya</h2>
                <p class="mt-1 text-sm text-slate-500">Semua undangan pernikahan digital yang Anda buat.</p>
            </div>
            <Link :href="route('user.invitations.create')"
                class="flex items-center gap-1.5 rounded-lg bg-pink-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-pink-700">
                <Plus class="h-4 w-4" />
                Buat Undangan
            </Link>
        </div>

        <div v-if="invitations.length > 0" class="relative max-w-sm">
            <Search class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
            <input v-model="search" type="text" placeholder="Cari nama undangan..."
                class="w-full rounded-lg border border-slate-300 py-2 pl-9 pr-3 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30" />
        </div>

        <div v-if="invitations.length === 0"
            class="rounded-xl border border-dashed border-slate-300 bg-white px-5 py-12 text-center">
            <Mail class="mx-auto h-8 w-8 text-slate-300" />
            <p class="mt-3 text-sm font-medium text-slate-600">Anda belum punya undangan.</p>
            <p class="mt-1 text-sm text-slate-400">Mulai buat undangan pernikahan digital pertama Anda.</p>
            <Link :href="route('user.invitations.create')"
                class="mt-4 inline-flex items-center gap-1.5 rounded-lg bg-pink-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-pink-700">
                <Plus class="h-4 w-4" />
                Buat Undangan Pertama
            </Link>
        </div>

        <div v-else-if="filteredInvitations.length === 0"
            class="rounded-xl border border-dashed border-slate-300 bg-white px-5 py-10 text-center text-sm text-slate-400">
            Tidak ada undangan yang cocok dengan pencarian "{{ search }}".
        </div>

        <div v-else class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div v-for="invitation in filteredInvitations" :key="invitation.id"
                class="overflow-hidden rounded-xl border border-slate-200 bg-white">
                <div class="flex h-32 items-center justify-center bg-slate-100">
                    <img v-if="invitation.theme?.thumbnail" :src="`/storage/${invitation.theme.thumbnail}`"
                        class="h-full w-full object-cover" />
                    <Mail v-else class="h-8 w-8 text-slate-300" />
                </div>

                <div class="p-4">
                    <p class="truncate text-sm font-semibold text-slate-900">{{ invitation.name }}</p>
                    <p class="mt-0.5 text-xs text-slate-500">{{ invitation.theme?.name ?? 'Belum pilih tema' }}</p>

                    <span class="mt-3 inline-block rounded-full px-2.5 py-1 text-xs font-medium"
                        :class="invitationStatus(invitation).class">
                        {{ invitationStatus(invitation).text }}
                    </span>

                    <div class="mt-4 flex items-center gap-4 border-t border-slate-100 pt-4 text-xs text-slate-500">
                        <span class="flex items-center gap-1.5">
                            <Users class="h-3.5 w-3.5" />
                            {{ invitation.guests_count }}
                            {{ invitation.max_guest ? `/ ${invitation.max_guest}` : '' }}
                        </span>
                        <span class="flex items-center gap-1.5">
                            <MessageSquare class="h-3.5 w-3.5" />
                            {{ invitation.wishes_count }}
                        </span>
                    </div>

                    <div class="mt-4 grid grid-cols-2 gap-2">
                        <Link :href="route('user.invitations.show', invitation.id)"
                            class="rounded-lg border border-slate-200 py-2 text-center text-xs font-medium text-slate-600 transition hover:bg-slate-50">
                            Lihat Detail
                        </Link>
                        <Link :href="route('builder.index', invitation.id)"
                            class="rounded-lg bg-pink-600 py-2 text-center text-xs font-semibold text-white transition hover:bg-pink-700">
                            Buka Builder
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>