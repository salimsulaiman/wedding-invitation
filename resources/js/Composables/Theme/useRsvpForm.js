import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'

/**
 * Form ucapan & konfirmasi kehadiran (RSVP).
 *
 * @param {object} options
 * @param {{name?: string, slug?: string} | null} options.guest
 * @param {boolean} [options.isPreview] - true kalau di-render lewat BuilderController::preview
 * @param {() => string | null} options.getDomainName - function agar tetap reaktif terhadap props
 * @param {string} [options.routeName]
 *
 * @example
 * const { form, rsvpMessage, submit } = useRsvpForm({
 *     guest: props.guest,
 *     isPreview: props.isPreview,
 *     getDomainName: () => props.invitation?.domain?.name,
 * })
 */
export function useRsvpForm({ guest, isPreview = false, getDomainName, routeName = 'public.invitation.wishes.store' }) {
    const rsvpMessage = ref('')

    const form = useForm({
        name: guest?.name ?? '',
        message: '',
        attendance: 'hadir',
        guest_slug: guest?.slug ?? null,
    })

    const submit = () => {
        // Mode preview builder: dikirim eksplisit lewat props isPreview dari controller.
        // Jangan kirim RSVP sungguhan, cukup tampilkan pesan simulasi.
        if (isPreview) {
            rsvpMessage.value = 'Mode preview — ucapan tidak benar-benar terkirim.'
            setTimeout(() => (rsvpMessage.value = ''), 3000)
            return
        }

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