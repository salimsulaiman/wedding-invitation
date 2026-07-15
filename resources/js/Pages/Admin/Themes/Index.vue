<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Pagination from '@/Components/Admin/Pagination.vue'
import { Head, Link, router, useForm } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import { Plus, Search, Pencil, Trash2, Settings2, Trash, X } from 'lucide-vue-next'
import debounce from 'lodash/debounce'
import { useFormatters } from '@/composables/useFormatters'

defineOptions({ layout: AdminLayout })

const props = defineProps({
    themes: Object,
    categories: Array,
    filters: Object,
})

const { formatCurrency } = useFormatters()

const search = ref(props.filters.search ?? '')
const categoryId = ref(props.filters.category_id ?? '')

watch([search, categoryId], debounce(([searchValue, categoryValue]) => {
    router.get(route('admin.themes.index'), { search: searchValue, category_id: categoryValue }, {
        preserveState: true,
        replace: true,
    })
}, 350))

const destroyTheme = (theme) => {
    if (confirm(`Hapus tema "${theme.name}"? Tindakan ini tidak bisa dibatalkan.`)) {
        router.delete(route('admin.themes.destroy', theme.id), { preserveScroll: true })
    }
}

const categoryModalOpen = ref(false)
const categoryForm = useForm({ name: '', price: 0, description: '', allow_custom_photos: false })

const editingCategory = ref(null)

const editCategory = (category) => {
    editingCategory.value = category.id

    categoryForm.name = category.name
    categoryForm.price = category.price
    categoryForm.description = category.description ?? ''
    categoryForm.allow_custom_photos = category.allow_custom_photos ?? false
}

const cancelEdit = () => {
    editingCategory.value = null
    categoryForm.reset()
}

const submitCategory = () => {
    if (editingCategory.value) {
        categoryForm.put(
            route('admin.theme-categories.update', editingCategory.value),
            {
                preserveScroll: true,
                onSuccess: () => {
                    editingCategory.value = null
                    categoryForm.reset()
                },
            }
        )

        return
    }

    categoryForm.post(route('admin.theme-categories.store'), {
        preserveScroll: true,
        onSuccess: () => categoryForm.reset(),
    })
}

const deleteCategory = (category) => {
    if (confirm(`Hapus jenis tema "${category.name}"? Pastikan tidak ada tema yang memakainya.`)) {
        router.delete(route('admin.theme-categories.destroy', category.id), { preserveScroll: true })
    }
}
</script>

