import { ref, onMounted, onUnmounted } from 'vue'

/**
 * Carousel foto cover yang berganti otomatis tiap interval tertentu.
 * Dipakai untuk foto di halaman sampul (opening screen) & hero section.
 *
 * @param {import('vue').ComputedRef<string[]>} photos
 * @param {number} intervalMs
 *
 * @example
 * const { activeIndex } = useCoverCarousel(coverPhotos, 3500)
 */
export function useCoverCarousel(photos, intervalMs = 3500) {
    const activeIndex = ref(0)
    let interval = null

    onMounted(() => {
        if (photos.value.length > 1) {
            interval = setInterval(() => {
                activeIndex.value = (activeIndex.value + 1) % photos.value.length
            }, intervalMs)
        }
    })

    onUnmounted(() => {
        if (interval) clearInterval(interval)
    })

    return { activeIndex }
}
