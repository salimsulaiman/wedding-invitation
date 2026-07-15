<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Pagination from '@/Components/Admin/Pagination.vue'
import { Head, Link, router, useForm } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import { Plus, Search, X } from 'lucide-vue-next'
import debounce from 'lodash/debounce'
import { useFormatters } from '@/composables/useFormatters'

defineOptions({ layout: AdminLayout })

const props = defineProps({
    orders: Object,
    customers: Array,
    themeCategories: Array,
    filters: Object,
})

const { formatCurrency, formatDate } = useFormatters()

const statusLabel = {
    pending: { text: 'Menunggu', class: 'bg-amber-50 text-amber-700' },
    paid: { text: 'Lunas', class: 'bg-emerald-50 text-emerald-700' },
    cancelled: { text: 'Batal', class: 'bg-red-50 text-red-600' },
    completed: { text: 'Selesai', class: 'bg-emerald-50 text-emerald-700' },
}

const search = ref(props.filters.search ?? '')
const status = ref(props.filters.status ?? '')

watch([search, status], debounce(([searchValue, statusValue]) => {
    router.get(route('admin.orders.index'), { search: searchValue, status: statusValue }, {
        preserveState: true,
        replace: true,
    })
}, 350))

const createModalOpen = ref(false)

const form = useForm({
    user_id: '',
    theme_category_id: '',
    price: 0,
    notes: '',
    ordered_at: new Date().toISOString().slice(0, 10),
})

const onCategoryChange = () => {
    const category = props.themeCategories.find((c) => c.id === form.theme_category_id)
    if (category) {
        form.price = category.price
    }
}

const submit = () => {
    form.post(route('admin.orders.store'), {
        preserveScroll: true,
        onSuccess: () => {
            createModalOpen.value = false
            form.reset()
        },
    })
}
</script>

<template>
    <Head title="Pesanan" />

    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Riwayat Pesanan</h2>
                <p class="mt-1 text-sm text-slate-500">Pesanan yang masuk melalui WhatsApp, dicatat manual di sini.</p>
            </div>
            <button
                class="flex items-center gap-1.5 rounded-lg bg-pink-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-pink-700"
                @click="createModalOpen = true"
            >
                <Plus class="h-4 w-4" />
                Catat Pesanan
            </button>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white">
            <div class="flex flex-col gap-3 border-b border-slate-200 p-4 sm:flex-row sm:items-center">
                <div class="relative flex-1">
                    <Search class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Cari nama customer..."
                        class="w-full rounded-lg border border-slate-300 py-2 pl-9 pr-3 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                    />
                </div>
                <select
                    v-model="status"
                    class="rounded-lg border border-slate-300 py-2 px-3 text-sm text-slate-700 focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                >
                    <option value="">Semua Status</option>
                    <option value="pending">Menunggu</option>
                    <option value="paid">Lunas</option>
                    <option value="completed">Selesai</option>
                    <option value="cancelled">Batal</option>
                </select>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 text-xs font-medium uppercase tracking-wide text-slate-400">
                            <th class="px-5 py-3">Customer</th>
                            <th class="px-5 py-3">Paket</th>
                            <th class="px-5 py-3">Harga</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3">Tanggal Pesan</th>
                            <th class="px-5 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-if="orders.data.length === 0">
                            <td colspan="6" class="px-5 py-10 text-center text-sm text-slate-400">
                                Belum ada pesanan tercatat.
                            </td>
                        </tr>
                        <tr v-for="order in orders.data" :key="order.id" class="transition hover:bg-slate-50">
                            <td class="px-5 py-3.5">
                                <p class="font-medium text-slate-900">{{ order.user?.name }}</p>
                                <p class="text-xs text-slate-500">{{ order.user?.email }}</p>
                            </td>
                            <td class="px-5 py-3.5 text-slate-600">{{ order.theme_category?.name ?? '-' }}</td>
                            <td class="px-5 py-3.5 font-medium text-slate-900">{{ formatCurrency(order.price) }}</td>
                            <td class="px-5 py-3.5">
                                <span
                                    class="rounded-full px-2.5 py-1 text-xs font-medium"
                                    :class="statusLabel[order.status]?.class"
                                >
                                    {{ statusLabel[order.status]?.text }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-slate-500">{{ formatDate(order.ordered_at) }}</td>
                            <td class="px-5 py-3.5 text-right">
                                <Link
                                    :href="route('admin.orders.show', order.id)"
                                    class="text-sm font-medium text-pink-600 hover:underline"
                                >
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

    <div v-if="createModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 p-4">
        <div class="max-h-[85vh] w-full max-w-md overflow-y-auto rounded-xl bg-white">
            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                <h3 class="text-sm font-semibold text-slate-900">Catat Pesanan Baru</h3>
                <button class="text-slate-400 hover:text-slate-600" @click="createModalOpen = false">
                    <X class="h-4.5 w-4.5" />
                </button>
            </div>

            <form class="space-y-4 p-5" @submit.prevent="submit">
                <div>
                    <label class="block text-xs font-medium text-slate-600">Customer</label>
                    <select
                        v-model="form.user_id"
                        class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                    >
                        <option value="" disabled>Pilih customer</option>
                        <option v-for="customer in customers" :key="customer.id" :value="customer.id">
                            {{ customer.name }} ({{ customer.email }})
                        </option>
                    </select>
                    <p v-if="form.errors.user_id" class="mt-1 text-xs text-red-600">{{ form.errors.user_id }}</p>
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600">Paket</label>
                    <select
                        v-model="form.theme_category_id"
                        class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                        @change="onCategoryChange"
                    >
                        <option value="">Belum ditentukan</option>
                        <option v-for="category in themeCategories" :key="category.id" :value="category.id">
                            {{ category.name }}
                        </option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600">Harga</label>
                    <input
                        v-model.number="form.price"
                        type="number"
                        min="0"
                        class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                    />
                    <p v-if="form.errors.price" class="mt-1 text-xs text-red-600">{{ form.errors.price }}</p>
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600">Tanggal Pesan</label>
                    <input
                        v-model="form.ordered_at"
                        type="date"
                        class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                    />
                    <p v-if="form.errors.ordered_at" class="mt-1 text-xs text-red-600">{{ form.errors.ordered_at }}</p>
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600">Catatan (opsional)</label>
                    <textarea
                        v-model="form.notes"
                        rows="2"
                        placeholder="Cth: pesan via WA, minta tema custom warna emas"
                        class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                    ></textarea>
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full rounded-lg bg-pink-600 py-2.5 text-sm font-semibold text-white transition hover:bg-pink-700 disabled:opacity-60"
                >
                    Simpan Pesanan
                </button>
            </form>
        </div>
    </div>
</template>