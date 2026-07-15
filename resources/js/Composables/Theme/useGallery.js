import { ref, computed, nextTick } from 'vue'

/**
 * Galeri foto dengan main image + strip thumbnail yang auto-scroll
 * mengikuti foto yang sedang aktif.
 *
 * @param {import('vue').ComputedRef<string[]>} images
 *
 * @example
 * const { currentIndex, mainImage, thumbsWrap, next, prev, set } = useGallery(galleryImages)
 * // di template: <div ref="thumbsWrap"> ... </div>
 */
export function useGallery(images) {
    const currentIndex = ref(0)
    const thumbsWrap = ref(null)

    const mainImage = computed(() => images.value[currentIndex.value])

    const scrollThumbIntoView = () => {
        nextTick(() => {
            const wrap = thumbsWrap.value
            if (!wrap) return
            const thumb = wrap.children[currentIndex.value]
            if (!thumb) return
            const targetLeft = thumb.offsetLeft - wrap.clientWidth / 2 + thumb.clientWidth / 2
            wrap.scrollTo({ left: targetLeft, behavior: 'smooth' })
        })
    }

    const next = () => {
        currentIndex.value = (currentIndex.value + 1) % images.value.length
        scrollThumbIntoView()
    }

    const prev = () => {
        currentIndex.value = (currentIndex.value - 1 + images.value.length) % images.value.length
        scrollThumbIntoView()
    }

    const set = (index) => {
        currentIndex.value = index
        scrollThumbIntoView()
    }

    return { currentIndex, mainImage, thumbsWrap, next, prev, set }
}
