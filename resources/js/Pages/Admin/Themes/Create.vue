<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ArrowLeft } from 'lucide-vue-next'
import { ref } from 'vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
    categories: Array,
})

const form = useForm({
    theme_category_id: '',
    name: '',
    component_key: '',
    price: 0,
    description: '',
    thumbnail: null,
    is_active: true,
})

const preview = ref(null)

const onThumbnailChange = (event) => {
    const file = event.target.files[0]
    form.thumbnail = file
    preview.value = file ? URL.createObjectURL(file) : null
}

const submit = () => {
    form.post(route('admin.themes.store'), {
        forceFormData: true,
    })
}
</script>

<template>
    <Head title="Tambah Tema" />

    <div class="max-w-xl space-y-6">
        <div class="flex items-center gap-3">
            <Link :href="route('admin.themes.index')" class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-600">
                <ArrowLeft class="h-4 w-4" />
            </Link>
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Tambah Tema</h2>
                <p class="mt-0.5 text-sm text-slate-500">Tema baru akan tersedia untuk dibuka aksesnya ke customer.</p>
            </div>
        </div>

        <form class="space-y-5 rounded-xl border border-slate-200 bg-white p-6" @submit.prevent="submit">
            <div>
                <label class="block text-sm font-medium text-slate-700">Jenis Tema</label>
                <select
                    v-model="form.theme_category_id"
                    class="mt-1.5 block w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                >
                    <option value="" disabled>Pilih jenis tema</option>
                    <option v-for="category in categories" :key="category.id" :value="category.id">
                        {{ category.name }}
                    </option>
                </select>
                <p v-if="form.errors.theme_category_id" class="mt-1.5 text-sm text-red-600">{{ form.errors.theme_category_id }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">Nama Tema</label>
                <input
                    v-model="form.name"
                    type="text"
                    placeholder="Cth: Rosewood Elegance"
                    class="mt-1.5 block w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                />
                <p v-if="form.errors.name" class="mt-1.5 text-sm text-red-600">{{ form.errors.name }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">
                    Component Key
                    <span class="font-normal text-slate-400">(referensi ke project tema terpisah)</span>
                </label>
                <input
                    v-model="form.component_key"
                    type="text"
                    placeholder="Cth: rosewood-elegance"
                    class="mt-1.5 block w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                />
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">Deskripsi</label>
                <textarea
                    v-model="form.description"
                    rows="3"
                    class="mt-1.5 block w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                ></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">Thumbnail</label>
                <input
                    type="file"
                    accept="image/*"
                    class="mt-1.5 block w-full text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-pink-50 file:px-3 file:py-2 file:text-sm file:font-medium file:text-pink-700 hover:file:bg-pink-100"
                    @change="onThumbnailChange"
                />
                <p v-if="form.errors.thumbnail" class="mt-1.5 text-sm text-red-600">{{ form.errors.thumbnail }}</p>
                <img v-if="preview" :src="preview" class="mt-3 h-32 w-full rounded-lg object-cover" />
            </div>

            <label class="flex items-center gap-2 text-sm text-slate-600">
                <input
                    v-model="form.is_active"
                    type="checkbox"
                    class="h-4 w-4 rounded border-slate-300 text-pink-600 focus:ring-2 focus:ring-pink-500/30"
                />
                Aktifkan tema ini
            </label>

            <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-5">
                <Link :href="route('admin.themes.index')" class="text-sm font-medium text-slate-500 hover:text-slate-700">
                    Batal
                </Link>
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="rounded-lg bg-pink-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-pink-700 disabled:opacity-60"
                >
                    Simpan Tema
                </button>
            </div>
        </form>
    </div>
</template>