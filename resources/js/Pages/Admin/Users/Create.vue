<script setup lang="ts">
import CustomSelect from '@/Components/General/CustomSelect.vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, Link, useForm, usePage } from '@inertiajs/vue3'
import { ArrowLeft, CheckCircle2, Copy, Send, X, Check, Package } from 'lucide-vue-next'
import { computed, ref, watch } from 'vue'
import { route } from 'ziggy-js'

defineOptions({ layout: AdminLayout })

interface CreatedAccount {
    name: string
    username: string
    password: string
    phone: string | null
}

interface UserCreateForm {
    name: string
    email: string
    phone: string
    role: 'admin' | 'user'
}

const form = useForm<UserCreateForm>({
    name: '',
    email: '',
    phone: '',
    role: 'user',
})

const page = usePage<{
    flash?: {
        account?: CreatedAccount
    }
}>()

const account = computed<CreatedAccount | null>(() => page.props.flash?.account ?? null)
const showAccountModal = ref(false)
const copied = ref(false)

watch(
    account,
    (value) => {
        if (value) {
            showAccountModal.value = true
            copied.value = false
        }
    },
    { immediate: true }
)

const loginInfoText = computed(() => {
    if (!account.value) return ''
    return `Halo ${account.value.name}, berikut informasi login akun undangan digital Anda:\n\nUsername: ${account.value.username}\nPassword: ${account.value.password}\n\nSilakan login dan segera ganti password Anda demi keamanan.`
})

const copyAccount = async () => {
    try {
        if (navigator.clipboard) {
            await navigator.clipboard.writeText(loginInfoText.value)
        } else {
            const textarea = document.createElement('textarea')
            textarea.value = loginInfoText.value
            document.body.appendChild(textarea)
            textarea.select()
            document.execCommand('copy')
            document.body.removeChild(textarea)
        }

        copied.value = true
        setTimeout(() => copied.value = false, 2000)
    } catch (e) {
        console.error(e)
    }
}

const sendWhatsapp = () => {
    if (!account.value?.phone) {
        window.open(`https://wa.me/?text=${encodeURIComponent(loginInfoText.value)}`, '_blank')
        return
    }
    let phone = account.value.phone.replace(/[^\d+]/g, '')
    if (phone.startsWith('0')) phone = '62' + phone.slice(1)
    window.open(`https://wa.me/${phone}?text=${encodeURIComponent(loginInfoText.value)}`, '_blank')
}

const closeModal = () => {
    showAccountModal.value = false
    form.reset()
    form.clearErrors()
}

const submit = () => {
    form.post(route('admin.users.store'))
}
</script>

