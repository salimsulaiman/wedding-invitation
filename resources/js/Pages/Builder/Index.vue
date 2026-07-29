<script setup lang="ts">
import BuilderLayout from '@/Layouts/BuilderLayout.vue'
import { Head, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import { Heart, Image, Gift, Palette, Users, Globe } from 'lucide-vue-next'
import type { Component } from 'vue'

import CoupleEventForm from '@/Components/Builder/Sections/CoupleEventForm.vue'
import GalleryForm from '@/Components/Builder/Sections/GalleryForm.vue'
import DigitalEnvelopeForm from '@/Components/Builder/Sections/DigitalEnvelopeForm.vue'
import ThemeDesignForm from '@/Components/Builder/Sections/ThemeDesignForm.vue'
import GuestForm from '@/Components/Builder/Sections/GuestForm.vue'
import DomainForm from '@/Components/Builder/Sections/DomainForm.vue'
import BuilderPreviewPane from '@/Components/Builder/BuilderPreviewPane.vue'

defineOptions({ layout: BuilderLayout })

interface Theme {
    [key: string]: any
}

interface Invitation {
    id: number | string
    name?: string
    theme_id?: number | string | null
    theme?: Theme
    [key: string]: any
}

interface Props {
    invitation: Invitation
    availableThemes: Theme[]
}

const props = defineProps<Props>()

interface NavigationItem {
    key: string
    label: string
    icon: Component
}

const navigation: NavigationItem[] = [
    { key: 'couple-event', label: 'Mempelai & Acara', icon: Heart },
    { key: 'gallery', label: 'Galeri Foto', icon: Image },
    { key: 'envelope', label: 'Amplop Digital', icon: Gift },
    { key: 'theme', label: 'Tema Desain', icon: Palette },
    { key: 'domain', label: 'Domain', icon: Globe },
    { key: 'guests', label: 'Kelola Tamu', icon: Users },
]

const activeSection = ref<string>('couple-event')

// -----------------------------------------------------------------
// Preview: render tema asli via iframe. `version` = cache-buster.
// -----------------------------------------------------------------
const previewPane = ref<InstanceType<typeof BuilderPreviewPane> | null>(null)
const previewVersion = ref<number>(Date.now())

function refreshPreview() {
    previewVersion.value = Date.now()
    previewPane.value?.reload()
}

// Reload preview otomatis tiap ada request builder yang sukses (PUT/POST/PATCH/DELETE)
router.on('success', (event) => {
    const url: string = event.detail?.page?.url ?? ''
    if (url.includes(`/invitations/${props.invitation.id}/builder`)) {
        refreshPreview()
    }
})

const navRef = ref<HTMLElement | null>(null)

function handleWheelScroll(e: WheelEvent) {
    if (!navRef.value) return
    if (e.deltaY !== 0) {
        e.preventDefault()
        navRef.value.scrollLeft += e.deltaY
    }
}
</script>

<template>
    <Head :title="`Builder - ${invitation.name}`" />

    <div class="flex h-full">
        <div class="flex flex-1 flex-col overflow-auto py-8 px-8">

            <nav ref="navRef" @wheel="handleWheelScroll"
                class="scroll-hidden mb-6 flex flex-nowrap items-center gap-2 overflow-x-auto scroll-smooth rounded-2xl border border-slate-200 bg-white p-4"
            >
                <button
                    v-for="item in navigation"
                    :key="item.key"
                    @click="activeSection = item.key"
                    class="flex min-w-max shrink-0 items-center gap-2 rounded-xl px-5 py-3 text-sm font-medium transition-all duration-200"
                    :class="
                        activeSection === item.key
                            ? 'bg-pink-600 text-white shadow-md'
                            : 'text-slate-600 hover:bg-slate-100'
                    "
                >
                    <component :is="item.icon" class="h-4 w-4" />
                    <span>{{ item.label }}</span>
                </button>
            </nav>

            <div class="flex-1 overflow-y-auto p-6 bg-white border border-slate-200 rounded-2xl scroll-hidden">

                <CoupleEventForm
                    v-if="activeSection === 'couple-event'"
                    :invitation="invitation"
                />
                <GalleryForm
                    v-else-if="activeSection === 'gallery'"
                    :invitation="invitation"
                />
                <DigitalEnvelopeForm
                    v-else-if="activeSection === 'envelope'"
                    :invitation="invitation"
                />
                <ThemeDesignForm
                    v-else-if="activeSection === 'theme'"
                    :invitation="invitation"
                    :available-themes="availableThemes"
                />
                <DomainForm
                    v-else-if="activeSection === 'domain'"
                    :invitation="invitation"
                />
                <GuestForm
                    v-else-if="activeSection === 'guests'"
                    :invitation="invitation"
                />

            </div>
        </div>

        <div class="hidden flex-1 items-center justify-center overflow-y-auto bg-slate-100 p-8 lg:flex">
            <div v-if="!invitation.theme_id" class="flex h-180 w-full max-w-95 items-center justify-center rounded-[2.2rem] border border-dashed border-slate-300 bg-white text-center text-sm text-slate-400">
                Pilih tema terlebih dahulu<br>untuk melihat preview
            </div>
            <BuilderPreviewPane
                v-else
                ref="previewPane"
                :invitation-id="invitation.id"
                :version="previewVersion"
            />
        </div>
    </div>
</template>