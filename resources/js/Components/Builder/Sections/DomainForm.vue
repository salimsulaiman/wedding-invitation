<script setup>
import { ref, computed, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { Globe, Save } from 'lucide-vue-next'
import debounce from 'lodash/debounce'
import { CircleX, CircleCheckBig, LoaderCircle } from 'lucide-vue-next'

const props = defineProps({
    invitation: Object,
})

const form = useForm({
    name: props.invitation.domain?.name ?? '',
})

const checking = ref(false)
const available = ref(null)

const previewUrl = computed(() => {
    if (!form.name) {
        return 'https://undangin.id/...'
    }

    return `https://undangin.id/${form.name}`
})

const checkDomain = debounce(async (name) => {
    if (!name) {
        available.value = null
        return
    }

    checking.value = true

    try {
        const response = await fetch(
            route('builder.domain.check', props.invitation.id),
            {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': document
                        .querySelector('meta[name="csrf-token"]')
                        .content,
                },
                body: JSON.stringify({
                    name,
                }),
            }
        )

        const data = await response.json()

        available.value = data.available
    } catch (e) {
        available.value = null
    } finally {
        checking.value = false
    }
}, 500)

watch(
    () => form.name,
    (value) => {
        form.name = value
            .toLowerCase()
            .replace(/\s+/g, '-')
            .replace(/[^a-z0-9-]/g, '')

        available.value = null

        checkDomain(form.name)
    }
)

const submit = () => {
    form.put(route('builder.domain.update', props.invitation.id), {
        preserveScroll: true,
    })
}

</script>

<template>
    <div class="space-y-6">
        <div>
            <h3 class="text-sm font-semibold text-slate-900">
                Domain Undangan
            </h3>
            <p class="mt-1 text-sm text-slate-500">
                Tentukan alamat undangan yang akan dibagikan kepada tamu.
            </p>
        </div>

        <form
            class="space-y-4 rounded-lg border border-dashed border-slate-300 p-4"
            @submit.prevent="submit"
        >
            <div>
                <label class="mb-2 block text-xs font-medium uppercase tracking-wide text-slate-500">
                    Domain
                </label>

                <div class="flex overflow-hidden rounded-lg border border-slate-300 focus-within:border-pink-500 focus-within:ring-2 focus-within:ring-pink-500/30">
                    <div class="flex items-center bg-slate-100 px-3 text-sm text-slate-500">
                        https://undangin.id/
                    </div>

                    <input
                        v-model="form.name"
                        type="text"
                        placeholder="aditya-amelia"
                        class="flex-1 border-0 px-3 py-2 text-sm focus:outline-none focus:ring-0"
                    >
                </div>

                <p
                    v-if="form.errors.name"
                    class="mt-2 text-xs text-red-600"
                >
                    {{ form.errors.name }}
                </p>
                <div
                    v-if="form.name"
                    class="mt-3 rounded-lg border border-slate-200 bg-slate-50 p-3"
                >
                    <div
                        v-if="checking"
                        class="flex items-center gap-2 text-sm text-slate-600"
                    >
                        <LoaderCircle class="h-4 w-4 animate-spin" />
                        Mengecek ketersediaan domain...
                    </div>

                    <div
                        v-else-if="available === true"
                        class="flex items-center gap-2 text-sm font-medium text-green-600"
                    >
                        <CircleCheckBig class="h-4 w-4" />
                        Domain tersedia
                    </div>

                    <div
                        v-else-if="available === false"
                        class="flex items-center gap-2 text-sm font-medium text-red-600"
                    >
                        <CircleX class="h-4 w-4" />
                        Domain sudah digunakan
                    </div>
                </div>
            </div>

            <div class="rounded-lg bg-slate-50 p-3">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">
                    Preview URL
                </p>

                <p class="mt-1 break-all text-sm font-medium text-pink-600">
                    {{ previewUrl }}
                </p>
            </div>

            <div
                v-if="invitation.domain"
                class="rounded-lg border border-green-200 bg-green-50 p-3"
            >
                <div class="flex items-start gap-2">
                    <Globe class="mt-0.5 h-4 w-4 text-green-600" />

                    <div>
                        <p class="text-sm font-medium text-green-700">
                            Domain Aktif
                        </p>

                        <p class="mt-1 break-all text-xs text-green-600">
                            https://undangin.id/{{ invitation.domain.name }}
                        </p>
                    </div>
                </div>
            </div>

            <button
                type="submit"
                :disabled="
                    form.processing ||
                    checking ||
                    available === false
                "
                class="flex w-full items-center justify-center gap-2 rounded-lg bg-pink-600 py-2 text-sm font-semibold text-white transition hover:bg-pink-700 disabled:cursor-not-allowed disabled:opacity-60"
            >
                <Save class="h-4 w-4" />
                Simpan Domain
            </button>
        </form>
    </div>
</template>