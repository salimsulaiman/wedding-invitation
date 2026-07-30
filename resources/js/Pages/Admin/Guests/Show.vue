<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Pagination from '@/Components/Admin/Pagination.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, computed, watch } from 'vue'
import { ArrowLeft, Search, Users, MessageSquare } from 'lucide-vue-next'
import { route } from 'ziggy-js'
import debounce from 'lodash/debounce'
import { useFormatters } from '@/Composables/useFormatters'
import CustomSelect from '@/Components/General/CustomSelect.vue'
 
defineOptions({ layout: AdminLayout })
 
interface Invitation {
    id: number
    name: string
}
 
interface Guest {
    id: number
    name: string
    phone: string | null
    group: string | null
    quota: number
    is_sent: boolean
    opened_at: string | null
}
 
type Attendance = 'hadir' | 'tidak_hadir' | 'ragu'
 
interface Wish {
    id: number
    name: string
    message: string
    attendance: Attendance | null
    created_at: string
}
 
interface PaginationLink {
    url: string | null
    label: string
    active: boolean
}
 
interface PaginationData<T> {
    data: T[]
    links: PaginationLink[]
    current_page: number
    last_page: number
    per_page: number
    total: number
}
 
interface Filters {
    search?: string
    is_sent?: string
    attendance?: string
}
 
const attendanceLabel = {
    hadir: {
        text: 'Hadir',
        class: 'bg-emerald-50 text-emerald-700',
    },
    tidak_hadir: {
        text: 'Tidak Hadir',
        class: 'bg-red-50 text-red-600',
    },
    ragu: {
        text: 'Ragu-ragu',
        class: 'bg-amber-50 text-amber-700',
    },
} as const
 
const props = defineProps<{
    invitation: Invitation
    guests: PaginationData<Guest>
    wishes: PaginationData<Wish>
    filters: Filters
    activeTab: 'guests' | 'wishes'
}>()
 
const { formatDate, formatDateTime } = useFormatters()
 
const tab = ref<'guests' | 'wishes'>(props.activeTab)
const search = ref<string>(props.filters.search ?? '')
const isSent = ref<string>(props.filters.is_sent ?? '')
const attendance = ref<string>(props.filters.attendance ?? '')
 
const guestStatusOptions = [
    { value: '1', label: 'Terkirim' },
    { value: '0', label: 'Belum Kirim' },
]
 
const wishAttendanceOptions = [
    { value: 'hadir', label: 'Hadir' },
    { value: 'tidak_hadir', label: 'Tidak Hadir' },
]
 
// Opsi & label filter menyesuaikan tab yang sedang aktif
const statusOptions = computed(() => (tab.value === 'guests' ? guestStatusOptions : wishAttendanceOptions))
const statusAllLabel = computed(() => (tab.value === 'guests' ? 'Semua Status' : 'Semua Kehadiran'))
 
// Satu select dipakai untuk dua tab, disimpan ke ref yang berbeda (is_sent / attendance)
// biar filter guests & wishes tidak saling menimpa saat pindah tab
const statusValue = computed<string>({
    get: () => (tab.value === 'guests' ? isSent.value : attendance.value),
    set: (value) => {
        if (tab.value === 'guests') {
            isSent.value = value
        } else {
            attendance.value = value
        }
    },
})
 
const applyFilters = (): void => {
    router.get(
        route('admin.guests.show', props.invitation.id),
        {
            tab: tab.value,
            search: search.value,
            is_sent: isSent.value,
            attendance: attendance.value,
        },
        {
            preserveState: true,
            replace: true,
        },
    )
}
 
watch([search, isSent, attendance], debounce(applyFilters, 350))
 
const switchTab = (value: 'guests' | 'wishes'): void => {
    tab.value = value
    applyFilters()
}
</script>

