import { ref, onMounted, onUnmounted } from 'vue'

/**
 * Countdown mundur ke tanggal & jam acara (akad/resepsi).
 *
 * @param {() => ({ date: string, time?: string } | null | undefined)} getTarget
 *        Function yang mengembalikan object event yang jadi acuan countdown.
 *        Dibuat sebagai function (bukan value langsung) supaya tetap reaktif
 *        terhadap perubahan computed di komponen pemanggil.
 *
 * @example
 * const { days, hours, minutes, seconds } = useCountdown(() => akad.value ?? resepsi.value)
 */
export function useCountdown(getTarget) {
    const days = ref('00')
    const hours = ref('00')
    const minutes = ref('00')
    const seconds = ref('00')

    let interval = null

    const pad = (n) => String(n).padStart(2, '0')

    const getTargetTimestamp = () => {
        const source = getTarget()
        if (!source?.date) return null

        const target = new Date(source.date)

        if (source.time) {
            const [hour, minute, second] = source.time.split(':')
            target.setHours(Number(hour), Number(minute), Number(second ?? 0), 0)
        }

        return target.getTime()
    }

    const update = () => {
        const target = getTargetTimestamp()
        if (!target) return

        const distance = target - Date.now()

        if (distance < 0) {
            days.value = '00'
            hours.value = '00'
            minutes.value = '00'
            seconds.value = '00'
            return
        }

        days.value = pad(Math.floor(distance / (1000 * 60 * 60 * 24)))
        hours.value = pad(Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)))
        minutes.value = pad(Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60)))
        seconds.value = pad(Math.floor((distance % (1000 * 60)) / 1000))
    }

    onMounted(() => {
        update()
        interval = setInterval(update, 1000)
    })

    onUnmounted(() => {
        if (interval) clearInterval(interval)
    })

    return { days, hours, minutes, seconds }
}
