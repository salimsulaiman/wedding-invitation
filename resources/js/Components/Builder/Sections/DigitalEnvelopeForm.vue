<script setup>
import { useForm, router } from '@inertiajs/vue3'
import { Trash2, Copy } from 'lucide-vue-next'

const props = defineProps({
    invitation: Object,
})

const accountForm = useForm({
    type: 'bank',
    bank_name: '',
    account_number: '',
    account_holder: '',
})

const submitAccount = () => {
    accountForm.post(route('builder.accounts.store', props.invitation.id), {
        preserveScroll: true,
        onSuccess: () => accountForm.reset(),
    })
}

const destroyAccount = (account) => {
    if (confirm('Hapus rekening ini?')) {
        router.delete(route('builder.accounts.destroy', [props.invitation.id, account.id]), {
            preserveScroll: true,
        })
    }
}

const giftForm = useForm({
    receiver_name: props.invitation.gift_address?.receiver_name ?? '',
    phone: props.invitation.gift_address?.phone ?? '',
    address: props.invitation.gift_address?.address ?? '',
})

const submitGiftAddress = () => {
    giftForm.put(route('builder.gift-address.update', props.invitation.id), {
        preserveScroll: true,
    })
}

const copyNumber = (number) => {
    navigator.clipboard.writeText(number)
}
</script>

<template>
    <div class="space-y-8">
        <div>
            <h3 class="text-sm font-semibold text-slate-900">Amplop Digital (Kado)</h3>
            <p class="mt-1 text-sm text-slate-500">Tambahkan rekening bank atau e-wallet.</p>

            <div class="mt-4 space-y-2">
                <div
                    v-for="account in invitation.accounts"
                    :key="account.id"
                    class="flex items-center justify-between rounded-lg border border-slate-200 p-3"
                >
                    <div>
                        <p class="text-sm font-medium text-slate-900">{{ account.bank_name }}</p>
                        <p class="text-xs text-slate-500">{{ account.account_number }} a.n {{ account.account_holder }}</p>
                    </div>
                    <div class="flex items-center gap-1">
                        <button class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100" @click="copyNumber(account.account_number)">
                            <Copy class="h-3.5 w-3.5" />
                        </button>
                        <button class="rounded-lg p-1.5 text-slate-400 hover:bg-red-50 hover:text-red-600" @click="destroyAccount(account)">
                            <Trash2 class="h-3.5 w-3.5" />
                        </button>
                    </div>
                </div>
            </div>

            <form class="mt-4 space-y-3 rounded-lg border border-dashed border-slate-300 p-4" @submit.prevent="submitAccount">
                <div class="grid grid-cols-2 gap-3">
                    <select
                        v-model="accountForm.type"
                        class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                    >
                        <option value="bank">Bank</option>
                        <option value="ewallet">E-Wallet</option>
                    </select>
                    <input
                        v-model="accountForm.bank_name"
                        type="text"
                        placeholder="Nama Bank/E-Wallet"
                        class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                    />
                </div>
                <input
                    v-model="accountForm.account_number"
                    type="text"
                    placeholder="No. Rekening"
                    class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                />
                <input
                    v-model="accountForm.account_holder"
                    type="text"
                    placeholder="Atas Nama"
                    class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                />
                <button
                    type="submit"
                    :disabled="accountForm.processing"
                    class="w-full rounded-lg bg-pink-600 py-2 text-sm font-semibold text-white transition hover:bg-pink-700 disabled:opacity-60"
                >
                    Tambah Rekening
                </button>
            </form>
        </div>

        <div>
            <h3 class="text-sm font-semibold text-slate-900">Kirim Kado Fisik</h3>
            <p class="mt-1 text-sm text-slate-500">Alamat pengiriman paket/kado.</p>

            <form class="mt-4 space-y-3" @submit.prevent="submitGiftAddress">
                <input
                    v-model="giftForm.receiver_name"
                    type="text"
                    placeholder="Nama Penerima (opsional)"
                    class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                />
                <input
                    v-model="giftForm.phone"
                    type="text"
                    placeholder="No. Telepon (opsional)"
                    class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                />
                <textarea
                    v-model="giftForm.address"
                    rows="3"
                    placeholder="Alamat lengkap pengiriman"
                    class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                ></textarea>
                <p v-if="giftForm.errors.address" class="text-xs text-red-600">{{ giftForm.errors.address }}</p>
                <button
                    type="submit"
                    :disabled="giftForm.processing"
                    class="w-full rounded-lg bg-pink-600 py-2 text-sm font-semibold text-white transition hover:bg-pink-700 disabled:opacity-60"
                >
                    Simpan Alamat
                </button>
            </form>
        </div>
    </div>
</template>