<template>
    <Head :title="`Tamu & Ucapan - ${invitation.name}`" />

    <div class="space-y-6">
        <div class="flex items-center gap-3">
            <Link
                :href="route('admin.guests.index')"
                class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-600"
            >
                <ArrowLeft class="h-4 w-4" />
            </Link>

            <div>
                <h2 class="text-lg font-semibold text-slate-900">
                    Daftar Tamu - {{ invitation.name }}
                </h2>

                <p class="mt-0.5 text-sm text-slate-500">
                    Rekap tamu dan ucapan untuk undangan ini.
                </p>
            </div>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white">
            <!-- Tabs -->
            <div class="flex border-b border-slate-200 px-4">
                <button
                    class="flex items-center gap-1.5 border-b-2 px-4 py-3.5 text-sm font-medium transition"
                    :class="tab === 'guests' ? 'border-pink-600 text-pink-600' : 'border-transparent text-slate-500 hover:text-slate-700'"
                    @click="switchTab('guests')"
                >
                    <Users class="h-4 w-4" />
                    Daftar Tamu
                </button>
                <button
                    class="flex items-center gap-1.5 border-b-2 px-4 py-3.5 text-sm font-medium transition"
                    :class="tab === 'wishes' ? 'border-pink-600 text-pink-600' : 'border-transparent text-slate-500 hover:text-slate-700'"
                    @click="switchTab('wishes')"
                >
                    <MessageSquare class="h-4 w-4" />
                    Ucapan
                </button>
            </div>

            <!-- Filter -->
            <div class="flex flex-col gap-3 border-b border-slate-200 p-4 sm:flex-row sm:items-center">
                <div class="relative flex-1">
                    <Search class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Cari nama..."
                        class="w-full rounded-lg border border-slate-300 py-2 pl-9 pr-3 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                    />
                </div>
 
                <CustomSelect
                    v-model="statusValue"
                    :options="statusOptions"
                    :all-label="statusAllLabel"
                />
            </div>

            <!-- Tab: Daftar Tamu -->
            <div v-if="tab === 'guests'" class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 text-xs font-medium uppercase tracking-wide text-slate-400">
                            <th class="px-5 py-3">Nama Tamu</th>
                            <th class="px-5 py-3">Grup</th>
                            <th class="px-5 py-3">Kuota</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3">Dibuka</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-if="guests.data.length === 0">
                            <td colspan="5" class="px-5 py-10 text-center text-sm text-slate-400">
                                Belum ada data tamu.
                            </td>
                        </tr>
                        <tr v-for="guest in guests.data" :key="guest.id" class="transition hover:bg-slate-50">
                            <td class="px-5 py-3.5">
                                <p class="font-medium text-slate-900">{{ guest.name }}</p>
                                <p v-if="guest.phone" class="text-xs text-slate-500">{{ guest.phone }}</p>
                            </td>
                            <td class="px-5 py-3.5 text-slate-500">{{ guest.group ?? '-' }}</td>
                            <td class="px-5 py-3.5 text-slate-500">{{ guest.quota }}</td>
                            <td class="px-5 py-3.5">
                                <span
                                    class="rounded-full px-2.5 py-1 text-xs font-medium"
                                    :class="guest.is_sent ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600'"
                                >
                                    {{ guest.is_sent ? 'Terkirim' : 'Belum Kirim' }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-slate-500">
                                {{ guest.opened_at ? formatDateTime(guest.opened_at) : '-' }}
                            </td>
                        </tr>
                    </tbody>
                </table>

                <Pagination :links="guests.links" />
            </div>

            <!-- Tab: Ucapan -->
            <div v-if="tab === 'wishes'" class="divide-y divide-slate-100">
                <div v-if="wishes.data.length === 0" class="px-5 py-10 text-center text-sm text-slate-400">
                    Belum ada ucapan masuk.
                </div>
                <div v-for="wish in wishes.data" :key="wish.id" class="px-5 py-4">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-900">{{ wish.name }}</p>
                            <p class="text-xs text-slate-400">{{ formatDate(wish.created_at) }}</p>
                        </div>
                        <span
                            v-if="wish.attendance"
                            class="rounded-full px-2.5 py-1 text-xs font-medium"
                            :class="attendanceLabel[wish.attendance]?.class"
                        >
                            {{ attendanceLabel[wish.attendance]?.text }}
                        </span>
                    </div>
                    <p class="mt-2 text-sm text-slate-600">{{ wish.message }}</p>
                </div>

                <Pagination :links="wishes.links" />
            </div>
        </div>
    </div>
</template>