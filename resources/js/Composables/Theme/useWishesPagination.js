import { ref, computed } from 'vue'

/**
 * Pagination sederhana untuk daftar ucapan & doa.
 *
 * @param {import('vue').ComputedRef<Array>} wishes
 * @param {number} perPage
 *
 * @example
 * const { page, pagedWishes, pageNumbers, totalPages, goToPage } = useWishesPagination(wishes, 5)
 */
export function useWishesPagination(wishes, perPage = 5) {
    const page = ref(1)

    const totalPages = computed(() => Math.max(1, Math.ceil(wishes.value.length / perPage)))

    const pagedWishes = computed(() => {
        const start = (page.value - 1) * perPage
        return wishes.value.slice(start, start + perPage)
    })

    const pageNumbers = computed(() =>
        Array.from({ length: totalPages.value }, (_, i) => i + 1)
    )

    const goToPage = (target) => {
        if (target < 1 || target > totalPages.value) return
        page.value = target
    }

    return { page, totalPages, pagedWishes, pageNumbers, goToPage }
}
