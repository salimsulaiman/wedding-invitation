<script setup>
import { useForm, router } from '@inertiajs/vue3'
import { watch, ref, computed } from 'vue'
import { Trash2, Plus, Pencil, X, ImagePlus } from 'lucide-vue-next'
import { useFormatters } from '@/composables/useFormatters'

const props = defineProps({
    invitation: Object,
    live: Object,
})

const allowCustomPhotos = computed(() => props.invitation.theme?.category?.allow_custom_photos ?? true)

const {formatDate} = useFormatters()

const groom = props.invitation.couples?.find((c) => c.type === 'groom') ?? {}
const bride = props.invitation.couples?.find((c) => c.type === 'bride') ?? {}
const akad = props.invitation.events?.find((e) => e.type === 'akad') ?? {}
const resepsi = props.invitation.events?.find((e) => e.type === 'resepsi') ?? {}

const form = useForm({
    name: props.invitation.name ?? '',
    quote_text: props.invitation.quote_text ?? '',
    quote_source: props.invitation.quote_source ?? '',
    maps_link: props.invitation.maps_link ?? '',
    streaming_link: props.invitation.streaming_link ?? '',
    akad: {
        date: akad.date ?? '',
        time: akad.time ?? '',
        place: akad.place ?? '',
        address: akad.address ?? '',
    },
    resepsi: {
        date: resepsi.date ?? '',
        time: resepsi.time ?? '',
        place: resepsi.place ?? '',
        address: resepsi.address ?? '',
    },
    groom: {
        full_name: groom.full_name ?? '',
        nickname: groom.nickname ?? '',
        father_name: groom.father_name ?? '',
        mother_name: groom.mother_name ?? '',
        photo: null,
    },
    bride: {
        full_name: bride.full_name ?? '',
        nickname: bride.nickname ?? '',
        father_name: bride.father_name ?? '',
        mother_name: bride.mother_name ?? '',
        photo: null,
    },
})

watch(form, () => {
    props.live.name = form.name
    props.live.quote_text = form.quote_text
    props.live.quote_source = form.quote_source
    props.live.akad = { ...form.akad }
    props.live.resepsi = { ...form.resepsi }
    props.live.groom = { ...form.groom }
    props.live.bride = { ...form.bride }
}, { deep: true, immediate: true })

const submit = () => {
    form.put(route('builder.couple-event.update', props.invitation.id), {
        forceFormData: true,
        preserveScroll: true,
    })
}

/* ================= Love Story ================= */

const loveStoryForm = useForm({
    title: '',
    story_date: '',
    description: '',
    photo: null,
})

const editingId = ref(null)

const resetLoveStoryForm = () => {
    loveStoryForm.reset()
    loveStoryForm.clearErrors()
    editingId.value = null
}

const editLoveStory = (story) => {
    editingId.value = story.id
    loveStoryForm.title = story.title
    loveStoryForm.story_date = story.story_date ?? ''
    loveStoryForm.description = story.description ?? ''
    loveStoryForm.photo = null
}

const submitLoveStory = () => {
    if (editingId.value) {
        loveStoryForm.transform((data) => ({ ...data, _method: 'put' })).post(
            route('builder.love-stories.update', [props.invitation.id, editingId.value]),
            {
                forceFormData: true,
                preserveScroll: true,
                onSuccess: resetLoveStoryForm,
            }
        )
    } else {
        loveStoryForm.post(route('builder.love-stories.store', props.invitation.id), {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: resetLoveStoryForm,
        })
    }
}

const destroyLoveStory = (story) => {
    if (confirm(`Hapus love story "${story.title}"?`)) {
        router.delete(route('builder.love-stories.destroy', [props.invitation.id, story.id]), {
            preserveScroll: true,
        })
    }
}
</script>

