<script setup>
import { useFormatters } from '@/composables/useFormatters'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { ArrowLeft, Power, Trash2, ExternalLink, Users, MessageSquare, Globe } from 'lucide-vue-next'

defineOptions({ layout: AdminLayout })

const props = defineProps({
    invitation: Object,
})

const { formatDate, formatDateTime, formatCurrency } = useFormatters()

const statusLabel = {
    draft: { text: 'Draf', class: 'bg-slate-100 text-slate-600' },
    published: { text: 'Terbit', class: 'bg-emerald-50 text-emerald-700' },
    inactive: { text: 'Nonaktif', class: 'bg-red-50 text-red-600' },
}

const toggleActive = () => {
    router.patch(route('admin.invitations.toggle-active', props.invitation.id), {}, { preserveScroll: true })
}

const destroy = () => {
    if (confirm(`Hapus undangan "${props.invitation.name}"? Tindakan ini tidak bisa dibatalkan.`)) {
        router.delete(route('admin.invitations.destroy', props.invitation.id))
    }
}
</script>

<template>
    <Head :title="invitation.name" />

    <div class="max-w-7xl space-y-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <Link :href="route('admin.invitations.index')" class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-600">
                    <ArrowLeft class="h-4 w-4" />
                </Link>
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-lg font-semibold text-slate-900">{{ invitation.name }}</h2>
                        <span
                            class="rounded-full px-2.5 py-1 text-xs font-medium"
                            :class="statusLabel[invitation.status]?.class"
                        >
                            {{ statusLabel[invitation.status]?.text }}
                        </span>
                    </div>
                    <p class="mt-0.5 text-sm text-slate-500">Dibuat {{ formatDate(invitation.created_at) }}</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <Link
                    :href="route('builder.index', invitation.id)"
                    class="flex items-center gap-1.5 rounded-lg bg-pink-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-pink-700"
                >
                    <ExternalLink class="h-4 w-4" />
                    Buka Builder
                </Link>
                <button
                    title="Aktifkan/Nonaktifkan"
                    class="rounded-lg border border-slate-200 p-2.5 text-slate-500 transition hover:bg-slate-50"
                    @click="toggleActive"
                >
                    <Power class="h-4 w-4" />
                </button>
                <button
                    title="Hapus"
                    class="rounded-lg border border-slate-200 p-2.5 text-slate-500 transition hover:bg-red-50 hover:text-red-600"
                    @click="destroy"
                >
                    <Trash2 class="h-4 w-4" />
                </button>
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
                <a :href="`/${invitation.domain?.name}`" target="_blank" class="mt-2 block text-sm font-medium text-slate-900">
                    {{ invitation.domain?.name ?? 'Belum diatur' }}
                </a>
                <span
                    v-if="invitation.domain"
                    class="mt-1 inline-block rounded-full px-2 py-0.5 text-[11px] font-medium"
                    :class="invitation.domain.is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-600'"
                >
                    {{ invitation.domain.is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <h3 class="text-sm font-semibold text-slate-900">Informasi Customer</h3>
                <p class="mt-3 text-sm font-medium text-slate-800">{{ invitation.user?.name }}</p>
                <p class="text-sm text-slate-500">{{ invitation.user?.email }}</p>
                <p v-if="invitation.user?.phone" class="mt-1 text-sm text-slate-500">{{ invitation.user.phone }}</p>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <h3 class="text-sm font-semibold text-slate-900">Tema Dipakai</h3>
                <p class="mt-3 text-sm text-slate-700">{{ invitation.theme?.name ?? 'Belum pilih tema' }}</p>
                <p v-if="invitation.theme?.category" class="mt-0.5 text-xs text-slate-400">
                    Paket {{ invitation.theme.category.name }}
                </p>
            </div>
        </div>

        <div v-if="invitation.couples?.length" class="rounded-xl border border-slate-200 bg-white p-5">
            <h3 class="text-sm font-semibold text-slate-900">Mempelai</h3>
            <div class="mt-3 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div v-for="couple in invitation.couples" :key="couple.id">
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        {{ couple.type === 'groom' ? 'Mempelai Pria' : 'Mempelai Wanita' }}
                    </p>
                    <p class="mt-1 text-sm font-medium text-slate-800">{{ couple.full_name }}</p>
                    <p v-if="couple.nickname" class="text-sm text-slate-500">({{ couple.nickname }})</p>
                </div>
            </div>
        </div>

        <div v-if="invitation.events?.length" class="rounded-xl border border-slate-200 bg-white p-5">
            <h3 class="text-sm font-semibold text-slate-900">Jadwal Acara</h3>
            <div class="mt-3 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div v-for="event in invitation.events" :key="event.id">
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        {{ event.type === 'akad' ? 'Akad Nikah' : 'Resepsi' }}
                    </p>
                    <p class="mt-1 text-sm text-slate-800">{{ formatDate(event.date) }} · {{ event.time }}</p>
                    <p class="text-sm text-slate-500">{{ event.place }}</p>
                </div>
            </div>
        </div>

        <div v-if="invitation.orders?.length" class="rounded-xl border border-slate-200 bg-white">
            <div class="border-b border-slate-200 px-5 py-4">
                <h3 class="text-sm font-semibold text-slate-900">Riwayat Pesanan Terkait</h3>
            </div>
            <div class="divide-y divide-slate-100">
                <div v-for="order in invitation.orders" :key="order.id" class="flex items-center justify-between px-5 py-3.5">
                    <p class="text-sm text-slate-700">{{ formatDateTime(order.ordered_at) }}</p>
                    <p class="text-sm font-medium text-slate-900">{{ formatCurrency(order.price) }}</p>
                </div>
            </div>
        </div>
    </div>
</template>