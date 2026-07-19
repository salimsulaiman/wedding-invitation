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
        <div class="flex flex-1 flex-col overflow-hidden py-8 px-8">

            <nav
                class="scroll-hidden mb-6 flex items-center gap-2 overflow-x-auto rounded-2xl border border-slate-200 bg-white p-4"
            >
                <button
                    v-for="item in navigation"
                    :key="item.key"
                    @click="activeSection = item.key"
                    class="flex min-w-max items-center gap-2 rounded-xl px-5 py-3 text-sm font-medium transition-all duration-200"
                    :class="
                        activeSection === item.key
                            ? 'bg-pink-600 text-white shadow-md'
                            : 'text-slate-600 hover:bg-slate-100'
                    "
                >
                    <component
                        :is="item.icon"
                        class="h-4 w-4"
                    />

                    <span>{{ item.label }}</span>
                </button>
            </nav>

            <div class="flex-1 overflow-y-auto p-6 bg-white border border-slate-200 rounded-2xl scroll-hidden">

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