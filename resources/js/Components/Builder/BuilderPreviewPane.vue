<script setup>
defineProps({
    data: Object,
})

const formatDate = (value) => {
    if (!value) return null
    return new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }).format(new Date(value))
}
</script>

<template>
    <div class="mx-auto w-full max-w-sm overflow-hidden rounded-[2rem] border-8 border-slate-900 bg-white shadow-2xl">
        <div class="max-h-[720px] overflow-y-auto">
            <div class="flex flex-col items-center bg-gradient-to-b from-pink-50 to-white px-6 py-12 text-center">
                <p class="text-xs font-medium uppercase tracking-[0.3em] text-pink-500">The Wedding Of</p>

                <p class="mt-6 font-serif text-3xl text-slate-800">
                    {{ data.groom?.nickname || data.groom?.full_name || 'Mempelai Pria' }}
                </p>
                <p class="my-1 font-serif text-lg text-pink-400">&amp;</p>
                <p class="font-serif text-3xl text-slate-800">
                    {{ data.bride?.nickname || data.bride?.full_name || 'Mempelai Wanita' }}
                </p>

                <div class="mx-auto mt-6 h-px w-12 bg-pink-200"></div>

                <p v-if="data.resepsi?.date" class="mt-6 text-sm text-slate-500">
                    {{ formatDate(data.resepsi.date) }}
                </p>
                <p v-else class="mt-6 text-sm text-slate-300">Tanggal resepsi belum diatur</p>
            </div>

            <div v-if="data.quote_text" class="border-t border-slate-100 px-6 py-8 text-center">
                <p class="font-serif text-sm italic leading-relaxed text-slate-600">
                    &ldquo;{{ data.quote_text }}&rdquo;
                </p>
                <p v-if="data.quote_source" class="mt-2 text-xs text-slate-400">{{ data.quote_source }}</p>
            </div>

            <div class="border-t border-slate-100 px-6 py-8">
                <p class="text-center text-xs font-medium uppercase tracking-widest text-slate-400">Acara</p>

                <div v-if="data.akad?.date" class="mt-4 text-center">
                    <p class="text-sm font-medium text-slate-700">Akad Nikah</p>
                    <p class="text-sm text-slate-500">{{ formatDate(data.akad.date) }} · {{ data.akad.time }}</p>
                    <p class="text-xs text-slate-400">{{ data.akad.place }}</p>
                </div>

                <div v-if="data.resepsi?.date" class="mt-4 text-center">
                    <p class="text-sm font-medium text-slate-700">Resepsi</p>
                    <p class="text-sm text-slate-500">{{ formatDate(data.resepsi.date) }} · {{ data.resepsi.time }}</p>
                    <p class="text-xs text-slate-400">{{ data.resepsi.place }}</p>
                </div>
            </div>

            <div v-if="data.galleries?.length" class="border-t border-slate-100 px-6 py-8">
                <p class="text-center text-xs font-medium uppercase tracking-widest text-slate-400">Galeri</p>
                <div class="mt-4 grid grid-cols-3 gap-1.5">
                    <img
                        v-for="photo in data.galleries.slice(0, 6)"
                        :key="photo.id"
                        :src="`/storage/${photo.photo}`"
                        class="aspect-square rounded-lg object-cover"
                    />
                </div>
            </div>

            <div class="border-t border-slate-100 px-6 py-6 text-center">
                <p class="text-xs text-slate-300">
                    Preview sederhana · Tema: {{ data.theme?.name ?? 'Belum dipilih' }}
                </p>
            </div>
        </div>
    </div>
</template>