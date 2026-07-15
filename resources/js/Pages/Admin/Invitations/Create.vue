<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ArrowLeft, AlertCircle } from 'lucide-vue-next'
import { computed } from 'vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
    customers: Array,
    themes: Array,
})

const form = useForm({
    user_id: '',
    theme_id: '',
    name: '',
    max_guest: '',
    expired_at: '',
})

const selectedCustomer = computed(() =>
    props.customers.find((customer) => customer.id === form.user_id)
)

const availableThemes = computed(() => {
    if (! selectedCustomer.value) return []
    return props.themes.filter((theme) =>
        selectedCustomer.value.accessible_theme_category_ids.includes(theme.theme_category_id)
    )
})

const onCustomerChange = () => {
    form.theme_id = ''
}

const submit = () => {
    form.post(route('admin.invitations.store'))
}
</script>

<template>
    <Head title="Buatkan Undangan" />

    <div class="max-w-xl space-y-6">
        <div class="flex items-center gap-3">
            <Link :href="route('admin.invitations.index')" class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-600">
                <ArrowLeft class="h-4 w-4" />
            </Link>
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Buatkan Undangan</h2>
                <p class="mt-0.5 text-sm text-slate-500">Isi info dasar, sisanya bisa dilengkapi di builder.</p>
            </div>
        </div>

        <form class="space-y-5 rounded-xl border border-slate-200 bg-white p-6" @submit.prevent="submit">
            <div>
                <label class="block text-sm font-medium text-slate-700">Customer</label>
                <select
                    v-model="form.user_id"
                    class="mt-1.5 block w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                    @change="onCustomerChange"
                >
                    <option value="" disabled>Pilih customer</option>
                    <option v-for="customer in customers" :key="customer.id" :value="customer.id">
                        {{ customer.name }} ({{ customer.email }})
                    </option>
                </select>
                <p v-if="form.errors.user_id" class="mt-1.5 text-sm text-red-600">{{ form.errors.user_id }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">Nama Acara Undangan</label>
                <input
                    v-model="form.name"
                    type="text"
                    placeholder="Cth: The Wedding of Andi & Siti"
                    class="mt-1.5 block w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                />
                <p v-if="form.errors.name" class="mt-1.5 text-sm text-red-600">{{ form.errors.name }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">Tema</label>

                <div v-if="!selectedCustomer" class="mt-1.5 flex items-center gap-2 rounded-lg bg-slate-50 px-3.5 py-2.5 text-sm text-slate-400">
                    Pilih customer terlebih dahulu.
                </div>

                <div v-else-if="availableThemes.length === 0" class="mt-1.5 flex items-start gap-2 rounded-lg bg-amber-50 px-3.5 py-2.5 text-sm text-amber-700">
                    <AlertCircle class="mt-0.5 h-4 w-4 shrink-0" />
                    Customer ini belum memiliki akses ke paket tema manapun. Buka akses paket dulu di halaman Edit Akun.
                </div>

                <select
                    v-else
                    v-model="form.theme_id"
                    class="mt-1.5 block w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                >
                    <option value="">Belum pilih tema (bisa diatur di builder)</option>
                    <option v-for="theme in availableThemes" :key="theme.id" :value="theme.id">
                        {{ theme.name }}
                    </option>
                </select>
                <p v-if="form.errors.theme_id" class="mt-1.5 text-sm text-red-600">{{ form.errors.theme_id }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">
                    Maksimal Tamu
                    <span class="font-normal text-slate-400">(kosongkan untuk tanpa batasan)</span>
                </label>
                <input
                    v-model="form.max_guest"
                    type="number"
                    min="1"
                    class="mt-1.5 block w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                />
                <p v-if="form.errors.max_guest" class="mt-1.5 text-sm text-red-600">{{ form.errors.max_guest }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">
                    Tanggal Kedaluwarsa
                    <span class="font-normal text-slate-400">(kosongkan untuk tanpa expired)</span>
                </label>
                <input
                    v-model="form.expired_at"
                    type="date"
                    class="mt-1.5 block w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                />
                <p v-if="form.errors.expired_at" class="mt-1.5 text-sm text-red-600">{{ form.errors.expired_at }}</p>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-5">
                <Link :href="route('admin.invitations.index')" class="text-sm font-medium text-slate-500 hover:text-slate-700">
                    Batal
                </Link>
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="rounded-lg bg-pink-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-pink-700 disabled:opacity-60"
                >
                    Buat & Lanjut ke Builder
                </button>
            </div>
        </form>
    </div>
</template>