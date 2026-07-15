<script setup>
import { useFormatters } from '@/composables/useFormatters'
import { Head } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import {
    useCountdown,
    useCoverCarousel,
    useGallery,
    useWishesPagination,
    useRevealOnScroll,
    useScrollSpy,
    useRsvpForm,
    useCopyToClipboard,
    useCalendarLink,
    useMusicPlayer,
    useOpenInvitation,
} from '@/Composables/Theme'

const { formatLongDate, formatTime } = useFormatters()

const props = defineProps({
    invitation: Object,
    guest: Object,
})

const audioEl = ref(null)
const petalsContainer = ref(null)
const thumbsWrap = ref(null)

const asset = (file) => `/themes/${props.invitation.theme.component_key}/assets/${file}`
const photoUrl = (photo) => (photo ? `/storage/${photo}` : 'pasangan.jpeg')

// ---------------------------------------------------------------------------
// Data shaping — tetap ditulis per tema karena bentuk data bisa beda antar tema
// ---------------------------------------------------------------------------

const groom = computed(() => props.invitation?.couples?.find((c) => c.type === 'groom'))
const bride = computed(() => props.invitation?.couples?.find((c) => c.type === 'bride'))
const akad = computed(() => props.invitation?.events?.find((e) => e.type === 'akad'))
const resepsi = computed(() => props.invitation?.events?.find((e) => e.type === 'resepsi'))

const heroDateText = computed(() => formatLongDate(resepsi.value?.date ?? akad.value?.date))

const galleryImages = computed(() => {
    if (props.invitation?.galleries?.length) {
        return props.invitation.galleries.map((g) => `/storage/${g.photo}`)
    }
    return ['pasangan.jpeg', 'pasangan.jpeg', 'pasangan.jpeg', 'pasangan.jpeg', 'pasangan.jpeg', 'pasangan.jpeg']
})

const coverPhotos = computed(() => {
    if (props.invitation?.cover_photos?.length) {
        return props.invitation.cover_photos.map((p) => photoUrl(p.photo))
    }
    return [photoUrl(null)]
})

const musicSrc = computed(() =>
    props.invitation?.music
        ? `/storage/${props.invitation.music.file_path}`
        : 'https://the.invisimple.id/wp-content/uploads/2024/10/Tiara-Andini-Arsy-Widianto-Lagu-Pernikahan-Kita.mp3'
)

const quoteText = computed(
    () =>
        props.invitation?.quote_text ??
        'Dan di antara tanda-tanda kebesaran-Nya ialah Dia menciptakan pasangan-pasangan untukmu dari jenismu sendiri, agar kamu cenderung dan merasa tenteram kepadanya, dan Dia menjadikan di antaramu rasa kasih dan sayang.'
)
const quoteSource = computed(() => props.invitation?.quote_source ?? 'Q.S. Ar-Rum : 21')

const wishes = computed(() => props.invitation?.wishes ?? [])

const initials = (name) =>
    (name ?? '')
        .split(' ')
        .map((n) => n[0])
        .join('')
        .substring(0, 2)
        .toUpperCase()

const attendanceLabel = (value) => (value === 'hadir' ? 'Hadir' : 'Tidak Hadir')

// ---------------------------------------------------------------------------
// Behavior — semua diambil dari composable reusable
// ---------------------------------------------------------------------------

const {
    days: cdDays,
    hours: cdHours,
    minutes: cdMins,
    seconds: cdSecs,
} = useCountdown(() => akad.value ?? resepsi.value)

const { activeIndex: activeCoverIndex } = useCoverCarousel(coverPhotos)

const {
    currentIndex: currentGalIdx,
    mainImage: mainGalleryImg,
    thumbsWrap: galleryThumbsWrap,
    next: nextGallery,
    prev: prevGallery,
    set: setGallery,
} = useGallery(galleryImages)
// pakai ref bawaan composable untuk thumbsWrap
thumbsWrap.value = galleryThumbsWrap.value

const {
    page: wishesPage,
    totalPages: totalWishesPages,
    pagedWishes,
    pageNumbers: wishesPageNumbers,
    goToPage: goToWishesPage,
} = useWishesPagination(wishes, 5)

const { isPlaying, play: playMusic, toggle: toggleMusic } = useMusicPlayer(audioEl)

const { opened, open: openInvitation } = useOpenInvitation({
    musicPlayer: { play: playMusic },
    petalsContainer,
})

const { toast: copyToast, copy: copyAccountRaw } = useCopyToClipboard()
const copyAccount = (number) => copyAccountRaw(number, 'Nomor rekening disalin: ' + number)

const { form, rsvpMessage: rsvpMsg, submit: submitWish } = useRsvpForm({
    guest: props.guest,
    getDomainName: () => props.invitation?.domain?.name,
})

const calendarHref = useCalendarLink({
    event: computed(() => resepsi.value ?? akad.value),
    title: computed(
        () => `The Wedding of ${groom.value?.nickname ?? groom.value?.full_name ?? ''} & ${bride.value?.nickname ?? bride.value?.full_name ?? ''}`
    ),
    details: computed(
        () => `Akad Nikah & Resepsi Pernikahan ${groom.value?.nickname ?? ''} & ${bride.value?.nickname ?? ''}`
    ),
})

useRevealOnScroll()
useScrollSpy({
    activeClasses: ['text-[#1F3A5F]', '-translate-y-[3px]'],
    inactiveClasses: ['text-[#1F3A5F]/45'],
})
</script>

