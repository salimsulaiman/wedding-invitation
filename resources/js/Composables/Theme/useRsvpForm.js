import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'

/**
 * Form ucapan & konfirmasi kehadiran (RSVP).
 *
 * @param {object} options
 * @param {{name?: string, slug?: string} | null} options.guest
 * @param {() => string | null} options.getDomainName - function agar tetap reaktif terhadap props
 * @param {string} [options.routeName]
 *
 * @example
 * const { form, rsvpMessage, submit } = useRsvpForm({
 *     guest: props.guest,
 *     getDomainName: () => props.invitation?.domain?.name,
 * })
 */
export function useRsvpForm({ guest, getDomainName, routeName = 'public.invitation.wishes.store' }) {
    const rsvpMessage = ref('')

    const form = useForm({
        name: guest?.name ?? '',
        message: '',
        attendance: 'hadir',
        guest_slug: guest?.slug ?? null,
    })

    const submit = () => {
        const domainName = getDomainName()

        if (!domainName) {
            alert('Undangan ini belum memiliki domain aktif.')
            return
        }

        form.post(route(routeName, domainName), {
            preserveScroll: true,
            onSuccess: () => {
                form.reset('message')
                rsvpMessage.value = 'Terima kasih atas doa & konfirmasinya!'
                setTimeout(() => (rsvpMessage.value = ''), 3000)
            },
        })
    }

    return { form, rsvpMessage, submit }
}
