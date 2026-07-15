<script setup>
import { useForm, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import { Trash2, Plus, Send, Copy, Search, Download, Upload, ClipboardPaste, X, Check } from 'lucide-vue-next'
import Papa from 'papaparse'
import * as XLSX from 'xlsx'

const props = defineProps({
    invitation: Object,
})

const groupSuggestions = ['Keluarga', 'Kerabat', 'Teman Sekolah', 'Teman Kantor', 'Tetangga', 'VIP']

const form = useForm({
    name: '',
    phone: '',
    group: '',
    quota: 1,
})

const submit = () => {
    form.post(route('builder.guests.store', props.invitation.id), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    })
}

const destroy = (guest) => {
    router.delete(route('builder.guests.destroy', [props.invitation.id, guest.id]), {
        preserveScroll: true,
    })
}

const search = ref('')

const filteredGuests = computed(() => {
    if (!search.value.trim()) return props.invitation.guests ?? []
    const keyword = search.value.toLowerCase()
    return (props.invitation.guests ?? []).filter(
        (guest) =>
            guest.name.toLowerCase().includes(keyword) ||
            (guest.group ?? '').toLowerCase().includes(keyword)
    )
})

const buildGuestUrl = (guest) => {
    const domainName = props.invitation.domain?.name
    if (!domainName) return null
    return `${window.location.origin}/${domainName}?to=${guest.slug}`
}

const formatPhoneForWa = (phone) => {
    if (!phone) return null
    let cleaned = phone.replace(/[^\d+]/g, '')
    if (cleaned.startsWith('+')) cleaned = cleaned.slice(1)
    if (cleaned.startsWith('0')) cleaned = '62' + cleaned.slice(1)
    return cleaned
}

const buildWaLink = (guest) => {
    const phone = formatPhoneForWa(guest.phone)
    const url = buildGuestUrl(guest)
    if (!phone || !url) return null

    const message = `Halo ${guest.name}, kami dengan senang hati mengundang Anda. Silakan buka undangan kami di: ${url}`
    return `https://wa.me/${phone}?text=${encodeURIComponent(message)}`
}

const markSent = (guest) => {
    router.patch(route('builder.guests.mark-sent', [props.invitation.id, guest.id]), {}, {
        preserveScroll: true,
        preserveState: true,
    })
}

const sendWhatsapp = (guest) => {
    const link = buildWaLink(guest)
    if (!link) return
    window.open(link, '_blank')
    markSent(guest)
}

const copyLink = (guest) => {
    const url = buildGuestUrl(guest)
    if (!url) return
    navigator.clipboard.writeText(url)
}

/* ================= Import: modal + template + upload + paste ================= */

const showImportModal = ref(false)
const importMethod = ref('upload') // 'upload' | 'paste'
const pasteText = ref('')
const importedGuests = ref([])
const importError = ref('')

const openImportModal = () => {
    importMethod.value = 'upload'
    pasteText.value = ''
    importedGuests.value = []
    importError.value = ''
    showImportModal.value = true
}

const closeImportModal = () => {
    showImportModal.value = false
}

const downloadTemplate = () => {
    const rows = [
        ['Nama', 'No. WhatsApp', 'Grup'],
        ['Budi Santoso', '08123456789', 'Keluarga'],
        ['Siti Aminah', '08234567890', 'Teman Kantor'],
    ]

    const worksheet = XLSX.utils.aoa_to_sheet(rows)
    const workbook = XLSX.utils.book_new()
    XLSX.utils.book_append_sheet(workbook, worksheet, 'Tamu')
    XLSX.writeFile(workbook, 'template-daftar-tamu.xlsx')
}

const rowsToGuests = (rows) => {
    return rows
        .filter((row) => row.length && row[0] && String(row[0]).toLowerCase() !== 'nama')
        .map((row, index) => ({
            id: `import-${index}`,
            name: String(row[0] ?? '').trim(),
            phone: String(row[1] ?? '').trim(),
            group: String(row[2] ?? '').trim(),
        }))
        .filter((guest) => guest.name)
}

