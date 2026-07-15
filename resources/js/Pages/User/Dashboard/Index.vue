<script setup>
import UserLayout from '@/Layouts/UserLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { Mail, Users, MessageSquare, Plus } from 'lucide-vue-next'

defineOptions({ layout: UserLayout })

defineProps({
    invitations: Array,
    recentOrders: Array,
})

const formatCurrency = (value) =>
    new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value)

const formatDate = (value) =>
    new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }).format(new Date(value))

const statusLabel = {
    pending: { text: 'Menunggu', class: 'bg-amber-50 text-amber-700' },
    paid: { text: 'Lunas', class: 'bg-emerald-50 text-emerald-700' },
    cancelled: { text: 'Batal', class: 'bg-red-50 text-red-600' },
    completed: { text: 'Selesai', class: 'bg-emerald-50 text-emerald-700' },
}

const activeStatus = (invitation) => {
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
    <Head title="Dashboard" />

    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Undangan Saya</h2>
                <p class="mt-1 text-sm text-slate-500">Kelola undangan pernikahan digital Anda.</p>
            </div>
            <Link
                :href="route('user.invitations.index')"
                class="flex items-center gap-1.5 rounded-lg bg-pink-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-pink-700"
            >
                <Plus class="h-4 w-4" />
                Buat Undangan
            </Link>
        </div>

        <!-- List undangan -->
        <div v-if="invitations.length === 0" class="rounded-xl border border-dashed border-slate-300 bg-white px-5 py-12 text-center">
            <Mail class="mx-auto h-8 w-8 text-slate-300" />
            <p class="mt-3 text-sm font-medium text-slate-600">Anda belum punya undangan.</p>
            <p class="mt-1 text-sm text-slate-400">Mulai buat undangan pernikahan digital pertama Anda.</p>
        </div>

        <div v-else class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div
                v-for="invitation in invitations"
                :key="invitation.id"
                class="rounded-xl border border-slate-200 bg-white p-5"
            >
                <div class="flex items-start justify-between">
                    <p class="text-sm font-semibold text-slate-900">{{ invitation.name }}</p>
                </div>
                <p class="mt-1 text-xs text-slate-500">{{ invitation.theme?.name ?? 'Belum pilih tema' }}</p>

                <span
                    class="mt-3 inline-block rounded-full px-2.5 py-1 text-xs font-medium"
                    :class="activeStatus(invitation).class"
                >
                    {{ activeStatus(invitation).text }}
                </span>

                <div class="mt-4 flex items-center gap-4 border-t border-slate-100 pt-4 text-xs text-slate-500">
                    <span class="flex items-center gap-1.5">
                        <Users class="h-3.5 w-3.5" />
                        {{ invitation.guests_count }} tamu
                    </span>
                    <span class="flex items-center gap-1.5">
                        <MessageSquare class="h-3.5 w-3.5" />
                        {{ invitation.wishes_count }} ucapan
                    </span>
                </div>

                <Link
                    :href="route('builder.index', invitation.id)"
                    class="mt-4 block w-full rounded-lg border border-slate-200 py-2 text-center text-sm font-medium text-slate-700 transition hover:bg-slate-50"
                >
                    Buka Builder
                </Link>
            </div>
        </div>

        <!-- Riwayat pesanan -->
        <div class="rounded-xl border border-slate-200 bg-white">
            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                <h3 class="text-sm font-semibold text-slate-900">Riwayat Pesanan</h3>
                <Link :href="route('user.orders.index')" class="text-xs font-medium text-pink-600 hover:underline">
                    Lihat semua
                </Link>
            </div>
            <div class="divide-y divide-slate-100">
                <div v-if="recentOrders.length === 0" class="px-5 py-8 text-center text-sm text-slate-400">
                    Belum ada riwayat pesanan.
                </div>
                <div v-for="order in recentOrders" :key="order.id" class="flex items-center justify-between px-5 py-3.5">
                    <div>
                        <p class="text-sm font-medium text-slate-900">{{ order.theme?.name ?? 'Paket undangan' }}</p>
                        <p class="mt-0.5 text-xs text-slate-500">{{ formatDate(order.created_at) }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium text-slate-900">{{ formatCurrency(order.price) }}</p>
                        <span
                            class="mt-0.5 inline-block rounded-full px-2 py-0.5 text-[11px] font-medium"
                            :class="statusLabel[order.status]?.class"
                        >
                            {{ statusLabel[order.status]?.text }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>