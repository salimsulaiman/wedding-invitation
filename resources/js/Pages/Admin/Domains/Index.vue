<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Pagination from '@/Components/Admin/Pagination.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import { Search, Power, ExternalLink } from 'lucide-vue-next'
import debounce from 'lodash/debounce'
import { useFormatters } from '@/composables/useFormatters'

defineOptions({ layout: AdminLayout })

const props = defineProps({
    domains: Object,
    filters: Object,
})

const { formatDate } = useFormatters()

const search = ref(props.filters.search ?? '')
const status = ref(props.filters.status ?? '')

watch([search, status], debounce(([searchValue, statusValue]) => {
    router.get(route('admin.domains.index'), { search: searchValue, status: statusValue }, {
        preserveState: true,
        replace: true,
    })
}, 350))

const toggle = (domain) => {
    const action = domain.is_active ? 'menonaktifkan' : 'mengaktifkan'
    if (confirm(`Yakin ingin ${action} domain "${domain.name}"?`)) {
        router.patch(route('admin.domains.toggle', domain.id), {}, { preserveScroll: true })
    }
}
</script>

<template>
    <Head title="Domain" />

    <div class="space-y-6">
        <div>
            <h2 class="text-lg font-semibold text-slate-900">Domain</h2>
            <p class="mt-1 text-sm text-slate-500">Kelola domain custom yang dipakai tiap undangan.</p>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white">
            <div class="flex flex-col gap-3 border-b border-slate-200 p-4 sm:flex-row sm:items-center">
                <div class="relative flex-1">
                    <Search class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Cari nama domain..."
                        class="w-full rounded-lg border border-slate-300 py-2 pl-9 pr-3 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                    />
                </div>
                <select
                    v-model="status"
                    class="rounded-lg border border-slate-300 py-2 px-3 text-sm text-slate-700 focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                >
                    <option value="">Semua Status</option>
                    <option value="active">Aktif</option>
                    <option value="inactive">Nonaktif</option>
                </select>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 text-xs font-medium uppercase tracking-wide text-slate-400">
                            <th class="px-5 py-3">Domain</th>
                            <th class="px-5 py-3">Undangan</th>
                            <th class="px-5 py-3">Customer</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3">Diaktifkan</th>
                            <th class="px-5 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-if="domains.data.length === 0">
                            <td colspan="6" class="px-5 py-10 text-center text-sm text-slate-400">
                                Belum ada domain terdaftar.
                            </td>
                        </tr>
                        <tr v-for="domain in domains.data" :key="domain.id" class="transition hover:bg-slate-50">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-1.5 font-medium text-slate-900">
                                    <a
                                        v-if="domain.is_active"
                                        :href="`/${domain.name}`"
                                        target="_blank"
                                        class="text-slate-400 hover:text-pink-600 flex items-center gap-1"
                                    >
                                        {{ domain.name }}<ExternalLink class="h-3.5 w-3.5" />
                                    </a>
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                <Link
                                    v-if="domain.invitation"
                                    :href="route('admin.invitations.show', domain.invitation.id)"
                                    class="text-slate-700 hover:text-pink-600"
                                >
                                    {{ domain.invitation.name }}
                                </Link>
                                <span v-else class="text-slate-400">-</span>
                            </td>
                            <td class="px-5 py-3.5 text-slate-600">{{ domain.invitation?.user?.name ?? '-' }}</td>
                            <td class="px-5 py-3.5">
                                <span
                                    class="rounded-full px-2.5 py-1 text-xs font-medium"
                                    :class="domain.is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-600'"
                                >
                                    {{ domain.is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-slate-500">
                                {{ domain.activated_at ? formatDate(domain.activated_at) : '-' }}
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <button
                                    class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"
                                    :title="domain.is_active ? 'Nonaktifkan' : 'Aktifkan'"
                                    @click="toggle(domain)"
                                >
                                    <Power class="h-4 w-4" />
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <Pagination :links="domains.links" />
        </div>
    </div>
</template>