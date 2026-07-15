import { ref } from 'vue'

/**
 * Kontrol play/pause audio backsound.
 *
 * @param {import('vue').Ref<HTMLAudioElement | null>} audioEl
 *
 * @example
 * const audioEl = ref(null)
 * const musicPlayer = useMusicPlayer(audioEl)
 * // <audio ref="audioEl" :src="musicSrc" loop></audio>
 */
export function useMusicPlayer(audioEl) {
    const isPlaying = ref(false)

    const play = () => {
        if (!audioEl.value) return
        audioEl.value
            .play()
            .then(() => {
                isPlaying.value = true
            })
            .catch(() => {})
    }

    const toggle = () => {
        if (!audioEl.value) return
        if (isPlaying.value) {
            audioEl.value.pause()
        } else {
            audioEl.value.play()
        }
        isPlaying.value = !isPlaying.value
    }

    return { isPlaying, play, toggle }
}
