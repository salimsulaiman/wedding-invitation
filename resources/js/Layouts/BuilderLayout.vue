<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { X, Users, Palette } from 'lucide-vue-next'
import { computed } from 'vue'

const props = defineProps({
    invitation: {
        type: Object,
        required: true,
    },
})

const page = usePage()

const guestCount = computed(() => props.invitation.guests_count ?? 0)
const guestLimitLabel = computed(() =>
    props.invitation.max_guest ? `/ ${props.invitation.max_guest}` : '/ Tanpa batas'
)

const closeHref = computed(() =>
    page.props.auth.user.role === 'admin'
        ? route('admin.invitations.index')
        : route('user.invitations.index')
)
</script>

<template>
    <div class="flex h-screen flex-col bg-slate-50">
        <header class="flex h-16 shrink-0 items-center justify-between border-b border-slate-200 bg-white px-5 lg:px-8">
            <!-- Kiri: logo agency -->
            <div class="flex items-center gap-2.5">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-pink-600">
                    <span class="text-xs font-bold text-white">U</span>
                </div>
                <span class="hidden text-sm font-semibold tracking-tight text-slate-900 sm:block">
                    Undangin
                </span>
            </div>

            <!-- Kanan: info tema, tamu terdaftar, tutup builder -->
            <div class="flex items-center gap-2 sm:gap-4">
                <div class="hidden items-center gap-1.5 rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-600 sm:flex">
                    <Palette class="h-3.5 w-3.5 text-pink-500" />
                    {{ invitation.theme?.name ?? 'Belum pilih tema' }}
                </div>

                <div class="flex items-center gap-1.5 rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-600">
                    <Users class="h-3.5 w-3.5 text-pink-500" />
                    {{ guestCount }} {{ guestLimitLabel }}
                </div>

                <Link
                    :href="closeHref"
                    class="flex items-center gap-1.5 rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-600 transition hover:bg-slate-50"
                >
                    <X class="h-3.5 w-3.5" />
                    <span class="hidden sm:inline">Tutup Builder</span>
                </Link>
            </div>
        </header>

        <!-- Konten builder (sisi kiri nav+field, sisi kanan preview) diisi oleh halaman -->
        <div class="min-h-0 flex-1">
            <slot />
        </div>
    </div>
</template>