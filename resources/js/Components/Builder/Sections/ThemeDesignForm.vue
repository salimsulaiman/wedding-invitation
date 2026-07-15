<script setup>
import { useForm, router } from '@inertiajs/vue3'
import { Music, Trash2, Check } from 'lucide-vue-next'

const props = defineProps({
    invitation: Object,
    availableThemes: Array,
})

const selectTheme = (theme) => {
    router.put(route('builder.theme.update', props.invitation.id), {
        theme_id: theme.id,
    }, {
        preserveScroll: true,
    })
}

const musicForm = useForm({
    title: '',
    file: null,
})

const submitMusic = () => {
    musicForm.post(route('builder.music.store', props.invitation.id), {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => musicForm.reset(),
    })
}

const destroyMusic = () => {
    if (confirm('Hapus backsound ini?')) {
        router.delete(route('builder.music.destroy', props.invitation.id), { preserveScroll: true })
    }
}
</script>

<template>
    <div class="space-y-8">
        <div>
            <h3 class="text-sm font-semibold text-slate-900">Tema Desain</h3>
            <p class="mt-1 text-sm text-slate-500">Pilih tema yang tersedia sesuai paket yang dimiliki customer.</p>

            <div v-if="availableThemes.length === 0" class="mt-4 rounded-lg border border-dashed border-slate-300 py-8 text-center text-sm text-slate-400">
                Belum ada tema tersedia untuk paket customer ini.
            </div>

            <div class="mt-4 grid grid-cols-2 gap-3">
                <button
                    v-for="theme in availableThemes"
                    :key="theme.id"
                    type="button"
                    class="relative overflow-hidden rounded-lg border-2 text-left transition"
                    :class="invitation.theme_id === theme.id ? 'border-pink-500' : 'border-slate-200 hover:border-slate-300'"
                    @click="selectTheme(theme)"
                >
                    <div class="flex h-24 items-center justify-center bg-slate-100">
                        <img v-if="theme.thumbnail" :src="`/storage/${theme.thumbnail}`" class="h-full w-full object-cover" />
                        <span v-else class="text-xs text-slate-400">Tidak ada gambar</span>
                    </div>
                    <div class="p-2">
                        <p class="truncate text-xs font-medium text-slate-800">{{ theme.name }}</p>
                    </div>
                    <div v-if="invitation.theme_id === theme.id" class="absolute right-1.5 top-1.5 rounded-full bg-pink-600 p-1 text-white">
                        <Check class="h-3 w-3" />
                    </div>
                </button>
            </div>
        </div>

        <div>
            <h3 class="text-sm font-semibold text-slate-900">Backsound Musik</h3>
            <p class="mt-1 text-sm text-slate-500">Opsional, musik latar saat undangan dibuka.</p>

            <div v-if="invitation.music" class="mt-4 flex items-center justify-between rounded-lg border border-slate-200 p-3">
                <div class="flex items-center gap-2">
                    <Music class="h-4 w-4 text-pink-500" />
                    <p class="text-sm text-slate-700">{{ invitation.music.title || 'Backsound' }}</p>
                </div>
                <button class="rounded-lg p-1.5 text-slate-400 hover:bg-red-50 hover:text-red-600" @click="destroyMusic">
                    <Trash2 class="h-3.5 w-3.5" />
                </button>
            </div>

            <form v-else class="mt-4 space-y-3" @submit.prevent="submitMusic">
                <input
                    v-model="musicForm.title"
                    type="text"
                    placeholder="Judul lagu (opsional)"
                    class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                />
                <input
                    type="file"
                    accept="audio/*"
                    class="block w-full text-xs text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-pink-50 file:px-3 file:py-1.5 file:text-xs file:font-medium file:text-pink-700 hover:file:bg-pink-100"
                    @change="musicForm.file = $event.target.files[0]"
                />
                <p v-if="musicForm.errors.file" class="text-xs text-red-600">{{ musicForm.errors.file }}</p>
                <button
                    type="submit"
                    :disabled="musicForm.processing || !musicForm.file"
                    class="w-full rounded-lg bg-pink-600 py-2 text-sm font-semibold text-white transition hover:bg-pink-700 disabled:opacity-60"
                >
                    Unggah Backsound
                </button>
            </form>
        </div>
    </div>
</template>