<template>

    <Head title="Tambah Akun" />

    <div class="max-w-xl space-y-6">
        <div class="flex items-center gap-3">
            <Link :href="route('admin.users.index')"
                class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-600">
                <ArrowLeft class="h-4 w-4" />
            </Link>
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Tambah Akun</h2>
                <p class="mt-0.5 text-sm text-slate-500">Buat akun baru untuk admin atau customer.</p>
            </div>
        </div>

        <form class="space-y-5 rounded-xl border border-slate-200 bg-white p-6" @submit.prevent="submit">
            <div>
                <label class="block text-sm font-medium text-slate-700">Nama Lengkap</label>
                <input v-model="form.name" type="text"
                    class="mt-1.5 block w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30" />
                <p v-if="form.errors.name" class="mt-1.5 text-sm text-red-600">{{ form.errors.name }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">
                    Email
                    <span class="font-normal text-slate-400">(opsional)</span>
                </label>
                <input v-model="form.email" type="email"
                    class="mt-1.5 block w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30" />
                <p v-if="form.errors.email" class="mt-1.5 text-sm text-red-600">{{ form.errors.email }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">No. Telepon</label>
                <input v-model="form.phone" type="text" placeholder="08xxxxxxxxxx"
                    class="mt-1.5 block w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30" />
                <p v-if="form.errors.phone" class="mt-1.5 text-sm text-red-600">{{ form.errors.phone }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">Role</label>

                <CustomSelect v-model="form.role" :options="[
                    { value: 'user', label: 'Customer' },
                    { value: 'admin', label: 'Admin' },
                ]" class="mt-1.5" :include-all-option="false" />
            </div>

            <div class="rounded-lg border border-blue-200 bg-blue-50 p-4">
                <p class="text-sm font-medium text-blue-900">Username dan kata sandi akan dibuat otomatis.</p>
                <p class="mt-1 text-xs text-blue-700">Informasi login akan ditampilkan setelah akun berhasil dibuat.</p>
            </div>

            <div v-if="form.role === 'user'"
                class="flex items-start gap-3 rounded-lg border border-slate-200 bg-slate-50 p-4 text-xs text-slate-600">
                <Package class="mt-0.5 h-5 w-5 shrink-0 text-pink-500" />

                <div class="leading-6">
                    <p>
                        Akses paket tema tidak akan terbuka secara otomatis saat akun dibuat.
                    </p>
                    <p class="mt-1">
                        Untuk memberikan akses, buka halaman
                        <Link :href="route('admin.orders.index')" class="font-medium text-pink-600 hover:underline">
                            Pesanan
                        </Link>
                        setelah pembayaran berstatus
                        <span class="font-medium text-slate-900">"Lunas"</span>.
                    </p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-5">
                <Link :href="route('admin.users.index')"
                    class="text-sm font-medium text-slate-500 hover:text-slate-700">
                    Batal
                </Link>
                <button type="submit" :disabled="form.processing"
                    class="rounded-lg bg-pink-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-pink-700 disabled:opacity-60">
                    Simpan Akun
                </button>
            </div>
        </form>
    </div>

    <div v-if="showAccountModal && account"
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 p-4">
        <div class="w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-xl">
            <div class="flex items-start justify-between border-b border-slate-100 px-6 py-5">
                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-emerald-50">
                        <CheckCircle2 class="h-5 w-5 text-emerald-600" />
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-slate-900">Akun berhasil dibuat</h3>
                        <p class="mt-0.5 text-sm text-slate-500">Simpan informasi login berikut.</p>
                    </div>
                </div>
                <button class="text-slate-400 hover:text-slate-600" @click="closeModal">
                    <X class="h-4.5 w-4.5" />
                </button>
            </div>

            <div class="px-6 py-5">
                <div class="space-y-4 rounded-xl border border-pink-100 bg-pink-50/40 p-4">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Nama</p>
                        <p class="mt-0.5 text-sm font-medium text-slate-900">{{ account.name }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Username</p>
                        <p class="mt-0.5 font-mono text-sm font-semibold text-slate-900">{{ account.username }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Password</p>
                        <p class="mt-0.5 font-mono text-sm font-semibold text-slate-900">{{ account.password }}</p>
                    </div>
                </div>
                <p class="mt-3 text-xs text-slate-400">Customer akan diminta mengganti password ini saat login pertama
                    kali.</p>
            </div>

            <div class="flex flex-col gap-2 border-t border-slate-100 px-6 py-5">
                <button
                    class="flex items-center justify-center gap-1.5 rounded-lg bg-pink-600 py-2.5 text-sm font-semibold text-white transition hover:bg-pink-700"
                    @click="copyAccount">
                    <component :is="copied ? Check : Copy" class="h-4 w-4" />
                    {{ copied ? 'Tersalin' : 'Salin Informasi Login' }}
                </button>
                <button
                    class="flex items-center justify-center gap-1.5 rounded-lg bg-emerald-600 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-700"
                    @click="sendWhatsapp">
                    <Send class="h-4 w-4" />
                    Kirim ke WhatsApp
                </button>
                <button
                    class="rounded-lg border border-slate-300 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
                    @click="closeModal">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</template>