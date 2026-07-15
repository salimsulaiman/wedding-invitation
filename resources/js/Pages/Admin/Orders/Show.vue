<script setup>
import { useFormatters } from '@/composables/useFormatters'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ArrowLeft, Mail } from 'lucide-vue-next'

defineOptions({ layout: AdminLayout })

const props = defineProps({
    order: Object,
})

const { formatCurrency, formatDateTime } = useFormatters()

const form = useForm({
    status: props.order.status,
    price: props.order.price,
    notes: props.order.notes ?? '',
})

const submit = () => {
    form.patch(route('admin.orders.update', props.order.id), {
        preserveScroll: true,
    })
}
</script>

<template>
    <Head title="Detail Pesanan" />

    <div class="max-w-3xl space-y-6">
        <div class="flex items-center gap-3">
            <Link :href="route('admin.orders.index')" class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-600">
                <ArrowLeft class="h-4 w-4" />
            </Link>
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Detail Pesanan</h2>
                <p class="mt-0.5 text-sm text-slate-500">Dicatat oleh {{ order.handled_by?.name ?? '-' }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="space-y-6 lg:col-span-1">
                <div class="rounded-xl border border-slate-200 bg-white p-5">
                    <h3 class="text-sm font-semibold text-slate-900">Customer</h3>
                    <p class="mt-3 text-sm font-medium text-slate-800">{{ order.user?.name }}</p>
                    <p class="text-sm text-slate-500">{{ order.user?.email }}</p>
                    <p v-if="order.user?.phone" class="mt-1 text-sm text-slate-500">{{ order.user.phone }}</p>
                </div>

                <div class="rounded-xl border border-slate-200 bg-white p-5">
                    <h3 class="text-sm font-semibold text-slate-900">Paket Dipesan</h3>
                    <p class="mt-3 text-sm text-slate-700">{{ order.theme_category?.name ?? 'Belum ditentukan' }}</p>
                </div>

                <div class="rounded-xl border border-slate-200 bg-white p-5">
                    <h3 class="text-sm font-semibold text-slate-900">Undangan Terkait</h3>
                    <div v-if="order.invitation" class="mt-3 flex items-center gap-2 text-sm text-slate-700">
                        <Mail class="h-4 w-4 text-pink-500" />
                        {{ order.invitation.name }}
                    </div>
                    <p v-else class="mt-3 text-sm text-slate-400">Undangan belum dibuat dari pesanan ini.</p>
                </div>
            </div>

            <div class="lg:col-span-2">
                <form class="space-y-5 rounded-xl border border-slate-200 bg-white p-6" @submit.prevent="submit">
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Status Pesanan</label>
                        <select
                            v-model="form.status"
                            class="mt-1.5 block w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                        >
                            <option value="pending">Menunggu</option>
                            <option value="paid">Lunas</option>
                            <option value="completed">Selesai</option>
                            <option value="cancelled">Batal</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700">Harga</label>
                        <input
                            v-model.number="form.price"
                            type="number"
                            min="0"
                            class="mt-1.5 block w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                        />
                        <p v-if="form.errors.price" class="mt-1.5 text-sm text-red-600">{{ form.errors.price }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700">Catatan</label>
                        <textarea
                            v-model="form.notes"
                            rows="4"
                            class="mt-1.5 block w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                        ></textarea>
                    </div>

                    <div class="flex items-center justify-between border-t border-slate-100 pt-5 text-xs text-slate-400">
                        <span>Dicatat: {{ formatDateTime(order.created_at) }}</span>
                        <span>Terakhir diubah: {{ formatDateTime(order.updated_at) }}</span>
                    </div>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full rounded-lg bg-pink-600 py-2.5 text-sm font-semibold text-white transition hover:bg-pink-700 disabled:opacity-60"
                    >
                        Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>