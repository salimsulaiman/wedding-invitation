import { ref, onMounted, onUnmounted } from 'vue'
import { spawnPetals } from './usePetals'

/**
 * Orkestrator state "opened" untuk undangan: mengunci scroll body sebelum
 * dibuka, lalu saat dibuka menyalakan musik & menaburkan petals.
 *
 * @param {object} options
 * @param {{ play: () => void }} [options.musicPlayer] - hasil dari useMusicPlayer()
 * @param {import('vue').Ref<HTMLElement | null>} [options.petalsContainer]
 * @param {object} [options.petalsOptions]
 *
 * @example
 * const petalsContainer = ref(null)
 * const musicPlayer = useMusicPlayer(audioEl)
 * const { opened, open } = useOpenInvitation({ musicPlayer, petalsContainer })
 * // <button @click="open">Buka Undangan</button>
 */
export function useOpenInvitation({ musicPlayer, petalsContainer, petalsOptions } = {}) {
    const opened = ref(false)

    const open = () => {
        opened.value = true
        document.body.style.overflow = 'auto'
        musicPlayer?.play()
        if (petalsContainer) {
            spawnPetals(petalsContainer.value, petalsOptions)
        }
    }

    onMounted(() => {
        document.body.style.overflow = 'hidden'
    })

    onUnmounted(() => {
        document.body.style.overflow = 'auto'
    })

    return { opened, open }
}
