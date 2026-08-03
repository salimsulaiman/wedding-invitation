<script setup lang="ts">
import UserLayout from '@/Layouts/UserLayout.vue'
import { Head, useForm } from '@inertiajs/vue3'
import { User as UserIcon, KeyRound } from 'lucide-vue-next'
import { route } from 'ziggy-js'

defineOptions({ layout: UserLayout })

interface UserProfile {
    id: number
    name: string
    username: string
    email: string | null
    phone: string | null
}

const props = defineProps<{
    user: UserProfile
}>()

const profileForm = useForm({
    name: props.user.name,
    email: props.user.email ?? '',
    phone: props.user.phone ?? '',
})

const submitProfile = () => {
    profileForm.put(route('user.profile.update'), {
        preserveScroll: true,
    })
}

const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
})

const submitPassword = () => {
    passwordForm.put(route('user.profile.password.update'), {
        preserveScroll: true,
        onSuccess: () => passwordForm.reset(),
    })
}
</script>

<template>

    <Head title="Profil Saya" />

    <div class="mx-auto max-w-2xl space-y-6">
        <div>
            <h2 class="text-lg font-semibold text-slate-900">Profil Saya</h2>
            <p class="mt-1 text-sm text-slate-500">Kelola informasi akun dan keamanan Anda.</p>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-6">
            <div class="flex items-center gap-2">
                <UserIcon class="h-4 w-4 text-pink-600" />
                <h3 class="text-sm font-semibold text-slate-900">Informasi Akun</h3>
            </div>

            <form class="mt-5 space-y-4" @submit.prevent="submitProfile">
                <div>
                    <label class="block text-sm font-medium text-slate-700">Username</label>
                    <input :value="user.username" type="text" disabled
                        class="mt-1.5 block w-full rounded-lg border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-500" />
                    <p class="mt-1.5 text-xs text-slate-400">Username tidak bisa diubah. Hubungi admin bila diperlukan.
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Nama Lengkap</label>
                    <input v-model="profileForm.name" type="text"
                        class="mt-1.5 block w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30" />
                    <p v-if="profileForm.errors.name" class="mt-1.5 text-sm text-red-600">{{ profileForm.errors.name }}
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">
                        Email
                        <span class="font-normal text-slate-400">(opsional)</span>
                    </label>
                    <input v-model="profileForm.email" type="email"
                        class="mt-1.5 block w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30" />
                    <p v-if="profileForm.errors.email" class="mt-1.5 text-sm text-red-600">{{ profileForm.errors.email
                        }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">No. Telepon</label>
                    <input v-model="profileForm.phone" type="text" placeholder="08xxxxxxxxxx"
                        class="mt-1.5 block w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30" />
                    <p v-if="profileForm.errors.phone" class="mt-1.5 text-sm text-red-600">{{ profileForm.errors.phone
                        }}</p>
                </div>

                <button type="submit" :disabled="profileForm.processing"
                    class="w-full rounded-lg bg-pink-600 py-2.5 text-sm font-semibold text-white transition hover:bg-pink-700 disabled:opacity-60">
                    Simpan Perubahan
                </button>
            </form>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-6">
            <div class="flex items-center gap-2">
                <KeyRound class="h-4 w-4 text-pink-600" />
                <h3 class="text-sm font-semibold text-slate-900">Ganti Password</h3>
            </div>
            <p class="mt-1 text-sm text-slate-500">Pastikan password baru mudah diingat tapi sulit ditebak orang lain.
            </p>

            <form class="mt-5 space-y-4" @submit.prevent="submitPassword">
                <div>
                    <label class="block text-sm font-medium text-slate-700">Password Saat Ini</label>
                    <input v-model="passwordForm.current_password" type="password"
                        class="mt-1.5 block w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30" />
                    <p v-if="passwordForm.errors.current_password" class="mt-1.5 text-sm text-red-600">
                        {{ passwordForm.errors.current_password }}
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Password Baru</label>
                    <input v-model="passwordForm.password" type="password"
                        class="mt-1.5 block w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30" />
                    <p v-if="passwordForm.errors.password" class="mt-1.5 text-sm text-red-600">{{
                        passwordForm.errors.password }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Konfirmasi Password Baru</label>
                    <input v-model="passwordForm.password_confirmation" type="password"
                        class="mt-1.5 block w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30" />
                </div>

                <button type="submit" :disabled="passwordForm.processing"
                    class="w-full rounded-lg bg-pink-600 py-2.5 text-sm font-semibold text-white transition hover:bg-pink-700 disabled:opacity-60">
                    Perbarui Password
                </button>
            </form>
        </div>
    </div>
</template>