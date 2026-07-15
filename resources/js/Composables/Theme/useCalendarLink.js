import { computed } from 'vue'

/**
 * Generate link "Simpan ke Google Calendar" berdasarkan data acara.
 *
 * @param {object} options
 * @param {import('vue').ComputedRef<{date: string, time?: string} | null>} options.event
 * @param {import('vue').ComputedRef<string>} options.title
 * @param {import('vue').ComputedRef<string>} options.details
 * @param {number} [options.durationHours]
 *
 * @example
 * const calendarHref = useCalendarLink({
 *     event: computed(() => resepsi.value ?? akad.value),
 *     title: computed(() => `The Wedding of ${groomName} & ${brideName}`),
 *     details: computed(() => `Akad Nikah & Resepsi Pernikahan ${groomName} & ${brideName}`),
 * })
 */
export function useCalendarLink({ event, title, details, durationHours = 3 }) {
    return computed(() => {
        const source = event.value
        if (!source?.date) return '#'

        const start = new Date(source.date)
        if (source.time) {
            const [h, m] = source.time.split(':')
            start.setHours(Number(h), Number(m), 0, 0)
        }

        const end = new Date(start.getTime() + durationHours * 60 * 60 * 1000)
        const fmt = (d) => d.toISOString().replace(/[-:]/g, '').split('.')[0] + 'Z'

        const params = new URLSearchParams({
            action: 'TEMPLATE',
            text: title.value,
            details: details.value,
            dates: `${fmt(start)}/${fmt(end)}`,
        })

        return `https://www.google.com/calendar/render?${params.toString()}`
    })
}