const handleFileUpload = (event) => {
    const file = event.target.files[0]
    if (!file) return

    importError.value = ''
    const isExcel = /\.(xlsx|xls)$/i.test(file.name)

    if (isExcel) {
        const reader = new FileReader()
        reader.onload = (e) => {
            const workbook = XLSX.read(e.target.result, { type: 'binary' })
            const firstSheet = workbook.Sheets[workbook.SheetNames[0]]
            const rows = XLSX.utils.sheet_to_json(firstSheet, { header: 1 })
            importedGuests.value = rowsToGuests(rows)

            if (!importedGuests.value.length) {
                importError.value = 'Tidak ada data yang bisa dibaca. Pastikan mengikuti format template.'
            }
        }
        reader.readAsBinaryString(file)
    } else {
        Papa.parse(file, {
            complete: (result) => {
                importedGuests.value = rowsToGuests(result.data)
                if (!importedGuests.value.length) {
                    importError.value = 'Tidak ada data yang bisa dibaca. Pastikan mengikuti format template.'
                }
            },
        })
    }

    event.target.value = ''
}

const detectDelimiter = (line) => {
    if (line.includes('\t')) return '\t'
    if (line.includes(';')) return ';'
    return ','
}

const previewPaste = () => {
    const lines = pasteText.value.split('\n').map((line) => line.trim()).filter(Boolean)
    if (!lines.length) return

    const delimiter = detectDelimiter(lines[0])
    const rows = lines.map((line) => line.split(delimiter).map((col) => col.trim()))
    importedGuests.value = rowsToGuests(rows)

    if (!importedGuests.value.length) {
        importError.value = 'Tidak ada data yang bisa dibaca. Format: Nama, No. WhatsApp, Grup.'
    }
}

const removeImportRow = (id) => {
    importedGuests.value = importedGuests.value.filter((row) => row.id !== id)
}

const savingImport = ref(false)

const saveImportedGuests = async () => {
    const validRows = importedGuests.value.filter((row) => row.name.trim())
    if (!validRows.length) return

    savingImport.value = true

    for (const row of validRows) {
        await new Promise((resolve) => {
            router.post(route('builder.guests.store', props.invitation.id), {
                name: row.name,
                phone: row.phone,
                group: row.group,
                quota: 1,
            }, {
                preserveScroll: true,
                preserveState: true,
                onFinish: resolve,
            })
        })
    }

    savingImport.value = false
    closeImportModal()
}

/* ================= Kirim massal WhatsApp ================= */

const showBulkModal = ref(false)
const bulkQueue = ref([])
const bulkIndex = ref(0)

const eligibleForBulk = computed(() =>
    (props.invitation.guests ?? []).filter((guest) => formatPhoneForWa(guest.phone) && !guest.is_sent)
)

const currentBulkGuest = computed(() => bulkQueue.value[bulkIndex.value] ?? null)

const startBulkSend = () => {
    if (!props.invitation.domain?.name) {
        alert('Atur domain undangan terlebih dahulu sebelum mengirim WhatsApp massal.')
        return
    }
    if (!eligibleForBulk.value.length) {
        alert('Semua tamu dengan nomor WhatsApp sudah dikirimi, atau belum ada nomor terdaftar.')
        return
    }
    bulkQueue.value = eligibleForBulk.value
    bulkIndex.value = 0
    showBulkModal.value = true
}

const sendCurrentAndNext = () => {
    const guest = currentBulkGuest.value
    if (!guest) return
    sendWhatsapp(guest)

    if (bulkIndex.value < bulkQueue.value.length - 1) {
        bulkIndex.value++
    } else {
        showBulkModal.value = false
    }
}

const skipCurrent = () => {
    if (bulkIndex.value < bulkQueue.value.length - 1) {
        bulkIndex.value++
    } else {
        showBulkModal.value = false
    }
}
</script>

