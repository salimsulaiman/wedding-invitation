<script setup lang="ts">
import UserLayout from '@/Layouts/UserLayout.vue'
import { useFormatters } from '@/Composables/useFormatters'
import { Head, Link } from '@inertiajs/vue3'
import { ArrowLeft, Mail } from 'lucide-vue-next'
import { route } from 'ziggy-js'

defineOptions({ layout: UserLayout })

interface ThemeCategory {
    id: number
    name: string
    price: number
}

interface InvitationRef {
    id: number
    name: string
    status: string
}

interface Order {
    id: number
    price: number
    status: 'pending' | 'paid' | 'cancelled' | 'completed'
    notes: string | null
    order_source: string
    ordered_at: string | null
    created_at: string
    theme_category: ThemeCategory | null
    invitation: InvitationRef | null
}

const props = defineProps<{
    order: Order
}>()

const { formatCurrency, formatDateTime } = useFormatters()

const statusLabel: Record<Order['status'], { text: string; class: string }> = {
    pending: { text: 'Menunggu Konfirmasi', class: 'bg-amber-50 text-amber-700' },
    paid: { text: 'Lunas', class: 'bg-emerald-50 text-emerald-700' },
    cancelled: { text: 'Dibatalkan', class: 'bg-red-50 text-red-600' },
    completed: { text: 'Selesai', class: 'bg-emerald-50 text-emerald-700' },
}
</script>

<template>

    <Head title="Detail Pesanan" />

    <div class="mx-auto max-w-2xl space-y-6">
        <div class="flex items-center gap-3">
            <Link :href="route('user.orders.index')"
                class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-600">
                <ArrowLeft class="h-4 w-4" />
            </Link>
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Detail Pesanan</h2>
                <p class="mt-0.5 text-sm text-slate-500">Dipesan {{ formatDateTime(order.ordered_at ?? order.created_at)
                    }}</p>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-6">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-slate-500">Status Pesanan</p>
                <span class="rounded-full px-2.5 py-1 text-xs font-medium" :class="statusLabel[order.status].class">
                    {{ statusLabel[order.status].text }}
                </span>
            </div>

            <div class="mt-5 space-y-4 border-t border-slate-100 pt-5">
                <div class="flex items-center justify-between">
                    <p class="text-sm text-slate-500">Paket</p>
                    <p class="text-sm font-medium text-slate-900">{{ order.theme_category?.name ?? 'Paket undangan' }}
                    </p>
                </div>
                <div class="flex items-center justify-between">
                    <p class="text-sm text-slate-500">Harga</p>
                    <p class="text-sm font-semibold text-slate-900">{{ formatCurrency(order.price) }}</p>
                </div>
                <div class="flex items-center justify-between">
                    <p class="text-sm text-slate-500">Sumber Pesanan</p>
                    <p class="text-sm capitalize text-slate-900">{{ order.order_source }}</p>
                </div>
            </div>

            <div v-if="order.notes" class="mt-5 border-t border-slate-100 pt-5">
                <p class="text-sm font-medium text-slate-500">Catatan dari Admin</p>
                <p class="mt-1.5 text-sm text-slate-700">{{ order.notes }}</p>
            </div>
        </div>

        <div v-if="order.invitation" class="rounded-xl border border-pink-100 bg-pink-50/40 p-5">
            <div class="flex items-center gap-2 text-slate-700">
                <Mail class="h-4 w-4 text-pink-600" />
                <p class="text-sm font-medium">Undangan dari pesanan ini sudah dibuat</p>
            </div>
            <Link :href="route('user.invitations.show', order.invitation.id)"
                class="mt-3 inline-block text-sm font-medium text-pink-600 hover:underline">
                {{ order.invitation.name }} &rarr;
            </Link>
        </div>

        <div v-else
            class="rounded-xl border border-dashed border-slate-300 bg-white p-5 text-center text-sm text-slate-500">
            Undangan untuk pesanan ini belum dibuat. Silakan buat undangan baru atau hubungi admin.
        </div>
    </div>
</template>