<template>
    <form class="space-y-8" @submit.prevent="submit">
        <div>
            <h3 class="text-sm font-semibold text-slate-900">Informasi Pokok Acara</h3>
            <div class="mt-4 space-y-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600">Nama Acara Undangan</label>
                    <input
                        v-model="form.name"
                        type="text"
                        placeholder="The Wedding of Andi & Siti"
                        class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                    />
                    <p v-if="form.errors.name" class="mt-1 text-xs text-red-600">{{ form.errors.name }}</p>
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600">Link Google Maps</label>
                    <input
                        v-model="form.maps_link"
                        type="url"
                        placeholder="https://maps.google.com/..."
                        class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                    />
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600">Link Live Streaming (opsional)</label>
                    <input
                        v-model="form.streaming_link"
                        type="url"
                        placeholder="https://youtube.com/..."
                        class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                    />
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600">Kutipan Pembuka</label>
                    <textarea
                        v-model="form.quote_text"
                        rows="2"
                        placeholder="Teks ayat/kutipan"
                        class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                    ></textarea>
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600">Sumber Kutipan</label>
                    <input
                        v-model="form.quote_source"
                        type="text"
                        placeholder="Cth: QS. Ar-Rum: 21"
                        class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                    />
                </div>
            </div>
        </div>

        <div>
            <h3 class="text-sm font-semibold text-slate-900">Akad Nikah / Pemberkatan</h3>
            <div class="mt-4 space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-slate-600">Tanggal</label>
                        <input
                            v-model="form.akad.date"
                            type="date"
                            class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                        />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600">Pukul</label>
                        <input
                            v-model="form.akad.time"
                            type="time"
                            class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                        />
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600">Tempat</label>
                    <input
                        v-model="form.akad.place"
                        type="text"
                        class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                    />
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600">Alamat</label>
                    <textarea
                        v-model="form.akad.address"
                        rows="2"
                        class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                    ></textarea>
                </div>
            </div>
        </div>

        <div>
            <h3 class="text-sm font-semibold text-slate-900">Acara Resepsi</h3>
            <div class="mt-4 space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-slate-600">Tanggal</label>
                        <input
                            v-model="form.resepsi.date"
                            type="date"
                            class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                        />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600">Pukul</label>
                        <input
                            v-model="form.resepsi.time"
                            type="time"
                            class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                        />
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600">Tempat</label>
                    <input
                        v-model="form.resepsi.place"
                        type="text"
                        class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                    />
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600">Alamat</label>
                    <textarea
                        v-model="form.resepsi.address"
                        rows="2"
                        class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                    ></textarea>
                </div>
            </div>
        </div>

        <div>
            <h3 class="text-sm font-semibold text-slate-900">Mempelai Pria</h3>
            <div class="mt-4 space-y-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600">Nama Lengkap</label>
                    <input
                        v-model="form.groom.full_name"
                        type="text"
                        class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                    />
                    <p v-if="form.errors['groom.full_name']" class="mt-1 text-xs text-red-600">{{ form.errors['groom.full_name'] }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600">Nama Panggilan</label>
                    <input
                        v-model="form.groom.nickname"
                        type="text"
                        class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                    />
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-slate-600">Nama Ayah</label>
                        <input
                            v-model="form.groom.father_name"
                            type="text"
                            class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                        />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600">Nama Ibu</label>
                        <input
                            v-model="form.groom.mother_name"
                            type="text"
                            class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                        />
                    </div>
                </div>
                <div v-if="allowCustomPhotos">
                    <label class="block text-xs font-medium text-slate-600">Foto</label>
                    <input
                        type="file"
                        accept="image/*"
                        class="mt-1 block w-full text-xs text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-pink-50 file:px-3 file:py-1.5 file:text-xs file:font-medium file:text-pink-700 hover:file:bg-pink-100"
                        @change="form.groom.photo = $event.target.files[0]"
                    />
                </div>
                <p v-else class="text-xs text-slate-400">Upload foto tidak tersedia untuk paket tema ini.</p>
            </div>
        </div>

        <div>
            <h3 class="text-sm font-semibold text-slate-900">Mempelai Wanita</h3>
            <div class="mt-4 space-y-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600">Nama Lengkap</label>
                    <input
                        v-model="form.bride.full_name"
                        type="text"
                        class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                    />
                    <p v-if="form.errors['bride.full_name']" class="mt-1 text-xs text-red-600">{{ form.errors['bride.full_name'] }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600">Nama Panggilan</label>
                    <input
                        v-model="form.bride.nickname"
                        type="text"
                        class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                    />
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-slate-600">Nama Ayah</label>
                        <input
                            v-model="form.bride.father_name"
                            type="text"
                            class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                        />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600">Nama Ibu</label>
                        <input
                            v-model="form.bride.mother_name"
                            type="text"
                            class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
                        />
                    </div>
                </div>
                <div v-if="allowCustomPhotos">
                    <label class="block text-xs font-medium text-slate-600">Foto</label>
                    <input
                        type="file"
                        accept="image/*"
                        class="mt-1 block w-full text-xs text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-pink-50 file:px-3 file:py-1.5 file:text-xs file:font-medium file:text-pink-700 hover:file:bg-pink-100"
                        @change="form.bride.photo = $event.target.files[0]"
                    />
                </div>
                <p v-else class="text-xs text-slate-400">Upload foto tidak tersedia untuk paket tema ini.</p>
            </div>
        </div>

        <button
            type="submit"
            :disabled="form.processing"
            class="w-full rounded-lg bg-pink-600 py-2.5 text-sm font-semibold text-white transition hover:bg-pink-700 disabled:opacity-60"
        >
            Simpan Mempelai & Acara
        </button>
    </form>

    <div class="mt-8 border-t border-slate-100 pt-8">
        <h3 class="text-sm font-semibold text-slate-900">Love Story</h3>
        <p class="mt-1 text-sm text-slate-500">Ceritakan momen-momen penting perjalanan kalian.</p>

        <div class="mt-4 space-y-3">
            <div
                v-for="story in invitation.love_stories"
                :key="story.id"
                class="flex items-start gap-3 rounded-lg border border-slate-200 p-3"
            >
                <img
                    v-if="story.photo"
                    :src="`/storage/${story.photo}`"
                    class="h-14 w-14 shrink-0 rounded-lg object-cover"
                />
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-slate-900">{{ story.title }}</p>
                    <p v-if="story.story_date" class="text-xs text-slate-400">{{ formatDate(story.story_date) }}</p>
                    <p v-if="story.description" class="mt-1 line-clamp-2 text-xs text-slate-500">{{ story.description }}</p>
                </div>
                <div class="flex shrink-0 items-center gap-1">
                    <button class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-600" @click="editLoveStory(story)">
                        <Pencil class="h-3.5 w-3.5" />
                    </button>
                    <button class="rounded-lg p-1.5 text-slate-400 hover:bg-red-50 hover:text-red-600" @click="destroyLoveStory(story)">
                        <Trash2 class="h-3.5 w-3.5" />
                    </button>
                </div>
            </div>

            <p v-if="!invitation.love_stories?.length" class="rounded-lg border border-dashed border-slate-200 py-6 text-center text-sm text-slate-400">
                Belum ada love story ditambahkan.
            </p>
        </div>

        <form class="mt-4 space-y-3 rounded-lg border border-dashed border-slate-300 p-4" @submit.prevent="submitLoveStory">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium text-slate-600">
                    {{ editingId ? 'Edit Love Story' : 'Tambah Love Story' }}
                </p>
                <button v-if="editingId" type="button" class="text-slate-400 hover:text-slate-600" @click="resetLoveStoryForm">
                    <X class="h-4 w-4" />
                </button>
            </div>

            <input
                v-model="loveStoryForm.title"
                type="text"
                placeholder="Judul, cth: Awal Bertemu"
                class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
            />
            <p v-if="loveStoryForm.errors.title" class="text-xs text-red-600">{{ loveStoryForm.errors.title }}</p>

            <input
                v-model="loveStoryForm.story_date"
                type="date"
                class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
            />

            <textarea
                v-model="loveStoryForm.description"
                rows="3"
                placeholder="Ceritakan momennya..."
                class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500/30"
            ></textarea>

            <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-slate-300 px-3 py-2 text-xs text-slate-500 hover:bg-slate-50">
                <ImagePlus class="h-3.5 w-3.5" />
                {{ loveStoryForm.photo ? loveStoryForm.photo.name : 'Pilih foto (opsional)' }}
                <input type="file" accept="image/*" class="hidden" @change="loveStoryForm.photo = $event.target.files[0]" />
            </label>

            <button
                type="submit"
                :disabled="loveStoryForm.processing"
                class="flex w-full items-center justify-center gap-1.5 rounded-lg bg-pink-600 py-2 text-sm font-semibold text-white transition hover:bg-pink-700 disabled:opacity-60"
            >
                <Plus class="h-4 w-4" />
                {{ editingId ? 'Simpan Perubahan' : 'Tambah Love Story' }}
            </button>
        </form>
    </div>
</template>