<template>
    <div class="space-y-6">
        <div>
            <h3 class="text-sm font-semibold text-slate-900">Kelola Tamu</h3>
            <p class="mt-1 text-sm text-slate-500">
                {{ invitation.guests_count ?? 0 }} tamu
                {{ invitation.max_guest ? `dari maksimal ${invitation.max_guest}` : '· tanpa batasan' }}
            </p>
        </div>

        <div v-if="!invitation.domain?.name" class="rounded-lg bg-amber-50 px-3.5 py-2.5 text-xs text-amber-700">
            Atur domain undangan dulu di menu Tema Desain, supaya setiap tamu bisa mendapat link personal.
        </div>

        <form class="space-y-3 rounded-lg border border-slate-200 p-4" @submit.prevent="submit">
            <input
                v-model="form.name"
                type="text"
                placeholder="Nama tamu"
                class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
            />
            <div class="grid grid-cols-2 gap-3">
                <input
                    v-model="form.phone"
                    type="text"
                    placeholder="No. WhatsApp (opsional)"
                    class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                />
                <input
                    v-model="form.group"
                    type="text"
                    list="group-suggestions"
                    placeholder="Grup (opsional)"
                    class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                />
                <datalist id="group-suggestions">
                    <option v-for="group in groupSuggestions" :key="group" :value="group" />
                </datalist>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600">Jumlah Tamu (termasuk pendamping)</label>
                <input
                    v-model.number="form.quota"
                    type="number"
                    min="1"
                    class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                />
            </div>
            <p v-if="form.errors.name" class="text-xs text-red-600">{{ form.errors.name }}</p>
            <button
                type="submit"
                :disabled="form.processing"
                class="flex w-full items-center justify-center gap-1.5 rounded-lg bg-pink-600 py-2 text-sm font-semibold text-white transition hover:bg-pink-700 disabled:opacity-60"
            >
                <Plus class="h-4 w-4" />
                Tambah Tamu
            </button>
        </form>

        <div class="flex flex-wrap gap-2 w-full">
            <button
                type="button"
                class="flex-1 flex items-center gap-1.5 rounded-lg border border-slate-300 px-3 py-2 text-xs font-medium text-slate-700 transition hover:bg-slate-50"
                @click="openImportModal"
            >
                <Upload class="h-3.5 w-3.5" />
                Import Banyak Tamu
            </button>
            <button
                type="button"
                class="flex-1 flex items-center gap-1.5 rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-emerald-700"
                @click="startBulkSend"
            >
                <Send class="h-3.5 w-3.5" />
                Kirim Massal WhatsApp
            </button>
        </div>

        <div class="relative">
            <Search class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
            <input
                v-model="search"
                type="text"
                placeholder="Cari nama atau grup..."
                class="w-full rounded-lg border border-slate-300 py-2 pl-9 pr-3 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
            />
        </div>

        <div class="space-y-2">
            <div v-if="filteredGuests.length === 0" class="rounded-lg border border-dashed border-slate-200 py-8 text-center text-sm text-slate-400">
                Belum ada tamu ditambahkan.
            </div>

            <div
                v-for="guest in filteredGuests"
                :key="guest.id"
                class="flex items-center justify-between rounded-lg border border-slate-200 p-3"
            >
                <div>
                    <p class="text-sm font-medium text-slate-900">{{ guest.name }}</p>
                    <div class="mt-0.5 flex items-center gap-2 text-xs text-slate-500">
                        <span>{{ guest.group || 'Tanpa grup' }} · {{ guest.quota }} orang</span>
                        <span v-if="guest.opened_at" class="rounded-full bg-emerald-50 px-2 py-0.5 font-medium text-emerald-700">
                            Dibuka
                        </span>
                        <span v-else-if="guest.is_sent" class="rounded-full bg-amber-50 px-2 py-0.5 font-medium text-amber-700">
                            Terkirim
                        </span>
                        <span v-else class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-500">
                            Belum Dikirim
                        </span>
                    </div>
                </div>
                <div class="flex items-center gap-1">
                    <button
                        class="rounded-lg p-1.5 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600 disabled:opacity-30"
                        title="Salin Link"
                        :disabled="!invitation.domain?.name"
                        @click="copyLink(guest)"
                    >
                        <Copy class="h-3.5 w-3.5" />
                    </button>
                    <button
                        class="rounded-lg p-1.5 text-slate-400 transition hover:bg-emerald-50 hover:text-emerald-600 disabled:opacity-30"
                        title="Kirim WhatsApp"
                        :disabled="!invitation.domain?.name || !guest.phone"
                        @click="sendWhatsapp(guest)"
                    >
                        <Send class="h-3.5 w-3.5" />
                    </button>
                    <button
                        class="rounded-lg p-1.5 text-slate-400 transition hover:bg-red-50 hover:text-red-600"
                        title="Hapus"
                        @click="destroy(guest)"
                    >
                        <Trash2 class="h-3.5 w-3.5" />
                    </button>
                </div>
            </div>
        </div>

        <div v-if="showImportModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 p-4">
            <div class="max-h-[85vh] w-full max-w-md overflow-y-auto rounded-xl bg-white p-5">
                <div class="mb-4 flex items-center justify-between">
                    <h4 class="text-sm font-semibold text-slate-900">Import Banyak Tamu</h4>
                    <button class="text-slate-400 hover:text-slate-600" @click="closeImportModal">
                        <X class="h-4 w-4" />
                    </button>
                </div>

                <template v-if="importedGuests.length === 0">
                    <div class="mb-4 flex rounded-lg bg-slate-100 p-1">
                        <button
                            type="button"
                            class="flex flex-1 items-center justify-center gap-1.5 rounded-md py-1.5 text-xs font-medium transition"
                            :class="importMethod === 'upload' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500'"
                            @click="importMethod = 'upload'"
                        >
                            <Upload class="h-3.5 w-3.5" />
                            Upload File
                        </button>
                        <button
                            type="button"
                            class="flex flex-1 items-center justify-center gap-1.5 rounded-md py-1.5 text-xs font-medium transition"
                            :class="importMethod === 'paste' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500'"
                            @click="importMethod = 'paste'"
                        >
                            <ClipboardPaste class="h-3.5 w-3.5" />
                            Paste Teks
                        </button>
                    </div>

                    <template v-if="importMethod === 'upload'">
                        <p class="mb-3 text-xs text-slate-500">
                            Unduh template, isi data tamu di Excel/Google Sheets, lalu upload kembali file-nya.
                        </p>

                        <button
                            type="button"
                            class="mb-3 flex w-full items-center justify-center gap-1.5 rounded-lg border border-slate-300 py-2 text-xs font-medium text-slate-700 transition hover:bg-slate-50"
                            @click="downloadTemplate"
                        >
                            <Download class="h-3.5 w-3.5" />
                            Unduh Template Excel
                        </button>

                        <label class="flex cursor-pointer flex-col items-center justify-center gap-1.5 rounded-lg border-2 border-dashed border-slate-300 px-4 py-8 text-center transition hover:border-pink-300 hover:bg-pink-50/50">
                            <Upload class="h-5 w-5 text-slate-400" />
                            <span class="text-xs font-medium text-slate-600">Klik untuk pilih file</span>
                            <span class="text-[11px] text-slate-400">.xlsx, .xls, atau .csv</span>
                            <input type="file" accept=".xlsx,.xls,.csv" class="hidden" @change="handleFileUpload" />
                        </label>
                    </template>

                    <template v-else>
                        <p class="mb-2 text-xs text-slate-500">
                            Blok data dari Excel/Google Sheets (kolom Nama, No. WhatsApp, Grup), lalu tempel di sini.
                        </p>
                        <textarea
                            v-model="pasteText"
                            rows="7"
                            placeholder="Budi Santoso	08123456789	Keluarga&#10;Siti Aminah	08234567890	Teman Kantor"
                            class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                        ></textarea>
                        <button
                            type="button"
                            :disabled="!pasteText.trim()"
                            class="mt-3 w-full rounded-lg bg-pink-600 py-2 text-sm font-semibold text-white transition hover:bg-pink-700 disabled:opacity-60"
                            @click="previewPaste"
                        >
                            Lanjutkan
                        </button>
                    </template>

                    <p v-if="importError" class="mt-3 text-xs text-red-600">{{ importError }}</p>
                </template>

                <template v-else>
                    <p class="mb-3 text-xs text-slate-500">
                        Periksa data sebelum disimpan ({{ importedGuests.length }} tamu).
                    </p>

                    <div class="max-h-72 space-y-2 overflow-y-auto">
                        <div
                            v-for="row in importedGuests"
                            :key="row.id"
                            class="flex items-start gap-2 rounded-lg border border-slate-200 p-2"
                        >
                            <div class="flex-1 space-y-1.5">
                                <input
                                    v-model="row.name"
                                    type="text"
                                    placeholder="Nama"
                                    class="block w-full rounded-md border border-slate-300 px-2 py-1 text-xs focus:border-pink-500 focus:outline-none focus:ring-1 focus:ring-pink-500/30"
                                />
                                <input
                                    v-model="row.phone"
                                    type="text"
                                    placeholder="No. WhatsApp"
                                    class="block w-full rounded-md border border-slate-300 px-2 py-1 text-xs focus:border-pink-500 focus:outline-none focus:ring-1 focus:ring-pink-500/30"
                                />
                                <input
                                    v-model="row.group"
                                    type="text"
                                    placeholder="Grup"
                                    class="block w-full rounded-md border border-slate-300 px-2 py-1 text-xs focus:border-pink-500 focus:outline-none focus:ring-1 focus:ring-pink-500/30"
                                />
                            </div>
                            <button class="rounded-lg p-1.5 text-slate-400 hover:bg-red-50 hover:text-red-600" @click="removeImportRow(row.id)">
                                <Trash2 class="h-3.5 w-3.5" />
                            </button>
                        </div>
                    </div>

                    <div class="mt-4 flex gap-2">
                        <button
                            type="button"
                            :disabled="savingImport"
                            class="flex flex-1 items-center justify-center gap-1.5 rounded-lg bg-pink-600 py-2 text-sm font-semibold text-white transition hover:bg-pink-700 disabled:opacity-60"
                            @click="saveImportedGuests"
                        >
                            <Check class="h-4 w-4" />
                            {{ savingImport ? 'Menyimpan...' : 'Simpan Semua' }}
                        </button>
                        <button
                            type="button"
                            class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-50"
                            @click="importedGuests = []"
                        >
                            Kembali
                        </button>
                    </div>
                </template>
            </div>
        </div>

        <div v-if="showBulkModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 p-4">
            <div class="w-full max-w-sm rounded-xl bg-white p-5">
                <div class="mb-3 flex items-center justify-between">
                    <h4 class="text-sm font-semibold text-slate-900">Kirim Massal WhatsApp</h4>
                    <button class="text-slate-400 hover:text-slate-600" @click="showBulkModal = false">
                        <X class="h-4 w-4" />
                    </button>
                </div>

                <p class="mb-1 text-xs text-slate-500">Progres: {{ bulkIndex + 1 }} dari {{ bulkQueue.length }}</p>
                <div class="mb-4 h-1.5 w-full rounded-full bg-slate-100">
                    <div
                        class="h-1.5 rounded-full bg-emerald-600 transition-all"
                        :style="{ width: `${((bulkIndex + 1) / bulkQueue.length) * 100}%` }"
                    ></div>
                </div>

                <div v-if="currentBulkGuest" class="mb-4 rounded-lg border border-slate-200 p-3">
                    <p class="text-sm font-medium text-slate-900">{{ currentBulkGuest.name }}</p>
                    <p class="text-xs text-slate-500">{{ currentBulkGuest.phone }}</p>
                </div>

                <p class="mb-4 text-[11px] text-slate-400">
                    Klik "Kirim & Lanjut" untuk membuka WhatsApp ke tamu ini, sistem otomatis lanjut ke tamu berikutnya.
                </p>

                <div class="flex gap-2">
                    <button
                        type="button"
                        class="flex flex-1 items-center justify-center gap-1.5 rounded-lg bg-emerald-600 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700"
                        @click="sendCurrentAndNext"
                    >
                        <Send class="h-4 w-4" />
                        Kirim & Lanjut
                    </button>
                    <button
                        type="button"
                        class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-50"
                        @click="skipCurrent"
                    >
                        Lewati
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>