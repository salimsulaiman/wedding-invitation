<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { useFormatters } from '@/Composables/useFormatters'
import { Head, Link, useForm, router } from '@inertiajs/vue3'
import { ArrowLeft, Package, ShoppingBag, UserCog, Trash2 } from 'lucide-vue-next'
import CustomSelect from '@/Components/General/CustomSelect.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
    user: Object,
    themeCategories: Array,
    accessList: Array,
})

const { formatDateTime } = useFormatters()

const form = useForm({
    name: props.user.name,
    username: props.user.username,
    email: props.user.email,
    phone: props.user.phone,
    password: '',
    role: props.user.role,
})

const submit = () => {
    form.put(route('admin.users.update', props.user.id))
}

const grantForm = useForm({
    theme_category_id: '',
})

const submitGrant = () => {
    if (!grantForm.theme_category_id) return

    router.post(route('admin.theme-categories.access.store', grantForm.theme_category_id), {
        user_id: props.user.id,
    }, {
        preserveScroll: true,
        onSuccess: () => (grantForm.theme_category_id = ''),
    })
}

const revokeAccess = (categoryId) => {
    if (!confirm('Cabut akses paket ini dari customer?')) return

    router.delete(route('admin.theme-categories.access.destroy', [categoryId, props.user.id]), {
        preserveScroll: true,
    })
}

const availableToGrant = () => {
    const grantedIds = props.accessList.map((a) => a.id)
    return props.themeCategories.filter((c) => !grantedIds.includes(c.id))
}
</script>

<template>

    <Head title="Edit Akun" />

    <div class="max-w-2xl space-y-6">
        <div class="flex items-center gap-3">
            <Link :href="route('admin.users.index')"
                class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-600">
                <ArrowLeft class="h-4 w-4" />
            </Link>
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Edit Akun</h2>
                <p class="mt-0.5 text-sm text-slate-500">Perbarui informasi akun {{ user.name }}.</p>
            </div>
        </div>

        <form class="space-y-5 rounded-xl border border-slate-200 bg-white p-6" @submit.prevent="submit">
            <h3 class="text-sm font-semibold text-slate-900">Informasi Akun</h3>

            <div>
                <label class="block text-sm font-medium text-slate-700">Nama Lengkap</label>
                <input v-model="form.name" type="text"
                    class="mt-1.5 block w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30" />
                <p v-if="form.errors.name" class="mt-1.5 text-sm text-red-600">{{ form.errors.name }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">Username</label>
                <input v-model="form.username" type="text"
                    class="mt-1.5 block w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30" />
                <p v-if="form.errors.username" class="mt-1.5 text-sm text-red-600">{{ form.errors.username }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">Email</label>
                <input v-model="form.email" type="email"
                    class="mt-1.5 block w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30" />
                <p v-if="form.errors.email" class="mt-1.5 text-sm text-red-600">{{ form.errors.email }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">No. Telepon</label>
                <input v-model="form.phone" type="text"
                    class="mt-1.5 block w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30" />
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">
                    Kata Sandi Baru
                    <span class="font-normal text-slate-400">(kosongkan jika tidak diubah)</span>
                </label>
                <input v-model="form.password" type="password"
                    class="mt-1.5 block w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30" />
                <p v-if="form.errors.password" class="mt-1.5 text-sm text-red-600">{{ form.errors.password }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">Role</label>
                <select v-model="form.role"
                    class="mt-1.5 block w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30">
                    <option value="user">Customer</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-5">
                <Link :href="route('admin.users.index')"
                    class="text-sm font-medium text-slate-500 hover:text-slate-700">
                    Batal
                </Link>
                <button type="submit" :disabled="form.processing"
                    class="rounded-lg bg-pink-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-pink-700 disabled:opacity-60">
                    Simpan Perubahan
                </button>
            </div>
        </form>

        <div v-if="user.role === 'user'" class="rounded-xl border border-slate-200 bg-white p-6">
            <div class="flex items-center gap-2">
                <Package class="h-4 w-4 text-pink-600" />
                <h3 class="text-sm font-semibold text-slate-900">Akses Paket Tema</h3>
            </div>

            <div class="mt-3 space-y-2">
                <div v-if="accessList.length === 0"
                    class="rounded-lg border border-dashed border-slate-200 py-6 text-center text-sm text-slate-400">
                    Belum ada paket yang diakses customer ini.
                </div>

                <div v-for="access in accessList" :key="access.id"
                    class="flex items-center justify-between rounded-lg border border-slate-200 p-3.5">
                    <div>
                        <p class="text-sm font-medium text-slate-900">{{ access.name }}</p>
                        <div class="mt-1 flex items-center gap-1.5 text-xs">
                            <span class="flex items-center gap-1 rounded-full px-2 py-0.5 font-medium"
                                :class="access.source === 'order' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700'">
                                <component :is="access.source === 'order' ? ShoppingBag : UserCog" class="h-3 w-3" />
                                {{ access.source === 'order' ? `Dari Pesanan #${access.order_id}` : 'Dibuka Manual' }}
                            </span>
                            <span class="text-slate-400">{{ formatDateTime(access.granted_at) }}</span>
                        </div>
                    </div>
                    <button class="rounded-lg p-1.5 text-slate-400 hover:bg-red-50 hover:text-red-600"
                        @click="revokeAccess(access.id)">
                        <Trash2 class="h-4 w-4" />
                    </button>
                </div>
            </div>

            <div class="mt-4 rounded-lg border border-dashed border-amber-200 bg-amber-50/60 p-4">
                <p class="text-xs font-medium text-amber-800">Buka akses secara manual (khusus promo/koreksi)</p>
                <p class="mt-1 text-xs text-amber-700">
                    Untuk pembelian normal, buka akses lewat halaman Pesanan setelah status "Lunas".
                </p>

                <div v-if="availableToGrant().length > 0" class="mt-3 flex gap-2">
                    <CustomSelect v-model="grantForm.theme_category_id" :options="availableToGrant()" value-key="id"
                        label-key="name" placeholder="Pilih paket" :include-all-option="false" class="flex-1" />
                    <button type="button" :disabled="!grantForm.theme_category_id"
                        class="rounded-lg bg-amber-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-amber-700 disabled:opacity-50"
                        @click="submitGrant">
                        Buka
                    </button>
                </div>
                <p v-else class="mt-3 text-xs text-amber-600">Semua paket yang tersedia sudah diakses customer ini.</p>
            </div>
        </div>
    </div>
</template>