<template>
    <Head title="Tema" />

    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Tema Undangan</h2>
                <p class="mt-1 text-sm text-slate-500">Kelola desain tema dan paket (jenis tema).</p>
            </div>
            <div class="flex items-center gap-2">
                <button
                    class="flex items-center gap-1.5 rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-50"
                    @click="categoryModalOpen = true"
                >
                    <Settings2 class="h-4 w-4" />
                    Kelola Paket
                </button>
                <Link
                    :href="route('admin.themes.create')"
                    class="flex items-center gap-1.5 rounded-lg bg-pink-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-pink-700"
                >
                    <Plus class="h-4 w-4" />
                    Tambah Tema
                </Link>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white">
            <div class="flex flex-col gap-3 border-b border-slate-200 p-4 sm:flex-row sm:items-center">
                <div class="relative flex-1">
                    <Search class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Cari nama tema..."
                        class="w-full rounded-lg border border-slate-300 py-2 pl-9 pr-3 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                    />
                </div>
                <select
                    v-model="categoryId"
                    class="rounded-lg border border-slate-300 py-2 px-3 text-sm text-slate-700 focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                >
                    <option value="">Semua Paket</option>
                    <option v-for="category in categories" :key="category.id" :value="category.id">
                        {{ category.name }}
                    </option>
                </select>
            </div>

            <div class="grid grid-cols-1 gap-4 p-5 sm:grid-cols-2 lg:grid-cols-3">
                <div v-if="themes.data.length === 0" class="col-span-full py-10 text-center text-sm text-slate-400">
                    Belum ada tema ditambahkan.
                </div>

                <div
                    v-for="theme in themes.data"
                    :key="theme.id"
                    class="overflow-hidden rounded-xl border border-slate-200"
                >
                    <div class="flex h-36 items-center justify-center bg-slate-100">
                        <img
                            v-if="theme.thumbnail"
                            :src="`/storage/${theme.thumbnail}`"
                            :alt="theme.name"
                            class="h-full w-full object-cover"
                        />
                        <span v-else class="text-xs text-slate-400">Belum ada thumbnail</span>
                    </div>

                    <div class="p-4">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-sm font-semibold text-slate-900">{{ theme.name }}</p>
                                <span class="mt-1 inline-block rounded-full bg-pink-50 px-2 py-0.5 text-[11px] font-medium text-pink-700">
                                    {{ theme.category?.name }}
                                </span>
                            </div>
                            <span
                                class="rounded-full px-2 py-1 text-[11px] font-medium"
                                :class="theme.is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-600'"
                            >
                                {{ theme.is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>

                        <p v-if="theme.description" class="mt-2 line-clamp-2 text-xs text-slate-500">
                            {{ theme.description }}
                        </p>

                        <div class="mt-4 flex items-center gap-2 border-t border-slate-100 pt-3">
                            <Link
                                :href="route('admin.themes.edit', theme.id)"
                                class="flex flex-1 items-center justify-center gap-1.5 rounded-lg border border-slate-200 py-2 text-xs font-medium text-slate-600 transition hover:bg-slate-50"
                            >
                                <Pencil class="h-3.5 w-3.5" />
                                Edit
                            </Link>
                            <button
                                class="rounded-lg border border-slate-200 p-2 text-slate-500 transition hover:bg-red-50 hover:text-red-600"
                                @click="destroyTheme(theme)"
                            >
                                <Trash2 class="h-3.5 w-3.5" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <Pagination :links="themes.links" />
        </div>
    </div>

    <div v-if="categoryModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 p-4">
        <div class="max-h-[85vh] w-full max-w-md overflow-y-auto rounded-xl bg-white">
            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                <h3 class="text-sm font-semibold text-slate-900">Kelola Paket</h3>
                <button class="text-slate-400 hover:text-slate-600" @click="categoryModalOpen = false">
                    <X class="h-4.5 w-4.5" />
                </button>
            </div>

            <div class="p-5">
                <form class="space-y-3" @submit.prevent="submitCategory">
                    <div>
                        <label class="block text-xs font-medium text-slate-600">Nama Paket</label>
                        <input
                            v-model="categoryForm.name"
                            type="text"
                            placeholder="Cth: Premium"
                            class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                        />
                        <p v-if="categoryForm.errors.name" class="mt-1 text-xs text-red-600">{{ categoryForm.errors.name }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-600">Harga Paket</label>
                        <input
                            v-model.number="categoryForm.price"
                            type="number"
                            min="0"
                            placeholder="Cth: 150000"
                            class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                        />
                        <p v-if="categoryForm.errors.price" class="mt-1 text-xs text-red-600">{{ categoryForm.errors.price }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-600">Deskripsi (opsional)</label>
                        <input
                            v-model="categoryForm.description"
                            type="text"
                            class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                        />
                    </div>

                    <div>
                        <label class="mb-2 block text-xs font-medium text-slate-600">
                            Kustomisasi Foto
                        </label>

                        <label class="flex cursor-pointer items-center justify-between rounded-lg border border-slate-300 px-4 py-3 transition hover:border-pink-300">
                            <div>
                                <p class="text-sm font-medium text-slate-800">
                                    Izinkan Upload Foto
                                </p>
                                <p class="mt-1 text-xs text-slate-500">
                                    Jika dinonaktifkan, pelanggan hanya dapat menggunakan foto bawaan tema. Foto yang sudah pernah diunggah tetap ditampilkan, tetapi tidak dapat menambah, mengganti, atau menghapus foto.
                                </p>
                                <button
                                    type="button"
                                    @click="categoryForm.allow_custom_photos = !categoryForm.allow_custom_photos"
                                    :class="[
                                        categoryForm.allow_custom_photos
                                            ? 'bg-pink-600'
                                            : 'bg-slate-300',
                                        'relative mt-3 inline-flex h-6 w-11 items-center rounded-full transition'
                                    ]"
                                >
                                    <span
                                        :class="[
                                            categoryForm.allow_custom_photos
                                                ? 'translate-x-6'
                                                : 'translate-x-1',
                                            'inline-block h-4 w-4 rounded-full bg-white transition'
                                        ]"
                                    />
                                </button>
                            </div>
                        </label>

                        <p v-if="categoryForm.errors.allow_custom_photos" class="mt-1 text-xs text-red-600">
                            {{ categoryForm.errors.allow_custom_photos }}
                        </p>
                    </div>

                    <button
                        type="submit"
                        :disabled="categoryForm.processing"
                        class="w-full rounded-lg bg-pink-600 py-2 text-sm font-semibold text-white transition hover:bg-pink-700 disabled:opacity-60"
                    >
                        {{ editingCategory ? 'Edit Paket' : 'Tambah Paket' }}
                    </button>
                    <button
                        v-if="editingCategory"
                        type="button"
                        @click="cancelEdit"
                        class="w-full rounded-lg border border-slate-300 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50"
                    >
                        Batal
                    </button>
                </form>

                <div class="mt-5 space-y-2 border-t border-slate-100 pt-4">
                    <p class="text-xs font-medium text-slate-500">Paket tersedia:</p>

                    <div v-if="categories.length === 0" class="py-6 text-center text-xs text-slate-400">
                        Belum ada paket dibuat.
                    </div>

                    <div
                        v-for="category in categories"
                        :key="category.id"
                        class="flex items-center justify-between rounded-lg border border-slate-100 px-3 py-2.5"
                    >
                        <div>
                            <p class="text-sm font-medium text-slate-900">{{ category.name }}</p>
                            <p class="text-xs font-medium text-pink-600">{{ formatCurrency(category.price) }}</p>
                        </div>
                        <div class="flex items-center gap-1">
                            <button
                                class="rounded-lg p-1.5 text-slate-400 hover:bg-blue-50 hover:text-blue-600"
                                @click="editCategory(category)"
                            >
                                <Pencil class="h-3.5 w-3.5" />
                            </button>

                            <button
                                class="rounded-lg p-1.5 text-slate-400 hover:bg-red-50 hover:text-red-600"
                                @click="deleteCategory(category)"
                            >
                                <Trash class="h-3.5 w-3.5" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>