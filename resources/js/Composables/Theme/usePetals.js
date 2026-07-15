/**
 * Spawn elemen "kelopak jatuh" ke dalam container tertentu.
 * Bukan composable reaktif — murni DOM helper, dipanggil sekali saat undangan dibuka.
 * Elemen SVG di dalamnya butuh @keyframes "fall" yang sudah didefinisikan di <style> tema.
 *
 * @param {HTMLElement | null} container
 * @param {object} [options]
 * @param {number} [options.count]
 * @param {string} [options.color]
 * @param {number} [options.minDuration]
 * @param {number} [options.maxDuration]
 *
 * @example
 * spawnPetals(petalsContainer.value, { color: '#6C90B3' })
 */
export function spawnPetals(container, { count = 10, color = '#6C90B3', minDuration = 10, maxDuration = 20 } = {}) {
    if (!container) return

    for (let i = 0; i < count; i++) {
        const petal = document.createElement('div')
        petal.className = 'absolute top-[-10%] opacity-85 pointer-events-none animate-[fall_linear_infinite]'
        petal.style.left = `${Math.random() * 100}%`
        petal.style.width = `${10 + Math.random() * 14}px`
        petal.style.animationDuration = `${minDuration + Math.random() * (maxDuration - minDuration)}s`
        petal.style.animationDelay = `${Math.random() * 10}s`
        petal.innerHTML = `<svg viewBox="0 0 24 24" fill="${color}"><path d="M12 2c3 3 3 7 0 10-3-3-3-7 0-10z"/></svg>`
        container.appendChild(petal)
    }
}
