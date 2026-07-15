import { onMounted, onUnmounted } from 'vue'

/**
 * Highlight item navigasi berdasarkan section yang sedang aktif saat scroll.
 * Nav item di template butuh attribute `data-nav` dan `href="#section-id"`.
 *
 * @example
 * useScrollSpy({
 *     activeClasses: ['text-[#1F3A5F]', '-translate-y-[3px]'],
 *     inactiveClasses: ['text-[#1F3A5F]/45'],
 * })
 */
export function useScrollSpy({
    sectionSelector = 'section[id]',
    navSelector = '[data-nav]',
    activeClasses = [],
    inactiveClasses = [],
} = {}) {
    let handler = null

    onMounted(() => {
        handler = () => {
            let currentId = ''
            const scrollPos = window.scrollY
            const viewportHeight = window.innerHeight

            document.querySelectorAll(sectionSelector).forEach((section) => {
                if (scrollPos >= section.offsetTop - viewportHeight / 2) {
                    currentId = section.id
                }
            })

            document.querySelectorAll(navSelector).forEach((nav) => {
                const isActive = nav.getAttribute('href') === '#' + currentId
                activeClasses.forEach((c) => nav.classList.toggle(c, isActive))
                inactiveClasses.forEach((c) => nav.classList.toggle(c, !isActive))
            })
        }

        window.addEventListener('scroll', handler)
    })

    onUnmounted(() => {
        if (handler) window.removeEventListener('scroll', handler)
    })
}
