import { onMounted, onUnmounted } from 'vue'

/**
 * Animasi fade-up saat elemen ber-class "reveal" masuk viewport.
 * Elemen di template harus sudah punya class awal:
 * "reveal opacity-0 translate-y-6 transition-all duration-[900ms]"
 *
 * @param {string} selector
 * @param {IntersectionObserverInit} options
 *
 * @example
 * useRevealOnScroll() // cukup dipanggil sekali di setup(), tanpa perlu return apapun
 */
export function useRevealOnScroll(selector = '.reveal', options = { threshold: 0.15 }) {
    let observer = null

    onMounted(() => {
        observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('opacity-100', 'translate-y-0')
                }
            })
        }, options)

        document.querySelectorAll(selector).forEach((el) => observer.observe(el))
    })

    onUnmounted(() => {
        if (observer) observer.disconnect()
    })
}
