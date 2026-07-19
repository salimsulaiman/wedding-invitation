<script setup>
import { useForm, router } from '@inertiajs/vue3'
import { ref, onBeforeUnmount, computed } from 'vue'
import { Trash2, ImagePlus, X } from 'lucide-vue-next'

const props = defineProps({
    invitation: Object,
})

const allowCustomPhotos = computed(() => props.invitation.theme?.category?.allow_custom_photos ?? true)

const MAX_COVER = 3

const coverForm = useForm({
    type: 'cover',
    photos: [],
})

const coverPreviews = ref([])

const onCoverFilesChange = (event) => {
    const files = Array.from(event.target.files)
    addCoverFiles(files)
    event.target.value = ''
}

const addCoverFiles = (files) => {
    const remaining = remainingCoverSlots() - coverPreviews.value.length
    const accepted = files.slice(0, Math.max(remaining, 0))

    accepted.forEach((file) => {
        coverPreviews.value.push({
            file,
            url: URL.createObjectURL(file),
        })
    })

    coverForm.photos = coverPreviews.value.map((p) => p.file)
}

const removeCoverPreview = (index) => {
    URL.revokeObjectURL(coverPreviews.value[index].url)
    coverPreviews.value.splice(index, 1)
    coverForm.photos = coverPreviews.value.map((p) => p.file)
}

const submitCover = () => {
    coverForm.post(route('builder.gallery.store', props.invitation.id), {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            coverPreviews.value.forEach((p) => URL.revokeObjectURL(p.url))
            coverPreviews.value = []
            coverForm.reset()
        },
    })
}

const remainingCoverSlots = () => {
    const current = props.invitation.cover_photos?.length ?? 0
    return Math.max(MAX_COVER - current, 0)
}

const galleryForm = useForm({
    type: 'gallery',
    photos: [],
})

const galleryPreviews = ref([])

const onGalleryFilesChange = (event) => {
    const files = Array.from(event.target.files)
    files.forEach((file) => {
        galleryPreviews.value.push({
            file,
            url: URL.createObjectURL(file),
        })
    })
    galleryForm.photos = galleryPreviews.value.map((p) => p.file)
    event.target.value = ''
}

const removeGalleryPreview = (index) => {
    URL.revokeObjectURL(galleryPreviews.value[index].url)
    galleryPreviews.value.splice(index, 1)
    galleryForm.photos = galleryPreviews.value.map((p) => p.file)
}

const submitGallery = () => {
    galleryForm.post(route('builder.gallery.store', props.invitation.id), {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            galleryPreviews.value.forEach((p) => URL.revokeObjectURL(p.url))
            galleryPreviews.value = []
            galleryForm.reset()
        },
    })
}

const destroyPhoto = (photo) => {
    if (confirm('Hapus foto ini?')) {
        router.delete(route('builder.gallery.destroy', [props.invitation.id, photo.id]), {
            preserveScroll: true,
        })
    }
}

onBeforeUnmount(() => {
    coverPreviews.value.forEach((p) => URL.revokeObjectURL(p.url))
    galleryPreviews.value.forEach((p) => URL.revokeObjectURL(p.url))
})
</script>

