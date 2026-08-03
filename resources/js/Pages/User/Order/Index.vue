<script setup lang="ts">
import UserLayout from '@/Layouts/UserLayout.vue'
import Pagination from '@/Components/Admin/Pagination.vue'
import { useFormatters } from '@/Composables/useFormatters'
import { Head, Link } from '@inertiajs/vue3'
import { ShoppingBag } from 'lucide-vue-next'
import { route } from 'ziggy-js'

defineOptions({ layout: UserLayout })

interface ThemeCategory {
    id: number
    name: string
}

interface Order {
    id: number
    price: number
    status: 'pending' | 'paid' | 'cancelled' | 'completed'
    created_at: string
    ordered_at: string | null
    theme_category: ThemeCategory | null
}

interface PaginationLink {
    url: string | null
    label: string
    active: boolean
}

interface PaginatedOrders {
    data: Order[]
    links: PaginationLink[]
}

defineProps<{
    orders: PaginatedOrders
}>()

const { formatCurrency, formatDate } = useFormatters()

const statusLabel: Record<Order['status'], { text: string; class: string }> = {
    pending: { text: 'Menunggu', class: 'bg-amber-50 text-amber-700' },
    paid: { text: 'Lunas', class: 'bg-emerald-50 text-emerald-700' },
    cancelled: { text: 'Batal', class: 'bg-red-50 text-red-600' },
    completed: { text: 'Selesai', class: 'bg-emerald-50 text-emerald-700' },
}
</script>

<template>

    <Head title="Riwayat Pesanan" />

    <div class="space-y-6">
        <div>
            <h2 class="text-lg font-semibold text-slate-900">Riwayat Pesanan</h2>
            <p class="mt-1 text-sm text-slate-500">Semua pesanan paket tema yang pernah Anda buat via WhatsApp.</p>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white">
            <div v-if="orders.data.length === 0" class="px-5 py-16 text-center">
                <ShoppingBag class="mx-auto h-8 w-8 text-slate-300" />
                <p class="mt-3 text-sm font-medium text-slate-600">Belum ada riwayat pesanan.</p>
                <p class="mt-1 text-sm text-slate-400">Hubungi admin melalui WhatsApp untuk memesan paket tema.</p>
            </div>

            <div v-else class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr
                            class="border-b border-slate-200 text-xs font-medium uppercase tracking-wide text-slate-400">
                            <th class="px-5 py-3">Paket</th>
                            <th class="px-5 py-3">Harga</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3">Tanggal Pesan</th>
                            <th class="px-5 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="order in orders.data" :key="order.id" class="transition hover:bg-slate-50">
                            <td class="px-5 py-3.5 font-medium text-slate-900">
                                {{ order.theme_category?.name ?? 'Paket undangan' }}
                            </td>
                            <td class="px-5 py-3.5 text-slate-700">{{ formatCurrency(order.price) }}</td>
                            <td class="px-5 py-3.5">
                                <span class="rounded-full px-2.5 py-1 text-xs font-medium"
                                    :class="statusLabel[order.status].class">
                                    {{ statusLabel[order.status].text }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-slate-500">
                                {{ formatDate(order.ordered_at ?? order.created_at) }}
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <Link :href="route('user.orders.show', order.id)"
                                    class="text-sm font-medium text-pink-600 hover:underline">
                                    Detail
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <Pagination :links="orders.links" />
        </div>
    </div>
</template>