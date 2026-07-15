<script setup>
import BuilderLayout from '@/Layouts/BuilderLayout.vue'
import { Head } from '@inertiajs/vue3'
import { ref, reactive, computed } from 'vue'
import { Heart, Image, Gift, Palette, Users, Globe } from 'lucide-vue-next'

import CoupleEventForm from '@/Components/Builder/Sections/CoupleEventForm.vue'
import GalleryForm from '@/Components/Builder/Sections/GalleryForm.vue'
import DigitalEnvelopeForm from '@/Components/Builder/Sections/DigitalEnvelopeForm.vue'
import ThemeDesignForm from '@/Components/Builder/Sections/ThemeDesignForm.vue'
import GuestForm from '@/Components/Builder/Sections/GuestForm.vue'
import DomainForm from '@/Components/Builder/Sections/DomainForm.vue'
import BuilderPreviewPane from '@/Components/Builder/BuilderPreviewPane.vue'

defineOptions({ layout: BuilderLayout })

const props = defineProps({
    invitation: Object,
    availableThemes: Array,
})

const navigation = [
    { key: 'couple-event', label: 'Mempelai & Acara', icon: Heart },
    { key: 'gallery', label: 'Galeri Foto', icon: Image },
    { key: 'envelope', label: 'Amplop Digital', icon: Gift },
    { key: 'theme', label: 'Tema Desain', icon: Palette },
    { key: 'domain', label: 'Domain', icon: Globe },
    { key: 'guests', label: 'Kelola Tamu', icon: Users },
]

const activeSection = ref('couple-event')

const liveText = reactive({
    name: props.invitation.name ?? '',
    quote_text: props.invitation.quote_text ?? '',
    quote_source: props.invitation.quote_source ?? '',
    groom: {},
    bride: {},
    akad: {},
    resepsi: {},
})

const previewData = computed(() => ({
    ...liveText,
    galleries: props.invitation.galleries,
    theme: props.invitation.theme,
}))
</script>

<template>
    <Head :title="`Builder - ${invitation.name}`" />

    <div class="flex h-full">
        <div class="flex w-full flex-col overflow-y-auto border-r border-slate-200 bg-white lg:w-[440px] lg:shrink-0">
            <nav class="flex shrink-0 gap-1 overflow-x-auto border-b border-slate-200 p-3 lg:flex-col lg:overflow-visible">
                <button
                    v-for="item in navigation"
                    :key="item.key"
                    class="flex shrink-0 items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm font-medium transition lg:w-full"
                    :class="activeSection === item.key ? 'bg-pink-50 text-pink-700' : 'text-slate-600 hover:bg-slate-50'"
                    @click="activeSection = item.key"
                >
                    <component :is="item.icon" class="h-4 w-4" />
                    {{ item.label }}
                </button>
            </nav>

            <div class="flex-1 p-5">
                <CoupleEventForm
                    v-if="activeSection === 'couple-event'"
                    :invitation="invitation"
                    :live="liveText"
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
            <BuilderPreviewPane :data="previewData" />
        </div>
    </div>
</template>