<template>
    <Head :title="invitation?.name ?? 'Undangan Pernikahan'" />

    <audio v-if="invitation" ref="audioEl" :src="musicSrc" loop></audio>

    <div class="antialiased font-['Jost',sans-serif] bg-[#FAF7F1] text-[#1F3A5F] overflow-x-hidden pb-[100px]">

        <div class="hidden lg:flex fixed inset-y-0 left-0 right-[420px] flex-col items-center justify-center bg-[#FAF7F1] overflow-hidden border-r border-[#B9CFE1]/30">
            <div class="absolute top-[-40px] left-[-40px] w-96 opacity-80 pointer-events-none [transform:scale(-1,-1)]">
                <img :src="asset('corner1.png')" class="w-full animate-[pulseZoom_5.5s_ease-in-out_infinite]">
            </div>
            <div class="absolute top-[-40px] right-[-40px] w-96 opacity-80 pointer-events-none -rotate-90">
                <img :src="asset('corner1.png')" class="w-full animate-[pulseZoom_5.5s_ease-in-out_infinite] [animation-delay:2s]">
            </div>
            <div class="absolute bottom-[-40px] left-[-40px] w-96 opacity-80 pointer-events-none -scale-x-100">
                <img :src="asset('corner1.png')" class="w-full animate-[pulseZoom_5.5s_ease-in-out_infinite] [animation-delay:2s]">
            </div>
            <div class="absolute bottom-[-40px] right-[-40px] w-96 opacity-80 pointer-events-none [transform:scaleY(-1)_rotate(-90deg)]">
                <img :src="asset('corner1.png')" class="w-full animate-[pulseZoom_5.5s_ease-in-out_infinite]">
            </div>

            <div class="absolute inset-0 opacity-[0.03] pointer-events-none [background-image:radial-gradient(#1F3A5F_1px,transparent_1px)] [background-size:24px_24px]"></div>

            <div class="text-center relative z-10 flex flex-col items-center">
                <p class="font-['Cormorant_Garamond',serif] tracking-[0.35em] text-sm uppercase text-[#1F3A5F] mb-4">The Wedding Of</p>
                <h1 class="font-['Tangerine',cursive] text-8xl text-[#1F3A5F] drop-shadow-sm">
                    {{ groom?.nickname ?? groom?.full_name }} &amp; {{ bride?.nickname ?? bride?.full_name }}
                </h1>
            </div>

            <div class="absolute right-8 top-1/2 -translate-y-1/2 flex flex-col items-center gap-2 text-[#1F3A5F]/40">
                <span class="text-[10px] tracking-[0.3em] uppercase [writing-mode:vertical-rl]">Scroll</span>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-4 h-4 animate-bounce"><path d="M12 5v14M19 12l-7 7-7-7" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>
        </div>

        <div id="app-wrap" class="w-full lg:w-[420px] lg:ml-auto min-h-screen relative bg-[#FAF7F1] shadow-[-10px_0_30px_rgba(0,0,0,0.1)]">

            <div ref="petalsContainer" class="fixed inset-y-0 right-0 w-full lg:w-[420px] pointer-events-none z-40 hidden md:block overflow-hidden"></div>

            <div v-if="!opened" class="fixed top-0 bottom-0 right-0 w-full lg:w-[420px] z-50 bg-[#FAF7F1] overflow-y-auto overflow-x-hidden">
                <div class="relative max-w-md w-full mx-auto min-h-full flex flex-col p-4">
                    <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
                        <div class="absolute w-[250px] pointer-events-none z-0 top-[-18px] left-[-18px] [transform:scale(-1,-1)]">
                            <img :src="asset('corner1.png')" alt="" class="w-full opacity-75 animate-[pulseZoom_5.5s_ease-in-out_infinite] brightness-[1.05]">
                        </div>
                        <div class="absolute w-[250px] pointer-events-none z-0 bottom-[-18px] right-[-18px] [transform:srotate(-90deg)]">
                            <img :src="asset('corner1.png')" alt="" class="w-full opacity-75 animate-[pulseZoom_5.5s_ease-in-out_infinite] [animation-delay:1.8s] brightness-[1.05]">
                        </div>
                    </div>

                    <div class="bg-white/10 backdrop-blur-[3px] border border-white/35 shadow-[0_8px_32px_rgba(0,0,0,0.05)] rounded-3xl relative z-10 w-full text-center py-10 px-4 my-auto">
                        <p class="font-['Cormorant_Garamond',serif] tracking-[0.35em] text-[10px] text-[#5F84A8] uppercase mb-4">The Wedding Of</p>

                        <div class="relative w-full max-w-[260px] mx-auto flex flex-col items-center justify-center">
                            <div class="relative w-[220px] h-[280px] rounded-[110px_110px_12px_12px] border-[3px] border-white shadow-[0_8px_24px_rgba(31,58,95,0.18),0_0_0_1px_#B9CFE1] overflow-hidden bg-[#B9CFE1]/40 z-[2]">
                                <img
                                    v-for="(photo, index) in coverPhotos"
                                    :key="photo + index"
                                    :src="photo"
                                    alt="Foto Pasangan"
                                    class="absolute inset-0 w-full h-full object-cover transition-opacity duration-1000 ease-in-out"
                                    :class="index === activeCoverIndex ? 'opacity-100' : 'opacity-0'"
                                >
                            </div>

                            <div v-if="coverPhotos.length > 1" class="flex gap-1.5 mt-2">
                                <span
                                    v-for="(photo, index) in coverPhotos"
                                    :key="'dot-' + index"
                                    class="h-1.5 w-1.5 rounded-full transition-colors"
                                    :class="index === activeCoverIndex ? 'bg-[#1F3A5F]' : 'bg-[#B9CFE1]'"
                                ></span>
                            </div>

                            <img :src="asset('corner2.png')" alt="" class="w-[180px] h-auto object-contain opacity-95 -mt-[45px] relative z-10 animate-[cornerZoom_4s_ease-in-out_infinite]">
                        </div>

                        <h1 class="font-['Tangerine',cursive] text-6xl leading-none mt-4 text-[#1F3A5F]">
                            {{ groom?.nickname ?? groom?.full_name }} &amp; {{ bride?.nickname ?? bride?.full_name }}
                        </h1>
                        <div class="h-px bg-gradient-to-r from-transparent via-[#C7A662] to-transparent w-20 mx-auto my-4"></div>
                        <p class="font-['Cormorant_Garamond',serif] text-xs text-[#1F3A5F]/70 mb-1">Kepada Yth. Bapak/Ibu/Saudara/i</p>
                        <p class="font-['Cormorant_Garamond',serif] text-lg italic mb-1">{{ guest?.name ?? 'Tamu Undangan' }}</p>
                        <p class="text-[10px] text-[#1F3A5F]/50 mb-6">*Mohon maaf apabila ada kesalahan penulisan</p>

                        <button
                            @click="openInvitation"
                            class="bg-[#1F3A5F] text-[#FAF7F1] hover:bg-[#5F84A8] hover:-translate-y-0.5 transition-all font-['Cormorant_Garamond',serif] tracking-widest text-xs uppercase px-8 py-2.5 rounded-full shadow-md"
                        >
                            Buka Undangan
                        </button>
                    </div>
                </div>
            </div>

            <div v-show="opened">

                <button
                    @click="toggleMusic"
                    class="fixed top-5 right-5 z-50 w-11 h-11 rounded-full bg-[#1F3A5F] text-[#FAF7F1] flex items-center justify-center shadow-lg"
                    :class="{ 'opacity-40': !isPlaying }"
                >
                    <span>&#9835;</span>
                </button>

                <nav class="fixed left-1/2 -translate-x-1/2 bottom-[env(safe-area-inset-bottom,24px)] mb-6 z-50 w-[calc(100%-48px)] max-w-[360px] bg-white/75 backdrop-blur-md border border-white/50 rounded-full shadow-[0_12px_40px_rgba(31,58,95,0.15)] flex justify-around items-center px-2 py-3 lg:left-auto lg:right-[30px] lg:translate-x-0">
                    <a href="#hero" data-nav class="flex flex-col items-center gap-1 w-11 transition-all text-[#1F3A5F]">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" class="w-5 h-5"><path d="M4 11.5 12 4l8 7.5"/><path d="M6 10v9h12v-9"/></svg>
                        <span class="text-[9px] tracking-[0.05em] uppercase font-medium">Home</span>
                    </a>
                    <a href="#mempelai" data-nav class="flex flex-col items-center gap-1 w-11 transition-all text-[#1F3A5F]/45">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" class="w-5 h-5"><path d="M12 20s-7-4.4-9.5-9C.8 7.4 3 4 6.5 4 9 4 11 6 12 7.3 13 6 15 4 17.5 4 21 4 23.2 7.4 21.5 11 19 15.6 12 20 12 20Z"/></svg>
                        <span class="text-[9px] tracking-[0.05em] uppercase font-medium">Pasangan</span>
                    </a>
                    <a href="#acara" data-nav class="flex flex-col items-center gap-1 w-11 transition-all text-[#1F3A5F]/45">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" class="w-5 h-5"><rect x="4" y="7" width="16" height="13" rx="1.5"/><path d="M4 11h16"/><path d="M8 4v5M16 4v5"/></svg>
                        <span class="text-[9px] tracking-[0.05em] uppercase font-medium">Acara</span>
                    </a>
                    <a href="#lovestory" data-nav class="flex flex-col items-center gap-1 w-11 transition-all text-[#1F3A5F]/45">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" class="w-5 h-5"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/></svg>
                        <span class="text-[9px] tracking-[0.05em] uppercase font-medium">Cerita</span>
                    </a>
                    <a href="#galeri" data-nav class="flex flex-col items-center gap-1 w-11 transition-all text-[#1F3A5F]/45">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" class="w-5 h-5"><path d="M4 8h3l1.5-2h7L17 8h3v11H4z"/><circle cx="12" cy="13" r="3.2"/></svg>
                        <span class="text-[9px] tracking-[0.05em] uppercase font-medium">Galeri</span>
                    </a>
                    <a href="#kado" data-nav class="flex flex-col items-center gap-1 w-11 transition-all text-[#1F3A5F]/45">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" class="w-5 h-5"><rect x="4" y="9" width="16" height="11" rx="1"/><path d="M4 9h16v4H4z"/><path d="M12 9v11"/><path d="M12 9c-1.4 0-2.6-1.4-2.6-2.7C9.4 5 10.4 4 11.5 4c1.4 0 2.5 2.3.5 5Z"/><path d="M12 9c1.4 0 2.6-1.4 2.6-2.7C14.6 5 13.6 4 12.5 4c-1.4 0-2.5 2.3-.5 5Z"/></svg>
                        <span class="text-[9px] tracking-[0.05em] uppercase font-medium">Kado</span>
                    </a>
                    <a href="#ucapan" data-nav class="flex flex-col items-center gap-1 w-11 transition-all text-[#1F3A5F]/45">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" class="w-5 h-5"><path d="M4 5h16v11H9l-4 3.5V16H4z"/></svg>
                        <span class="text-[9px] tracking-[0.05em] uppercase font-medium">Ucapan</span>
                    </a>
                </nav>

                <section id="hero" class="relative min-h-screen flex flex-col items-center justify-center px-4 py-20 overflow-hidden">
                    <div class="absolute w-[250px] pointer-events-none z-0 top-[-18px] right-[-18px] -rotate-90">
                        <img :src="asset('corner1.png')" alt="" class="w-full opacity-75 animate-[pulseZoom_5.5s_ease-in-out_infinite] [animation-delay:1.2s] brightness-[1.05]">
                    </div>
                    <div class="absolute w-[250px] pointer-events-none z-0 bottom-[-18px] left-[-18px] -scale-x-100">
                        <img :src="asset('corner1.png')" alt="" class="w-full opacity-75 animate-[pulseZoom_5.5s_ease-in-out_infinite] [animation-delay:0.6s] brightness-[1.05]">
                    </div>

                    <div class="bg-white/10 backdrop-blur-[3px] border border-white/35 shadow-[0_8px_32px_rgba(0,0,0,0.05)] rounded-3xl relative z-10 w-full max-w-md mx-auto text-center px-6 py-16">
                        <p class="reveal opacity-0 translate-y-6 transition-all duration-[900ms] font-['Cormorant_Garamond',serif] tracking-[0.4em] text-xs uppercase text-[#5F84A8] mb-6">The Wedding Of</p>

                        <div class="reveal opacity-0 translate-y-6 transition-all duration-[900ms] relative w-full max-w-[260px] mx-auto flex flex-col items-center justify-center mb-6">
                            <div class="relative w-[220px] h-[280px] rounded-[110px_110px_12px_12px] border-[3px] border-white shadow-[0_8px_24px_rgba(31,58,95,0.18),0_0_0_1px_#B9CFE1] overflow-hidden bg-[#B9CFE1]/40 z-[2]">
                                <img
                                    v-for="(photo, index) in coverPhotos"
                                    :key="photo + index"
                                    :src="photo"
                                    alt="Foto Pasangan"
                                    class="absolute inset-0 w-full h-full object-cover transition-opacity duration-1000 ease-in-out"
                                    :class="index === activeCoverIndex ? 'opacity-100' : 'opacity-0'"
                                >
                            </div>

                            <div v-if="coverPhotos.length > 1" class="flex gap-1.5 mt-2">
                                <span
                                    v-for="(photo, index) in coverPhotos"
                                    :key="'dot-' + index"
                                    class="h-1.5 w-1.5 rounded-full transition-colors"
                                    :class="index === activeCoverIndex ? 'bg-[#1F3A5F]' : 'bg-[#B9CFE1]'"
                                ></span>
                            </div>

                            <img :src="asset('corner2.png')" alt="" class="w-[180px] h-auto object-contain opacity-95 -mt-[45px] relative z-10 animate-[cornerZoom_4s_ease-in-out_infinite]">
                        </div>

                        <h1 class="reveal opacity-0 translate-y-6 transition-all duration-[900ms] font-['Tangerine',cursive] text-7xl md:text-8xl mb-4 text-[#1F3A5F]">
                            {{ groom?.nickname ?? groom?.full_name }} &amp; {{ bride?.nickname ?? bride?.full_name }}
                        </h1>
                        <p class="reveal opacity-0 translate-y-6 transition-all duration-[900ms] font-['Cormorant_Garamond',serif] text-lg text-[#1F3A5F]/80">{{ heroDateText }}</p>
                        <div class="reveal opacity-0 translate-y-6 transition-all duration-[900ms] h-px bg-gradient-to-r from-transparent via-[#C7A662] to-transparent w-32 mx-auto my-8"></div>
                        <p class="reveal opacity-0 translate-y-6 transition-all duration-[900ms] font-['Cormorant_Garamond',serif] text-sm text-[#1F3A5F]/60">Dengan penuh syukur, kami mengundang Bapak/Ibu/Saudara/i untuk hadir merayakan hari bahagia kami</p>
                    </div>
                </section>

                <section class="relative px-4 py-12 overflow-hidden">
                    <div class="bg-white/10 backdrop-blur-[3px] border border-white/35 shadow-[0_8px_32px_rgba(0,0,0,0.05)] rounded-3xl relative z-10 w-full max-w-md mx-auto text-center px-6 py-12">
                        <p class="reveal opacity-0 translate-y-6 transition-all duration-[900ms] font-['Cormorant_Garamond',serif] italic text-lg md:text-2xl mx-auto leading-relaxed">
                            "{{ quoteText }}"
                        </p>
                        <p class="reveal opacity-0 translate-y-6 transition-all duration-[900ms] font-['Cormorant_Garamond',serif] text-sm text-[#5F84A8] mt-6">{{ quoteSource }}</p>
                    </div>
                </section>

                <section id="mempelai" class="relative px-4 py-16 overflow-hidden">
                    <div class="absolute w-[250px] pointer-events-none z-0 top-[-18px] right-[-18px] -rotate-90">
                        <img :src="asset('corner1.png')" alt="" class="w-full opacity-75 animate-[pulseZoom_5.5s_ease-in-out_infinite] [animation-delay:1.2s] brightness-[1.05]">
                    </div>
                    <div class="absolute w-[250px] pointer-events-none z-0 bottom-[-18px] left-[-18px] -scale-x-100">
                        <img :src="asset('corner1.png')" alt="" class="w-full opacity-75 animate-[pulseZoom_5.5s_ease-in-out_infinite] [animation-delay:0.6s] brightness-[1.05]">
                    </div>

                    <div class="bg-white/10 backdrop-blur-[3px] border border-white/35 shadow-[0_8px_32px_rgba(0,0,0,0.05)] rounded-3xl relative z-10 w-full max-w-md mx-auto text-center px-4 py-12">
                        <p class="reveal opacity-0 translate-y-6 transition-all duration-[900ms] font-['Cormorant_Garamond',serif] tracking-[0.35em] text-xs uppercase text-[#1F3A5F] font-semibold mb-4">Assalamu'alaikum Warahmatullahi Wabarakatuh</p>
                        <p class="reveal opacity-0 translate-y-6 transition-all duration-[900ms] font-['Cormorant_Garamond',serif] text-sm text-[#1F3A5F]/80 mx-auto mb-10">Maha Suci Allah yang telah menciptakan makhluk-Nya berpasang-pasangan. Semoga ridho-Nya senantiasa mengiringi pernikahan kami.</p>

                        <div class="flex flex-col gap-6 items-center">
                            <div class="reveal opacity-0 translate-y-6 transition-all duration-[900ms]">
                                <div class="w-44 h-56 mx-auto rounded-t-full rounded-b-xl border-[3px] border-white shadow-sm flex items-center justify-center overflow-hidden">
                                    <img :src="photoUrl(bride?.photo)" :alt="bride?.full_name" class="w-full h-full object-cover">
                                </div>
                                <h3 class="font-['Tangerine',cursive] text-5xl mt-5 mb-1 text-[#1F3A5F]">{{ bride?.full_name }}</h3>
                                <p class="font-['Cormorant_Garamond',serif] text-sm font-semibold text-[#1F3A5F] mb-0.5">Putri dari</p>
                                <p class="font-['Cormorant_Garamond',serif] text-[13px] text-[#1F3A5F]/80">{{ bride?.father_name }} &amp; {{ bride?.mother_name }}</p>
                            </div>

                            <div class="reveal opacity-0 translate-y-6 transition-all duration-[900ms] my-2">
                                <span class="font-['Tangerine',cursive] text-5xl text-[#C7A662]">&amp;</span>
                            </div>

                            <div class="reveal opacity-0 translate-y-6 transition-all duration-[900ms]">
                                <div class="w-44 h-56 mx-auto rounded-t-full rounded-b-xl border-[3px] border-white shadow-sm flex items-center justify-center overflow-hidden">
                                    <img :src="photoUrl(groom?.photo)" :alt="groom?.full_name" class="w-full h-full object-cover">
                                </div>
                                <h3 class="font-['Tangerine',cursive] text-5xl mt-5 mb-1 text-[#1F3A5F]">{{ groom?.full_name }}</h3>
                                <p class="font-['Cormorant_Garamond',serif] text-sm font-semibold text-[#1F3A5F] mb-0.5">Putra dari</p>
                                <p class="font-['Cormorant_Garamond',serif] text-[13px] text-[#1F3A5F]/80">{{ groom?.father_name }} &amp; {{ groom?.mother_name }}</p>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="acara" class="relative px-4 py-16 overflow-hidden">
                    <div class="absolute w-[250px] pointer-events-none z-0 top-[-18px] left-[-18px] [transform:scale(-1,-1)]">
                        <img :src="asset('corner1.png')" alt="" class="w-full opacity-75 animate-[pulseZoom_5.5s_ease-in-out_infinite] brightness-[1.05]">
                    </div>
                    <div class="absolute w-[250px] pointer-events-none z-0 bottom-[-18px] right-[-18px] [transform:srotate(-90deg)]">
                        <img :src="asset('corner1.png')" alt="" class="w-full opacity-75 animate-[pulseZoom_5.5s_ease-in-out_infinite] [animation-delay:1.8s] brightness-[1.05]">
                    </div>

                    <div class="bg-white/10 backdrop-blur-[3px] border border-white/35 shadow-[0_8px_32px_rgba(0,0,0,0.05)] rounded-3xl relative z-10 w-full max-w-md mx-auto text-center px-6 py-16">
                        <p class="reveal opacity-0 translate-y-6 transition-all duration-[900ms] font-['Cormorant_Garamond',serif] tracking-[0.35em] text-xs uppercase text-[#5F84A8] mb-8">Save The Date</p>

                        <div class="reveal opacity-0 translate-y-6 transition-all duration-[900ms] flex justify-center gap-4 mb-14">
                            <div class="text-center">
                                <div class="text-3xl font-['Cormorant_Garamond',serif]">{{ cdDays }}</div>
                                <div class="text-[10px] tracking-widest uppercase text-[#1F3A5F]/50">Hari</div>
                            </div>
                            <div class="text-center">
                                <div class="text-3xl font-['Cormorant_Garamond',serif]">{{ cdHours }}</div>
                                <div class="text-[10px] tracking-widest uppercase text-[#1F3A5F]/50">Jam</div>
                            </div>
                            <div class="text-center">
                                <div class="text-3xl font-['Cormorant_Garamond',serif]">{{ cdMins }}</div>
                                <div class="text-[10px] tracking-widest uppercase text-[#1F3A5F]/50">Menit</div>
                            </div>
                            <div class="text-center">
                                <div class="text-3xl font-['Cormorant_Garamond',serif]">{{ cdSecs }}</div>
                                <div class="text-[10px] tracking-widest uppercase text-[#1F3A5F]/50">Detik</div>
                            </div>
                        </div>

                        <div class="grid gap-8">
                            <div v-if="akad" class="reveal opacity-0 translate-y-6 transition-all duration-[900ms] border border-[#B9CFE1]/50 rounded-xl p-8 bg-white/30">
                                <h3 class="font-['Cormorant_Garamond',serif] text-2xl mb-1">Akad Nikah</h3>
                                <p class="font-['Tangerine',cursive] text-4xl my-2 text-[#5F84A8]">{{ formatLongDate(akad.date) }}</p>
                                <p class="text-sm text-[#1F3A5F]/70 mb-4">{{ formatTime(akad.time) }} WIB &ndash; Selesai</p>
                                <p class="text-sm text-[#1F3A5F]/60 mb-4">{{ akad.address ?? akad.place }}</p>
                                <a v-if="invitation?.maps_link" :href="invitation.maps_link" target="_blank" class="inline-block text-xs tracking-widest uppercase border-b border-[#1F3A5F] pb-1">Google Maps</a>
                            </div>
                            <div v-if="resepsi" class="reveal opacity-0 translate-y-6 transition-all duration-[900ms] border border-[#B9CFE1]/50 rounded-xl p-8 bg-white/30">
                                <h3 class="font-['Cormorant_Garamond',serif] text-2xl mb-1">Resepsi</h3>
                                <p class="font-['Tangerine',cursive] text-4xl my-2 text-[#5F84A8]">{{ formatLongDate(resepsi.date) }}</p>
                                <p class="text-sm text-[#1F3A5F]/70 mb-4">{{ formatTime(resepsi.time) }} WIB</p>
                                <p class="text-sm text-[#1F3A5F]/60 mb-4">{{ resepsi.address ?? resepsi.place }}</p>
                                <a v-if="invitation?.maps_link" :href="invitation.maps_link" target="_blank" class="inline-block text-xs tracking-widest uppercase border-b border-[#1F3A5F] pb-1">Google Maps</a>
                            </div>
                        </div>

                        <a :href="calendarHref" target="_blank" class="reveal opacity-0 translate-y-6 transition-all duration-[900ms] bg-[#1F3A5F] text-[#FAF7F1] hover:bg-[#5F84A8] hover:-translate-y-0.5 transition-colors inline-block mt-12 font-['Cormorant_Garamond',serif] tracking-widest text-sm uppercase px-8 py-3 rounded-full">Simpan Tanggal</a>
                    </div>
                </section>

                <section id="lovestory" class="relative px-4 py-16 overflow-hidden">
                    <div class="absolute w-[250px] pointer-events-none z-0 top-[-18px] right-[-18px] -rotate-90">
                        <img :src="asset('corner1.png')" alt="" class="w-full opacity-75 animate-[pulseZoom_5.5s_ease-in-out_infinite] [animation-delay:1.2s] brightness-[1.05]">
                    </div>

                    <div class="bg-white/10 backdrop-blur-[3px] border border-white/35 shadow-[0_8px_32px_rgba(0,0,0,0.05)] rounded-3xl relative z-10 w-full max-w-md mx-auto text-center px-6 py-16">
                        <p class="reveal opacity-0 translate-y-6 transition-all duration-[900ms] font-['Cormorant_Garamond',serif] tracking-[0.35em] text-xs uppercase text-[#5F84A8] mb-2">Our Journey</p>
                        <h2 class="reveal opacity-0 translate-y-6 transition-all duration-[900ms] font-['Tangerine',cursive] text-5xl mb-14">Love Story</h2>

                        <div class="text-left space-y-10">
                            <div v-for="item in invitation.love_stories" :key="item.id" class="reveal opacity-0 translate-y-6 transition-all duration-[900ms] flex gap-5">
                                <div class="w-2 h-2 rounded-full bg-[#C7A662] mt-2 shrink-0"></div>
                                <div>
                                    <h4 class="font-['Cormorant_Garamond',serif] text-xl mb-1">{{ item.title }}</h4>
                                    <p class="text-sm text-[#1F3A5F]/60">{{ item.description }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="galeri" class="relative px-4 py-16 overflow-hidden">
                    <div class="absolute w-[250px] pointer-events-none z-0 top-[-18px] left-[-18px] [transform:scale(-1,-1)]">
                        <img :src="asset('corner1.png')" alt="" class="w-full opacity-75 animate-[pulseZoom_5.5s_ease-in-out_infinite] brightness-[1.05]">
                    </div>
                    <div class="absolute w-[250px] pointer-events-none z-0 bottom-[-18px] right-[-18px] [transform:srotate(-90deg)]">
                        <img :src="asset('corner1.png')" alt="" class="w-full opacity-75 animate-[pulseZoom_5.5s_ease-in-out_infinite] [animation-delay:1.8s] brightness-[1.05]">
                    </div>

                    <div class="bg-white/10 backdrop-blur-[3px] border border-white/35 shadow-[0_8px_32px_rgba(0,0,0,0.05)] rounded-3xl relative z-10 w-full max-w-md mx-auto text-center p-4">
                        <p class="reveal opacity-0 translate-y-6 transition-all duration-[900ms] font-['Cormorant_Garamond',serif] tracking-[0.35em] text-xs uppercase text-[#5F84A8] mb-2 mt-8">Gallery</p>
                        <h2 class="reveal opacity-0 translate-y-6 transition-all duration-[900ms] font-['Tangerine',cursive] text-5xl mb-8">Our Moments</h2>

                        <div class="reveal opacity-0 translate-y-6 transition-all duration-[900ms] relative bg-white/40 p-2 rounded-2xl shadow-sm border border-white/50 mb-8">
                            <div class="relative w-full aspect-[3/4] rounded-xl overflow-hidden mb-2">
                                <img :src="mainGalleryImg" alt="Gallery" class="w-full h-full object-cover">

                                <button @click="prevGallery" class="absolute left-2 top-1/2 -translate-y-1/2 w-8 h-8 flex items-center justify-center bg-black/20 text-white rounded-full backdrop-blur-sm hover:bg-black/40 transition">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-5 h-5"><path d="M15 18l-6-6 6-6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                                <button @click="nextGallery" class="absolute right-2 top-1/2 -translate-y-1/2 w-8 h-8 flex items-center justify-center bg-black/20 text-white rounded-full backdrop-blur-sm hover:bg-black/40 transition">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-5 h-5"><path d="M9 18l6-6-6-6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>

                                <button class="absolute top-3 left-3 text-white/80 hover:text-white">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-5 h-5"><path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                            </div>

                            <div ref="thumbsWrap" class="relative flex gap-2 overflow-x-auto py-1 px-1 snap-x [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden">
                                <img
                                    v-for="(img, idx) in galleryImages"
                                    :key="idx"
                                    :src="img"
                                    @click="setGallery(idx)"
                                    class="w-16 h-16 rounded-lg object-cover cursor-pointer snap-start shrink-0 transition-all"
                                    :class="idx === currentGalIdx ? 'border-2 border-[#C7A662]' : 'opacity-60 hover:opacity-100'"
                                >
                            </div>
                        </div>
                    </div>
                </section>

                <section v-if="invitation?.accounts?.length" id="kado" class="relative px-4 py-16 overflow-hidden">
                    <div class="absolute w-[250px] pointer-events-none z-0 top-[-18px] right-[-18px] -rotate-90">
                        <img :src="asset('corner1.png')" alt="" class="w-full opacity-75 animate-[pulseZoom_5.5s_ease-in-out_infinite] [animation-delay:1.2s] brightness-[1.05]">
                    </div>

                    <div class="bg-white/10 backdrop-blur-[3px] border border-white/35 shadow-[0_8px_32px_rgba(0,0,0,0.05)] rounded-3xl relative z-10 w-full max-w-md mx-auto text-center px-6 py-16">
                        <p class="reveal opacity-0 translate-y-6 transition-all duration-[900ms] font-['Cormorant_Garamond',serif] tracking-[0.35em] text-xs uppercase text-[#5F84A8] mb-2">Wedding Gift</p>
                        <h2 class="reveal opacity-0 translate-y-6 transition-all duration-[900ms] font-['Tangerine',cursive] text-5xl mb-4">Tanda Kasih</h2>
                        <p class="reveal opacity-0 translate-y-6 transition-all duration-[900ms] text-sm text-[#1F3A5F]/60 mx-auto mb-12">Doa restu Anda adalah karunia paling berarti bagi kami. Namun jika ingin memberi tanda kasih, Anda dapat melalui rekening berikut.</p>

                        <div class="grid gap-6 text-left">
                            <div v-for="account in invitation.accounts" :key="account.id" class="reveal opacity-0 translate-y-6 transition-all duration-[900ms] border border-[#B9CFE1]/50 bg-white/30 rounded-xl p-6">
                                <p class="text-xs tracking-widest uppercase text-[#5F84A8] mb-2">{{ account.bank_name }}</p>
                                <p class="font-['Cormorant_Garamond',serif] text-2xl mb-1">{{ account.account_number }}</p>
                                <p class="text-sm text-[#1F3A5F]/60 mb-4">a.n. {{ account.account_holder }}</p>
                                <button @click="copyAccount(account.account_number)" class="text-xs tracking-widest uppercase border-b border-[#1F3A5F] pb-1">Salin Nomor</button>
                            </div>
                        </div>

                        <div v-if="invitation?.gift_address" class="reveal opacity-0 translate-y-6 transition-all duration-[900ms] mt-6 border border-[#B9CFE1]/50 bg-white/30 rounded-xl p-6 text-left">
                            <p class="text-xs tracking-widest uppercase text-[#5F84A8] mb-2">Kirim Kado Fisik</p>
                            <p class="text-sm font-semibold text-[#1F3A5F]">{{ invitation.gift_address.receiver_name }}</p>
                            <p class="text-sm text-[#1F3A5F]/70 mt-1">{{ invitation.gift_address.address }}</p>
                            <p v-if="invitation.gift_address.phone" class="text-xs text-[#1F3A5F]/50 mt-1">{{ invitation.gift_address.phone }}</p>
                        </div>

                        <p class="text-xs text-[#8CA089] mt-4 h-4">{{ copyToast }}</p>
                    </div>
                </section>

                <section id="ucapan" class="relative px-4 py-16 overflow-hidden">
                    <div class="absolute w-[250px] pointer-events-none z-0 top-[-18px] left-[-18px] [transform:scale(-1,-1)]">
                        <img :src="asset('corner1.png')" alt="" class="w-full opacity-75 animate-[pulseZoom_5.5s_ease-in-out_infinite] brightness-[1.05]">
                    </div>
                    <div class="absolute w-[250px] pointer-events-none z-0 bottom-[-18px] right-[-18px] [transform:srotate(-90deg)]">
                        <img :src="asset('corner1.png')" alt="" class="w-full opacity-75 animate-[pulseZoom_5.5s_ease-in-out_infinite] [animation-delay:1.8s] brightness-[1.05]">
                    </div>

                    <div class="bg-white/10 backdrop-blur-[3px] border border-white/35 shadow-[0_8px_32px_rgba(0,0,0,0.05)] rounded-3xl relative z-10 w-full max-w-md mx-auto text-center px-6 py-16">
                        <p class="reveal opacity-0 translate-y-6 transition-all duration-[900ms] font-['Cormorant_Garamond',serif] tracking-[0.35em] text-xs uppercase text-[#5F84A8] mb-2">RSVP</p>
                        <h2 class="reveal opacity-0 translate-y-6 transition-all duration-[900ms] font-['Tangerine',cursive] text-5xl mb-10">Ucapan &amp; Doa</h2>

                        <form @submit.prevent="submitWish" class="reveal opacity-0 translate-y-6 transition-all duration-[900ms] text-left space-y-4 border border-[#B9CFE1] rounded-2xl p-6 bg-white/30">
                            <div>
                                <input v-model="form.name" required type="text" placeholder="Nama" class="w-full border border-[#B9CFE1] rounded-lg px-4 py-3 bg-white/70 text-sm focus:outline-none focus:border-[#5F84A8] placeholder-[#1F3A5F]/40">
                            </div>
                            <div>
                                <textarea v-model="form.message" required rows="3" placeholder="Ucapan" class="w-full border border-[#B9CFE1] rounded-lg px-4 py-3 bg-white/70 text-sm focus:outline-none focus:border-[#5F84A8] placeholder-[#1F3A5F]/40"></textarea>
                            </div>

                            <div class="pt-2 pb-2">
                                <div class="flex items-center gap-4 mb-4">
                                    <hr class="flex-grow border-[#B9CFE1]">
                                    <span class="text-xs font-['Cormorant_Garamond',serif] font-bold text-[#1F3A5F]">Konfirmasi Kehadiran</span>
                                    <hr class="flex-grow border-[#B9CFE1]">
                                </div>

                                <div class="flex gap-3">
                                    <label class="flex-1 relative">
                                        <input v-model="form.attendance" type="radio" name="attendance" value="hadir" required class="peer sr-only">
                                        <div class="w-full text-center py-2.5 rounded-full bg-black/5 text-[#1F3A5F]/50 text-sm font-semibold cursor-pointer peer-checked:bg-[#B9CFE1] peer-checked:text-[#1F3A5F] transition-all flex items-center justify-center gap-1.5">
                                            <svg viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                                            Hadir
                                        </div>
                                    </label>
                                    <label class="flex-1 relative">
                                        <input v-model="form.attendance" type="radio" name="attendance" value="tidak_hadir" class="peer sr-only">
                                        <div class="w-full text-center py-2.5 rounded-full bg-black/5 text-[#1F3A5F]/50 text-sm font-semibold cursor-pointer peer-checked:bg-gray-300 peer-checked:text-gray-700 transition-all flex items-center justify-center gap-1.5">
                                            <svg viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm5 13.59L15.59 17 12 13.41 8.41 17 7 15.59 10.59 12 7 8.41 8.41 7 12 10.59 15.59 7 17 8.41 13.41 12 17 15.59z"/></svg>
                                            Tidak Hadir
                                        </div>
                                    </label>
                                </div>
                            </div>
                            <button type="submit" :disabled="form.processing" class="bg-[#1F3A5F] text-[#FAF7F1] hover:bg-[#5F84A8] hover:-translate-y-0.5 transition-all disabled:opacity-60 w-full font-['Cormorant_Garamond',serif] tracking-widest text-sm uppercase px-8 py-3 rounded-full shadow-sm">Kirim</button>
                            <p class="text-xs text-[#1F3A5F] font-semibold text-center h-4">{{ rsvpMsg }}</p>
                        </form>

                        <div class="mt-14 relative">
                            <div class="space-y-4 text-left max-h-[360px] overflow-y-auto pr-2 [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden">
                                <div v-if="!wishes.length" class="text-center text-sm text-[#1F3A5F]/40 py-6">Jadilah yang pertama mengirim ucapan.</div>

                                <div v-for="wish in pagedWishes" :key="wish.id" class="bg-white/80 p-4 rounded-xl shadow-sm border border-white flex gap-4">
                                    <div
                                        class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-xs shrink-0"
                                        :class="wish.attendance === 'hadir' ? 'bg-[#B9CFE1] text-[#1F3A5F]' : 'bg-gray-200 text-gray-500'"
                                    >
                                        {{ initials(wish.name) }}
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-bold text-[13px] text-[#1F3A5F] mb-0.5 flex items-center justify-between">
                                            <span>{{ wish.name }}</span>
                                            <span
                                                class="text-[9px] px-2 py-0.5 rounded-full font-normal ml-2"
                                                :class="wish.attendance === 'hadir' ? 'bg-[#B9CFE1] text-[#1F3A5F]' : 'bg-gray-200 text-gray-600'"
                                            >
                                                {{ attendanceLabel(wish.attendance) }}
                                            </span>
                                        </p>
                                        <p class="text-[13px] text-[#1F3A5F]/80 leading-relaxed">{{ wish.message }}</p>
                                    </div>
                                </div>
                            </div>

                            <div v-if="wishes.length" class="flex items-center justify-center gap-2 mt-6">
                                <button
                                    @click="goToWishesPage(wishesPage - 1)"
                                    :disabled="wishesPage === 1"
                                    class="w-8 h-8 rounded-full flex items-center justify-center border border-[#B9CFE1] text-[#1F3A5F] transition-all"
                                    :class="wishesPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-[#B9CFE1]/20'"
                                >
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M15 18l-6-6 6-6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                                <button
                                    v-for="p in wishesPageNumbers"
                                    :key="p"
                                    @click="goToWishesPage(p)"
                                    class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-semibold transition-all"
                                    :class="p === wishesPage ? 'bg-[#1F3A5F] text-white' : 'border border-[#B9CFE1] text-[#1F3A5F] hover:bg-[#B9CFE1]/20'"
                                >
                                    {{ p }}
                                </button>
                                <button
                                    @click="goToWishesPage(wishesPage + 1)"
                                    :disabled="wishesPage === totalWishesPages"
                                    class="w-8 h-8 rounded-full flex items-center justify-center border border-[#B9CFE1] text-[#1F3A5F] transition-all"
                                    :class="wishesPage === totalWishesPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-[#B9CFE1]/20'"
                                >
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M9 18l6-6-6-6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="relative px-4 py-16 overflow-hidden">
                    <div class="absolute w-[250px] pointer-events-none z-0 top-[-18px] left-[-18px] [transform:scale(-1,-1)]">
                        <img :src="asset('corner1.png')" alt="" class="w-full opacity-75 animate-[pulseZoom_5.5s_ease-in-out_infinite] brightness-[1.05]">
                    </div>
                    <div class="absolute w-[250px] pointer-events-none z-0 top-[-18px] right-[-18px] -rotate-90">
                        <img :src="asset('corner1.png')" alt="" class="w-full opacity-75 animate-[pulseZoom_5.5s_ease-in-out_infinite] [animation-delay:1.2s] brightness-[1.05]">
                    </div>

                    <div class="bg-white/10 backdrop-blur-[3px] border border-white/35 shadow-[0_8px_32px_rgba(0,0,0,0.05)] rounded-3xl relative z-10 w-full max-w-md mx-auto text-center px-6 py-16">
                        <p class="reveal opacity-0 translate-y-6 transition-all duration-[900ms] font-['Cormorant_Garamond',serif] text-lg mx-auto text-[#1F3A5F]/70">Merupakan suatu kebahagiaan dan kehormatan bagi kami, apabila Bapak/Ibu/Saudara/i berkenan hadir dan memberikan doa restu.</p>
                        <p class="reveal opacity-0 translate-y-6 transition-all duration-[900ms] font-['Cormorant_Garamond',serif] text-sm mt-6 mb-10">Wassalamu'alaikum Warahmatullahi Wabarakatuh</p>
                        <p class="reveal opacity-0 translate-y-6 transition-all duration-[900ms] text-xs tracking-widest uppercase text-[#5F84A8] mb-2">Kami Yang Berbahagia</p>
                        <h2 class="reveal opacity-0 translate-y-6 transition-all duration-[900ms] font-['Tangerine',cursive] text-6xl">
                            {{ groom?.nickname ?? groom?.full_name }} &amp; {{ bride?.nickname ?? bride?.full_name }}
                        </h2>
                    </div>
                </section>

                <footer class="text-center py-8 border-t border-[#B9CFE1]">
                    <p class="text-[11px] tracking-widest uppercase text-[#1F3A5F]/40">Made with love &middot; {{ new Date().getFullYear() }}</p>
                </footer>
            </div>
        </div>
    </div>
</template>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;1,400&family=Jost:wght@300;400;500;600&family=Tangerine:wght@400;700&display=swap');

    @keyframes pulseZoom {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.09); }
    }
    @keyframes cornerZoom {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.12); }
    }
    @keyframes fall {
        0% { transform: translateY(0) rotate(0deg); opacity: 0.9; }
        100% { transform: translateY(110vh) rotate(360deg); opacity: 0.2; }
    }
    ::selection { background: #B9CFE1; color: #1F3A5F; }
</style>