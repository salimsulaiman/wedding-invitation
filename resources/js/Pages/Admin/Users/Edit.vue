<script setup>
import { useFormatters } from '@/composables/useFormatters'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ArrowLeft, Package } from 'lucide-vue-next'

defineOptions({ layout: AdminLayout })

const props = defineProps({
    user: Object,
    themeCategories: Array,
    accessThemeCategoryIds: Array,
})

const { formatCurrency } = useFormatters()

const form = useForm({
    name: props.user.name,
    username: props.user.username,
    email: props.user.email,
    phone: props.user.phone,
    password: '',
    role: props.user.role,
    theme_category_ids: [...props.accessThemeCategoryIds],
})

const toggleCategory = (id) => {
    const index = form.theme_category_ids.indexOf(id)
    if (index === -1) {
        form.theme_category_ids.push(id)
    } else {
        form.theme_category_ids.splice(index, 1)
    }
}

const submit = () => {
    form.put(route('admin.users.update', props.user.id))
}
</script>

<template>
    <Head title="Edit Akun" />

    <div class="space-y-6">
        <div class="flex items-center gap-3">
            <Link :href="route('admin.users.index')" class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-600">
                <ArrowLeft class="h-4 w-4" />
            </Link>
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Edit Akun</h2>
                <p class="mt-0.5 text-sm text-slate-500">Perbarui informasi akun {{ user.name }}.</p>
            </div>
        </div>

        <form class="grid grid-cols-1 gap-6 lg:grid-cols-2" @submit.prevent="submit">
            <!-- Kiri: Informasi umum -->
            <div class="space-y-5 rounded-xl border border-slate-200 bg-white p-6">
                <h3 class="text-sm font-semibold text-slate-900">Informasi Akun</h3>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Nama Lengkap</label>
                    <input
                        v-model="form.name"
                        type="text"
                        class="mt-1.5 block w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                    />
                    <p v-if="form.errors.name" class="mt-1.5 text-sm text-red-600">{{ form.errors.name }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Username</label>
                    <input
                        v-model="form.username"
                        type="text"
                        class="mt-1.5 block w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                    />
                    <p v-if="form.errors.username" class="mt-1.5 text-sm text-red-600">
                        {{ form.errors.username }}
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Email (Opsional)</label>
                    <input
                        v-model="form.email"
                        type="email"
                        class="mt-1.5 block w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                    />
                    <p v-if="form.errors.email" class="mt-1.5 text-sm text-red-600">{{ form.errors.email }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">No. Telepon</label>
                    <input
                        v-model="form.phone"
                        type="text"
                        class="mt-1.5 block w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                    />
                    <p v-if="form.errors.phone" class="mt-1.5 text-sm text-red-600">{{ form.errors.phone }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">
                        Kata Sandi Baru
                        <span class="font-normal text-slate-400">(kosongkan jika tidak diubah)</span>
                    </label>
                    <input
                        v-model="form.password"
                        type="password"
                        class="mt-1.5 block w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                    />
                    <p v-if="form.errors.password" class="mt-1.5 text-sm text-red-600">{{ form.errors.password }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Role</label>
                    <select
                        v-model="form.role"
                        class="mt-1.5 block w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                    >
                        <option value="user">Customer</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
            </div>

            <!-- Kanan: Akses paket tema -->
            <div class="rounded-xl border border-slate-200 bg-white p-6">
                <div class="flex items-center gap-2">
                    <Package class="h-4 w-4 text-pink-600" />
                    <h3 class="text-sm font-semibold text-slate-900">Akses Paket Tema</h3>
                </div>

                <p v-if="form.role !== 'user'" class="mt-3 text-sm text-slate-400">
                    Akses paket hanya berlaku untuk akun customer. Mengubah role ke Admin akan mencabut semua akses paket akun ini.
                </p>

                <template v-else>
                    <p class="mt-1 text-sm text-slate-500">
                        Centang paket yang boleh diakses customer ini di builder.
                    </p>

                    <div class="mt-4 space-y-2">
                        <div v-if="themeCategories.length === 0" class="rounded-lg border border-dashed border-slate-200 py-6 text-center text-sm text-slate-400">
                            Belum ada paket tema tersedia.
                        </div>

                        <label
                            v-for="category in themeCategories"
                            :key="category.id"
                            class="flex cursor-pointer items-start gap-3 rounded-lg border border-slate-200 p-3.5 transition"
                            :class="form.theme_category_ids.includes(category.id) ? 'border-pink-300 bg-pink-50' : 'hover:bg-slate-50'"
                        >
                            <input
                                type="checkbox"
                                class="mt-0.5 h-4 w-4 rounded border-slate-300 text-pink-600 focus:ring-2 focus:ring-pink-500/30"
                                :checked="form.theme_category_ids.includes(category.id)"
                                @change="toggleCategory(category.id)"
                            />
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-medium text-slate-900">{{ category.name }}</p>
                                    <p class="text-sm font-semibold text-pink-600">{{ formatCurrency(category.price) }}</p>
                                </div>
                                <p v-if="category.description" class="mt-0.5 text-xs text-slate-500">
                                    {{ category.description }}
                                </p>
                            </div>
                        </label>
                    </div>
                </template>
            </div>

            <!-- Aksi -->
            <div class="flex items-center justify-end gap-3 lg:col-span-2">
                <Link :href="route('admin.users.index')" class="text-sm font-medium text-slate-500 hover:text-slate-700">
                    Batal
                </Link>
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="rounded-lg bg-pink-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-pink-700 disabled:opacity-60"
                >
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</template>