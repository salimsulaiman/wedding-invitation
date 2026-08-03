<script setup lang="ts">
import UserLayout from '@/Layouts/UserLayout.vue'
import { useFormatters } from '@/Composables/useFormatters'
import { Head, Link } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import { ArrowLeft, Users, MessageSquare, Globe, Palette, ExternalLink, Copy, Check } from 'lucide-vue-next'
import { route } from 'ziggy-js'

defineOptions({ layout: UserLayout })

interface ThemeCategory {
    id: number
    name: string
}

interface Theme {
    id: number
    name: string
    thumbnail: string | null
    category: ThemeCategory | null
}

interface Domain {
    id: number
    name: string
    is_active: boolean
}

interface Invitation {
    id: number
    name: string
    status: string
    is_active: boolean
    expired_at: string | null
    max_guest: number | null
    created_at: string
    theme: Theme | null
    domain: Domain | null
    guests_count: number
    wishes_count: number
}

const props = defineProps<{
    invitation: Invitation
}>()

const { formatDate } = useFormatters()

const statusInfo = computed(() => {
    if (!props.invitation.is_active) {
        return { text: 'Nonaktif', class: 'bg-red-50 text-red-600' }
    }

    if (!props.invitation.expired_at) {
        return { text: 'Aktif · Tanpa batas waktu', class: 'bg-emerald-50 text-emerald-700' }
    }

    const expired = new Date(props.invitation.expired_at) < new Date()

    return expired
        ? { text: 'Kedaluwarsa', class: 'bg-red-50 text-red-600' }
        : { text: `Aktif hingga ${formatDate(props.invitation.expired_at)}`, class: 'bg-emerald-50 text-emerald-700' }
})

const publicUrl = computed(() => {
    if (!props.invitation.domain?.name) return null
    return `${window.location.origin}/${props.invitation.domain.name}`
})

const copied = ref(false)

const copyLink = () => {
    if (!publicUrl.value) return
    navigator.clipboard.writeText(publicUrl.value).then(() => {
        copied.value = true
        setTimeout(() => (copied.value = false), 2000)
    })
}
</script>

<template>

    <Head :title="invitation.name" />

    <div class="mx-auto max-w-3xl space-y-6">
        <div class="flex items-center gap-3">
            <Link :href="route('user.invitations.index')"
                class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-600">
                <ArrowLeft class="h-4 w-4" />
            </Link>
            <div class="min-w-0 flex-1">
                <div class="flex items-center gap-2">
                    <h2 class="truncate text-lg font-semibold text-slate-900">{{ invitation.name }}</h2>
                    <span class="shrink-0 rounded-full px-2.5 py-1 text-xs font-medium" :class="statusInfo.class">
                        {{ statusInfo.text }}
                    </span>
                </div>
                <p class="mt-0.5 text-sm text-slate-500">Dibuat {{ formatDate(invitation.created_at) }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <div class="flex items-center gap-2 text-slate-500">
                    <Users class="h-4 w-4" />
                    <p class="text-sm font-medium">Tamu Terdaftar</p>
                </div>
                <p class="mt-2 text-2xl font-semibold text-slate-900">
                    {{ invitation.guests_count }}
                    <span class="text-sm font-normal text-slate-400">
                        {{ invitation.max_guest ? `/ ${invitation.max_guest}` : '/ Tanpa batas' }}
                    </span>
                </p>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <div class="flex items-center gap-2 text-slate-500">
                    <MessageSquare class="h-4 w-4" />
                    <p class="text-sm font-medium">Ucapan Masuk</p>
                </div>
                <p class="mt-2 text-2xl font-semibold text-slate-900">{{ invitation.wishes_count }}</p>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <div class="flex items-center gap-2 text-slate-500">
                    <Globe class="h-4 w-4" />
                    <p class="text-sm font-medium">Domain</p>
                </div>
                <p class="mt-2 truncate text-sm font-medium text-slate-900">
                    {{ invitation.domain?.name ?? 'Belum diatur' }}
                </p>
                <span v-if="invitation.domain"
                    class="mt-1 inline-block rounded-full px-2 py-0.5 text-[11px] font-medium"
                    :class="invitation.domain.is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-600'">
                    {{ invitation.domain.is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
            </div>
        </div>

        <div v-if="publicUrl" class="rounded-xl border border-pink-100 bg-pink-50/40 p-5">
            <p class="text-sm font-medium text-slate-900">Link Undangan Anda</p>
            <div class="mt-3 flex items-center gap-2">
                <div
                    class="flex-1 truncate rounded-lg border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-600">
                    {{ publicUrl }}
                </div>
                <button
                    class="flex shrink-0 items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3.5 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-50"
                    @click="copyLink">
                    <component :is="copied ? Check : Copy" class="h-4 w-4" />
                    {{ copied ? 'Tersalin' : 'Salin' }}
                </button>

                <Link :href="publicUrl" target="_blank" class="flex shrink-0 items-center gap-1.5 rounded-lg bg-pink-600 px-3.5 py-2.5 text-sm font-semibold
                    text-white transition hover:bg-pink-700">
                    <ExternalLink class="h-4 w-4" />
                    Lihat
                </Link>
            </div>
        </div>

        <div v-else class="rounded-xl border border-dashed border-slate-300 bg-white p-5 text-center">
            <Globe class="mx-auto h-6 w-6 text-slate-300" />
            <p class="mt-2 text-sm text-slate-500">Domain belum diatur untuk undangan ini.</p>
            <Link :href="route('builder.index', invitation.id)"
                class="mt-3 inline-block text-sm font-medium text-pink-600 hover:underline">
                Atur domain di builder
            </Link>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-5">
            <div class="flex items-center gap-2">
                <Palette class="h-4 w-4 text-pink-600" />
                <p class="text-sm font-medium text-slate-900">Tema yang Dipakai</p>
            </div>

            <div v-if="invitation.theme" class="mt-3 flex items-center gap-3">
                <div
                    class="flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-lg bg-slate-100">
                    <img v-if="invitation.theme.thumbnail" :src="`/storage/${invitation.theme.thumbnail}`"
                        class="h-full w-full object-cover" />
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-800">{{ invitation.theme.name }}</p>
                    <p v-if="invitation.theme.category" class="text-xs text-slate-400">
                        Paket {{ invitation.theme.category.name }}
                    </p>
                </div>
            </div>

            <p v-else class="mt-3 text-sm text-slate-400">Belum memilih tema.</p>
        </div>

        <Link :href="route('builder.index', invitation.id)"
            class="flex w-full items-center justify-center gap-1.5 rounded-lg bg-pink-600 py-3 text-sm font-semibold text-white transition hover:bg-pink-700">
            Buka Builder
        </Link>
    </div>
</template>