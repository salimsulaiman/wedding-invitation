<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Pagination from '@/Components/Admin/Pagination.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import { Plus, Search, Pencil, Trash2, Power } from 'lucide-vue-next'
import debounce from 'lodash/debounce'
import { useFormatters } from '@/composables/useFormatters'

defineOptions({ layout: AdminLayout })

const props = defineProps({
    users: Object,
    filters: Object,
})

const { formatDate } = useFormatters()

const search = ref(props.filters.search ?? '')
const role = ref(props.filters.role ?? '')

watch([search, role], debounce(([searchValue, roleValue]) => {
    router.get(route('admin.users.index'), { search: searchValue, role: roleValue }, {
        preserveState: true,
        replace: true,
    })
}, 350))

const toggleActive = (user) => {
    router.patch(route('admin.users.toggle-active', user.id), {}, { preserveScroll: true })
}

const destroy = (user) => {
    if (confirm(`Hapus akun "${user.name}"? Tindakan ini tidak bisa dibatalkan.`)) {
        router.delete(route('admin.users.destroy', user.id), { preserveScroll: true })
    }
}
</script>

<template>
    <Head title="Pengguna" />

    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Pengguna</h2>
                <p class="mt-1 text-sm text-slate-500">Kelola akun admin dan customer.</p>
            </div>
            <Link
                :href="route('admin.users.create')"
                class="flex items-center gap-1.5 rounded-lg bg-pink-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-pink-700"
            >
                <Plus class="h-4 w-4" />
                Tambah Akun
            </Link>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white">
            <!-- Filter -->
            <div class="flex flex-col gap-3 border-b border-slate-200 p-4 sm:flex-row sm:items-center">
                <div class="relative flex-1">
                    <Search class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Cari nama, username, atau email..."
                        class="w-full rounded-lg border border-slate-300 py-2 pl-9 pr-3 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                    />
                </div>
                <select
                    v-model="role"
                    class="rounded-lg border border-slate-300 py-2 px-3 text-sm text-slate-700 focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                >
                    <option value="">Semua Role</option>
                    <option value="admin">Admin</option>
                    <option value="user">Customer</option>
                </select>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 text-xs font-medium uppercase tracking-wide text-slate-400">
                            <th class="px-5 py-3">Nama</th>
                            <th class="px-5 py-3">Username</th>
                            <th class="px-5 py-3">Role</th>
                            <th class="px-5 py-3">Undangan</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3">Bergabung</th>
                            <th class="px-5 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-if="users.data.length === 0">
                            <td colspan="6" class="px-5 py-10 text-center text-sm text-slate-400">
                                Tidak ada data pengguna.
                            </td>
                        </tr>
                        <tr v-for="user in users.data" :key="user.id" class="transition hover:bg-slate-50">
                            <td class="px-5 py-4">
                                <div>
                                    <p class="font-medium text-slate-900">
                                        {{ user.name }}
                                    </p>

                                    <p class="text-xs text-slate-500">
                                        {{ user.email || '-' }}
                                    </p>
                                </div>
                            </td>

                            <td class="px-5 py-4">
                                <span
                                    class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700"
                                >
                                    {{ user.username }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                <span
                                    class="rounded-full px-2.5 py-1 text-xs font-medium"
                                    :class="user.role === 'admin' ? 'bg-pink-50 text-pink-700' : 'bg-slate-100 text-slate-600'"
                                >
                                    {{ user.role === 'admin' ? 'Admin' : 'Customer' }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-slate-600">{{ user.invitations_count }}</td>
                            <td class="px-5 py-3.5">
                                <span
                                    class="rounded-full px-2.5 py-1 text-xs font-medium"
                                    :class="user.is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-600'"
                                >
                                    {{ user.is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-slate-500">{{ formatDate(user.created_at) }}</td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center justify-end gap-1">
                                    <button
                                        title="Aktifkan/Nonaktifkan"
                                        class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"
                                        @click="toggleActive(user)"
                                    >
                                        <Power class="h-4 w-4" />
                                    </button>
                                    <Link
                                        :href="route('admin.users.edit', user.id)"
                                        title="Edit"
                                        class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"
                                    >
                                        <Pencil class="h-4 w-4" />
                                    </Link>
                                    <button
                                        title="Hapus"
                                        class="rounded-lg p-2 text-slate-400 transition hover:bg-red-50 hover:text-red-600"
                                        @click="destroy(user)"
                                    >
                                        <Trash2 class="h-4 w-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <Pagination :links="users.links" />
        </div>
    </div>
</template>