<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { Users, Mail, CheckCircle2, ShoppingBag, Wallet } from 'lucide-vue-next'

defineOptions({ layout: AdminLayout })

const props = defineProps({
    stats: Object,
    recentInvitations: Array,
    recentOrders: Array,
    recentWishes: Array,
})

const formatCurrency = (value) =>
    new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value)

const formatDate = (value) =>
    new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }).format(new Date(value))

const statusLabel = {
    draft: { text: 'Draf', class: 'bg-slate-100 text-slate-600' },
    published: { text: 'Terbit', class: 'bg-emerald-50 text-emerald-700' },
    inactive: { text: 'Nonaktif', class: 'bg-red-50 text-red-600' },
    pending: { text: 'Menunggu', class: 'bg-amber-50 text-amber-700' },
    paid: { text: 'Lunas', class: 'bg-emerald-50 text-emerald-700' },
    cancelled: { text: 'Batal', class: 'bg-red-50 text-red-600' },
    completed: { text: 'Selesai', class: 'bg-emerald-50 text-emerald-700' },
}
</script>

<template>
    <Head title="Dashboard" />

    <div class="space-y-6">
        <div>
            <h2 class="text-lg font-semibold text-slate-900">Ringkasan</h2>
            <p class="mt-1 text-sm text-slate-500">Gambaran umum aktivitas platform hari ini.</p>
        </div>

        <!-- Stat cards -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-slate-500">Total Customer</p>
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-pink-50">
                        <Users class="h-4 w-4 text-pink-600" />
                    </div>
                </div>
                <p class="mt-3 text-2xl font-semibold text-slate-900">{{ stats.total_customers }}</p>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-slate-500">Total Undangan</p>
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-pink-50">
                        <Mail class="h-4 w-4 text-pink-600" />
                    </div>
                </div>
                <p class="mt-3 text-2xl font-semibold text-slate-900">{{ stats.total_invitations }}</p>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-slate-500">Undangan Aktif</p>
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-pink-50">
                        <CheckCircle2 class="h-4 w-4 text-pink-600" />
                    </div>
                </div>
                <p class="mt-3 text-2xl font-semibold text-slate-900">{{ stats.active_invitations }}</p>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-slate-500">Pendapatan Bulan Ini</p>
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-pink-50">
                        <Wallet class="h-4 w-4 text-pink-600" />
                    </div>
                </div>
                <p class="mt-3 text-2xl font-semibold text-slate-900">{{ formatCurrency(stats.revenue_this_month) }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <!-- Undangan terbaru -->
            <div class="rounded-xl border border-slate-200 bg-white">
                <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                    <h3 class="text-sm font-semibold text-slate-900">Undangan Terbaru</h3>
                    <Link :href="route('admin.invitations.index')" class="text-xs font-medium text-pink-600 hover:underline">
                        Lihat semua
                    </Link>
                </div>
                <div class="divide-y divide-slate-100">
                    <div v-if="recentInvitations.length === 0" class="px-5 py-8 text-center text-sm text-slate-400">
                        Belum ada undangan dibuat.
                    </div>
                    <Link
                        v-for="item in recentInvitations"
                        :key="item.id"
                        :href="route('admin.invitations.show', item.id)"
                        class="flex items-center justify-between px-5 py-3.5 transition hover:bg-slate-50"
                    >
                        <div>
                            <p class="text-sm font-medium text-slate-900">{{ item.name }}</p>
                            <p class="mt-0.5 text-xs text-slate-500">
                                {{ item.user?.name }} · {{ item.theme?.name ?? 'Belum pilih tema' }}
                            </p>
                        </div>
                        <span
                            class="rounded-full px-2.5 py-1 text-xs font-medium"
                            :class="statusLabel[item.status]?.class"
                        >
                            {{ statusLabel[item.status]?.text }}
                        </span>
                    </Link>
                </div>
            </div>

            <!-- Pesanan terbaru -->
            <div class="rounded-xl border border-slate-200 bg-white">
                <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                    <h3 class="text-sm font-semibold text-slate-900">Pesanan Terbaru</h3>
                    <Link :href="route('admin.orders.index')" class="text-xs font-medium text-pink-600 hover:underline">
                        Lihat semua
                    </Link>
                </div>
                <div class="divide-y divide-slate-100">
                    <div v-if="recentOrders.length === 0" class="px-5 py-8 text-center text-sm text-slate-400">
                        Belum ada pesanan masuk.
                    </div>
                    <Link
                        v-for="order in recentOrders"
                        :key="order.id"
                        :href="route('admin.orders.show', order.id)"
                        class="flex items-center justify-between px-5 py-3.5 transition hover:bg-slate-50"
                    >
                        <div>
                            <p class="text-sm font-medium text-slate-900">{{ order.user?.name }}</p>
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
                    </Link>
                </div>
            </div>
        </div>

        <!-- Ucapan terbaru -->
        <div class="rounded-xl border border-slate-200 bg-white">
            <div class="border-b border-slate-200 px-5 py-4">
                <h3 class="text-sm font-semibold text-slate-900">Ucapan &amp; Pesan Terbaru</h3>
            </div>
            <div class="divide-y divide-slate-100">
                <div v-if="recentWishes.length === 0" class="px-5 py-8 text-center text-sm text-slate-400">
                    Belum ada ucapan masuk.
                </div>
                <div v-for="wish in recentWishes" :key="wish.id" class="px-5 py-3.5">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-slate-900">{{ wish.name }}</p>
                        <p class="text-xs text-slate-400">{{ wish.invitation?.name }}</p>
                    </div>
                    <p class="mt-1 text-sm text-slate-600">{{ wish.message }}</p>
                </div>
            </div>
        </div>
    </div>
</template>