<template>
    <div class="space-y-8">
        <div v-if="allowCustomPhotos">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-slate-900">Foto Cover</h3>
                <span class="text-xs text-slate-400">
                    {{ invitation.cover_photos?.length ?? 0 }} / {{ MAX_COVER }}
                </span>
            </div>
            <p class="mt-1 text-sm text-slate-500">
                Foto sampul yang tampil bergantian di halaman depan undangan. Sebaiknya berisi foto kedua mempelai.
            </p>

            <div v-if="invitation.cover_photos?.length" class="mt-4 grid grid-cols-3 gap-2">
                <div
                    v-for="photo in invitation.cover_photos"
                    :key="photo.id"
                    class="group relative aspect-[3/4] overflow-hidden rounded-lg border border-slate-200"
                >
                    <img :src="`/storage/${photo.photo}`" class="h-full w-full object-cover" />
                    <button
                        v-if="allowCustomPhotos"
                        class="absolute right-1.5 top-1.5 rounded-lg bg-white/90 p-1.5 text-red-600 opacity-0 shadow-sm transition group-hover:opacity-100"
                        @click="destroyPhoto(photo)"
                    >
                        <Trash2 class="h-3.5 w-3.5" />
                    </button>
                </div>
            </div>

            <div v-if="allowCustomPhotos && coverPreviews.length" class="mt-4">
                <p class="mb-2 text-xs font-medium text-slate-500">Siap diunggah:</p>
                <div class="grid grid-cols-3 gap-2">
                    <div
                        v-for="(preview, index) in coverPreviews"
                        :key="preview.url"
                        class="group relative aspect-[3/4] overflow-hidden rounded-lg border-2 border-dashed border-pink-300"
                    >
                        <img :src="preview.url" class="h-full w-full object-cover opacity-90" />
                        <button
                            class="absolute right-1.5 top-1.5 rounded-lg bg-white/90 p-1.5 text-red-600 shadow-sm transition hover:bg-white"
                            @click="removeCoverPreview(index)"
                        >
                            <X class="h-3.5 w-3.5" />
                        </button>
                    </div>
                </div>
            </div>

            <div v-if="allowCustomPhotos && remainingCoverSlots() > coverPreviews.length" class="mt-3">
                <label class="flex cursor-pointer flex-col items-center justify-center gap-1.5 rounded-lg border-2 border-dashed border-slate-300 px-4 py-6 text-center transition hover:border-pink-300 hover:bg-pink-50/50">
                    <ImagePlus class="h-5 w-5 text-slate-400" />
                    <span class="text-xs font-medium text-slate-600">
                        Pilih foto cover ({{ remainingCoverSlots() - coverPreviews.length }} slot tersisa)
                    </span>
                    <input type="file" accept="image/*" multiple class="hidden" @change="onCoverFilesChange" />
                </label>
            </div>
            <p v-else-if="coverPreviews.length === 0" class="mt-3 text-xs text-slate-400">
                Batas maksimal foto cover sudah tercapai. Hapus salah satu untuk menambah yang baru.
            </p>

            <p v-if="coverForm.errors.photos" class="mt-2 text-xs text-red-600">{{ coverForm.errors.photos }}</p>

            <button
                v-if="allowCustomPhotos && coverPreviews.length > 0"
                type="button"
                :disabled="coverForm.processing"
                class="mt-3 w-full rounded-lg bg-pink-600 py-2 text-sm font-semibold text-white transition hover:bg-pink-700 disabled:opacity-60"
                @click="submitCover"
            >
                {{ coverForm.processing ? 'Mengunggah...' : `Unggah ${coverPreviews.length} Foto Cover` }}
            </button>
        </div>
        <div v-else>
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-slate-900">Foto Cover</h3>
            </div>
            <p class="mt-1 text-sm text-slate-500">
                Tema ini tidak memiliki akses untuk upload cover. Silahkan pilih tema premium untuk menikmati fitur gallery
            </p>
        </div>

        <div v-if="allowCustomPhotos" class="border-t border-slate-100 pt-8">
            <h3 class="text-sm font-semibold text-slate-900">Galeri Foto</h3>
            <p class="mt-1 text-sm text-slate-500">Unggah beberapa foto sekaligus untuk galeri undangan.</p>

            <div v-if="invitation.galleries?.length" class="mt-4 grid grid-cols-3 gap-2">
                <div
                    v-for="photo in invitation.galleries"
                    :key="photo.id"
                    class="group relative aspect-square overflow-hidden rounded-lg border border-slate-200"
                >
                    <img :src="`/storage/${photo.photo}`" class="h-full w-full object-cover" />
                    <button
                        v-if="allowCustomPhotos"
                        class="absolute right-1.5 top-1.5 rounded-lg bg-white/90 p-1.5 text-red-600 opacity-0 shadow-sm transition group-hover:opacity-100"
                        @click="destroyPhoto(photo)"
                    >
                        <Trash2 class="h-3.5 w-3.5" />
                    </button>
                </div>
            </div>

            <div v-if="allowCustomPhotos && galleryPreviews.length" class="mt-4">
                <p class="mb-2 text-xs font-medium text-slate-500">Siap diunggah:</p>
                <div class="grid grid-cols-3 gap-2">
                    <div
                        v-for="(preview, index) in galleryPreviews"
                        :key="preview.url"
                        class="group relative aspect-square overflow-hidden rounded-lg border-2 border-dashed border-pink-300"
                    >
                        <img :src="preview.url" class="h-full w-full object-cover opacity-90" />
                        <button
                            class="absolute right-1.5 top-1.5 rounded-lg bg-white/90 p-1.5 text-red-600 shadow-sm transition hover:bg-white"
                            @click="removeGalleryPreview(index)"
                        >
                            <X class="h-3.5 w-3.5" />
                        </button>
                    </div>
                </div>
            </div>

            <label v-if="allowCustomPhotos" class="mt-3 flex cursor-pointer flex-col items-center justify-center gap-1.5 rounded-lg border-2 border-dashed border-slate-300 px-4 py-6 text-center transition hover:border-pink-300 hover:bg-pink-50/50">
                <ImagePlus class="h-5 w-5 text-slate-400" />
                <span class="text-xs font-medium text-slate-600">Pilih foto galeri (bisa lebih dari satu)</span>
                <input type="file" accept="image/*" multiple class="hidden" @change="onGalleryFilesChange" />
            </label>

            <p v-if="galleryForm.errors.photos" class="mt-2 text-xs text-red-600">{{ galleryForm.errors.photos }}</p>

            <button
                v-if="allowCustomPhotos && galleryPreviews.length > 0"
                type="button"
                :disabled="galleryForm.processing"
                class="mt-3 w-full rounded-lg bg-pink-600 py-2.5 text-sm font-semibold text-white transition hover:bg-pink-700 disabled:opacity-60"
                @click="submitGallery"
            >
                {{ galleryForm.processing ? 'Mengunggah...' : `Unggah ${galleryPreviews.length} Foto` }}
            </button>
        </div>

        <div v-else>
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-slate-900">Galeri Foto</h3>
            </div>
            <p class="mt-1 text-sm text-slate-500">
                Tema ini tidak memiliki akses untuk upload gallery. Silahkan pilih tema premium untuk menikmati fitur gallery
            </p>
        </div>
    </div>
</template>