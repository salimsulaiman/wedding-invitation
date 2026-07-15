<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Pagination from '@/Components/Admin/Pagination.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import { Plus, Search, Power, Trash2, Users, MessageSquare } from 'lucide-vue-next'
import debounce from 'lodash/debounce'
import { useFormatters } from '@/composables/useFormatters'

defineOptions({ layout: AdminLayout })

const props = defineProps({
    invitations: Object,
    filters: Object,
})

const { formatDate } = useFormatters()

const statusLabel = {
    draft: { text: 'Draf', class: 'bg-slate-100 text-slate-600' },
    published: { text: 'Terbit', class: 'bg-emerald-50 text-emerald-700' },
    inactive: { text: 'Nonaktif', class: 'bg-red-50 text-red-600' },
}

const search = ref(props.filters.search ?? '')
const status = ref(props.filters.status ?? '')

watch([search, status], debounce(([searchValue, statusValue]) => {
    router.get(route('admin.invitations.index'), { search: searchValue, status: statusValue }, {
        preserveState: true,
        replace: true,
    })
}, 350))

const toggleActive = (invitation) => {
    router.patch(route('admin.invitations.toggle-active', invitation.id), {}, { preserveScroll: true })
}

const destroy = (invitation) => {
    if (confirm(`Hapus undangan "${invitation.name}"? Tindakan ini tidak bisa dibatalkan.`)) {
        router.delete(route('admin.invitations.destroy', invitation.id), { preserveScroll: true })
    }
}
</script>

<template>
    <Head title="Undangan" />

    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Undangan</h2>
                <p class="mt-1 text-sm text-slate-500">Semua undangan yang dibuat customer maupun admin.</p>
            </div>
            <Link
                :href="route('admin.invitations.create')"
                class="flex items-center gap-1.5 rounded-lg bg-pink-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-pink-700"
            >
                <Plus class="h-4 w-4" />
                Buatkan Undangan
            </Link>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white">
            <div class="flex flex-col gap-3 border-b border-slate-200 p-4 sm:flex-row sm:items-center">
                <div class="relative flex-1">
                    <Search class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Cari nama undangan atau customer..."
                        class="w-full rounded-lg border border-slate-300 py-2 pl-9 pr-3 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                    />
                </div>
                <select
                    v-model="status"
                    class="rounded-lg border border-slate-300 py-2 px-3 text-sm text-slate-700 focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                >
                    <option value="">Semua Status</option>
                    <option value="draft">Draf</option>
                    <option value="published">Terbit</option>
                    <option value="inactive">Nonaktif</option>
                </select>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 text-xs font-medium uppercase tracking-wide text-slate-400">
                            <th class="px-5 py-3">Nama Undangan</th>
                            <th class="px-5 py-3">Customer</th>
                            <th class="px-5 py-3">Tema</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3">Aktivitas</th>
                            <th class="px-5 py-3">Dibuat</th>
                            <th class="px-5 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-if="invitations.data.length === 0">
                            <td colspan="7" class="px-5 py-10 text-center text-sm text-slate-400">
                                Belum ada undangan.
                            </td>
                        </tr>
                        <tr v-for="invitation in invitations.data" :key="invitation.id" class="transition hover:bg-slate-50">
                            <td class="px-5 py-3.5">
                                <Link
                                    :href="route('admin.invitations.show', invitation.id)"
                                    class="font-medium text-slate-900 hover:text-pink-600"
                                >
                                    {{ invitation.name }}
                                </Link>
                            </td>
                            <td class="px-5 py-3.5 text-slate-600">{{ invitation.user?.name }}</td>
                            <td class="px-5 py-3.5">
                                <p class="text-slate-700">{{ invitation.theme?.name ?? '-' }}</p>
                                <p v-if="invitation.theme?.category" class="text-xs text-slate-400">{{ invitation.theme.category.name }}</p>
                            </td>
                            <td class="px-5 py-3.5">
                                <span
                                    class="rounded-full px-2.5 py-1 text-xs font-medium"
                                    :class="statusLabel[invitation.status]?.class"
                                >
                                    {{ statusLabel[invitation.status]?.text }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3 text-xs text-slate-500">
                                    <span class="flex items-center gap-1"><Users class="h-3.5 w-3.5" />{{ invitation.guests_count }}</span>
                                    <span class="flex items-center gap-1"><MessageSquare class="h-3.5 w-3.5" />{{ invitation.wishes_count }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-slate-500">{{ formatDate(invitation.created_at) }}</td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center justify-end gap-1">
                                    <button
                                        title="Aktifkan/Nonaktifkan"
                                        class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"
                                        @click="toggleActive(invitation)"
                                    >
                                        <Power class="h-4 w-4" />
                                    </button>
                                    <button
                                        title="Hapus"
                                        class="rounded-lg p-2 text-slate-400 transition hover:bg-red-50 hover:text-red-600"
                                        @click="destroy(invitation)"
                                    >
                                        <Trash2 class="h-4 w-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <Pagination :links="invitations.links" />
        </div>
    </div>
</template>