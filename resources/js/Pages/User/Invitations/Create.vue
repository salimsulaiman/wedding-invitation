<script setup lang="ts">
import UserLayout from '@/Layouts/UserLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { AlertCircle, ArrowLeft, Check } from 'lucide-vue-next'
import { route } from 'ziggy-js'

defineOptions({ layout: UserLayout })

interface Theme {
    id: number
    name: string
    theme_category_id: number
    thumbnail: string | null
}

const props = defineProps<{
    themes: Theme[]
    hasAnyAccess: boolean
}>()

const form = useForm({
    name: '',
    theme_id: null as number | null,
})

const selectTheme = (theme: Theme) => {
    form.theme_id = theme.id
}

const submit = () => {
    form.post(route('user.invitations.store'))
}
</script>

<template>

    <Head title="Buat Undangan" />

    <div class="max-w-2xl space-y-6">
        <div class="flex items-center gap-3">
            <Link :href="route('user.invitations.index')"
                class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-600">
                <ArrowLeft class="h-4 w-4" />
            </Link>
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Buat Undangan Baru</h2>
                <p class="mt-0.5 text-sm text-slate-500">Isi nama acara, sisanya bisa dilengkapi nanti di builder.</p>
            </div>
        </div>

        <div v-if="!hasAnyAccess"
            class="flex items-start gap-2.5 rounded-lg bg-amber-50 px-4 py-3.5 text-sm text-amber-700">
            <AlertCircle class="mt-0.5 h-4 w-4 shrink-0" />
            <div>
                Anda belum memiliki akses ke paket tema manapun. Anda tetap bisa membuat undangan dan memilih tema
                setelah menghubungi admin untuk membuka akses paket.
            </div>
        </div>

        <form class="space-y-6 rounded-xl border border-slate-200 bg-white p-6" @submit.prevent="submit">
            <div>
                <label class="block text-sm font-medium text-slate-700">Nama Acara Undangan</label>
                <input v-model="form.name" type="text" placeholder="Cth: The Wedding of Andi & Siti"
                    class="mt-1.5 block w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30" />
                <p v-if="form.errors.name" class="mt-1.5 text-sm text-red-600">{{ form.errors.name }}</p>
            </div>

            <div v-if="themes.length > 0">
                <label class="block text-sm font-medium text-slate-700">Pilih Tema</label>
                <p class="mt-1 text-xs text-slate-400">Tema bisa diganti kapan saja nanti di builder.</p>

                <div class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-3">
                    <button v-for="theme in themes" :key="theme.id" type="button"
                        class="relative overflow-hidden rounded-lg border-2 text-left transition"
                        :class="form.theme_id === theme.id ? 'border-pink-500' : 'border-slate-200 hover:border-slate-300'"
                        @click="selectTheme(theme)">
                        <div class="flex h-24 items-center justify-center bg-slate-100">
                            <img v-if="theme.thumbnail" :src="`/storage/${theme.thumbnail}`"
                                class="h-full w-full object-cover" />
                            <span v-else class="text-xs text-slate-400">Tidak ada gambar</span>
                        </div>
                        <div class="p-2">
                            <p class="truncate text-xs font-medium text-slate-800">{{ theme.name }}</p>
                        </div>
                        <div v-if="form.theme_id === theme.id"
                            class="absolute right-1.5 top-1.5 rounded-full bg-pink-600 p-1 text-white">
                            <Check class="h-3 w-3" />
                        </div>
                    </button>
                </div>
                <p v-if="form.errors.theme_id" class="mt-1.5 text-sm text-red-600">{{ form.errors.theme_id }}</p>
            </div>

            <div v-else
                class="rounded-lg border border-dashed border-slate-200 py-6 text-center text-sm text-slate-400">
                Belum ada tema yang bisa dipilih untuk akun Anda saat ini.
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-5">
                <Link :href="route('user.invitations.index')"
                    class="text-sm font-medium text-slate-500 hover:text-slate-700">
                    Batal
                </Link>
                <button type="submit" :disabled="form.processing"
                    class="rounded-lg bg-pink-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-pink-700 disabled:opacity-60">
                    Buat & Lanjut ke Builder
                </button>
            </div>
        </form>
    </div>
</template>