<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ArrowLeft, UserRound } from 'lucide-vue-next'
import { route } from 'ziggy-js'

defineOptions({
    layout: AdminLayout,
})

interface Customer {
    id: number
    username: string
    name: string
    email: string | null
}

interface InvitationData {
    id: number
    name: string
    max_guest: number | null
    expired_at: string | null
}

interface Props {
    invitation: InvitationData
    customer: Customer
}

const props = defineProps<Props>()

const form = useForm({
    name: props.invitation.name,
    max_guest: (props.invitation.max_guest ?? '') as number | '',
    expired_at: props.invitation.expired_at ?? '',
})

const submit = (): void => {
    form.put(route('admin.invitations.update', props.invitation.id))
}
</script>

<template>
    <Head title="Edit Undangan" />

    <div class="max-w-xl space-y-6">
        <div class="flex items-center gap-3">
            <Link
                :href="route('admin.invitations.index')"
                class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-600"
            >
                <ArrowLeft class="h-4 w-4" />
            </Link>

            <div>
                <h2 class="text-lg font-semibold text-slate-900">
                    Edit Undangan
                </h2>

                <p class="mt-0.5 text-sm text-slate-500">
                    Ubah info dasar undangan, sisanya bisa dilengkapi di builder.
                </p>
            </div>
        </div>

        <form
            class="space-y-5 rounded-xl border border-slate-200 bg-white p-6"
            @submit.prevent="submit"
        >
            <div>
                <label class="block text-sm font-medium text-slate-700">
                    Customer
                </label>

                <div class="mt-1.5 flex items-center gap-3 rounded-lg border border-slate-200 bg-slate-50 px-3.5 py-2.5">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-slate-200 text-slate-500">
                        <UserRound class="h-4 w-4" />
                    </div>

                    <div class="min-w-0">
                        <p class="truncate text-sm font-medium text-slate-900">
                            {{ customer.username }}
                        </p>
                        <p class="truncate text-xs text-slate-500">
                            {{ customer.email ? `${customer.name} • ${customer.email}` : customer.name }}
                        </p>
                    </div>
                </div>

                <p class="mt-1.5 text-xs text-slate-400">
                    Customer tidak bisa diubah setelah undangan dibuat.
                </p>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">
                    Nama Acara Undangan
                </label>

                <input
                    v-model="form.name"
                    type="text"
                    placeholder="Cth: The Wedding of Andi & Siti"
                    class="mt-1.5 block w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                />

                <p
                    v-if="form.errors.name"
                    class="mt-1.5 text-sm text-red-600"
                >
                    {{ form.errors.name }}
                </p>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">
                    Maksimal Tamu

                    <span class="font-normal text-slate-400">
                        (kosongkan untuk tanpa batasan)
                    </span>
                </label>

                <input
                    v-model="form.max_guest"
                    type="number"
                    min="1"
                    class="mt-1.5 block w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                />

                <p
                    v-if="form.errors.max_guest"
                    class="mt-1.5 text-sm text-red-600"
                >
                    {{ form.errors.max_guest }}
                </p>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">
                    Tanggal Kedaluwarsa

                    <span class="font-normal text-slate-400">
                        (kosongkan untuk tanpa expired)
                    </span>
                </label>

                <input
                    v-model="form.expired_at"
                    type="date"
                    class="mt-1.5 block w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                />

                <p
                    v-if="form.errors.expired_at"
                    class="mt-1.5 text-sm text-red-600"
                >
                    {{ form.errors.expired_at }}
                </p>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-5">
                <Link
                    :href="route('admin.invitations.index')"
                    class="text-sm font-medium text-slate-500 hover:text-slate-700"
                >
                    Batal
                </Link>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="rounded-lg bg-pink-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-pink-700 disabled:opacity-60"
                >
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</template>