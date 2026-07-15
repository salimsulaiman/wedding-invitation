import { ref } from 'vue'

/**
 * Copy teks ke clipboard + tampilkan toast singkat.
 * Umum dipakai untuk tombol "Salin Nomor" rekening.
 *
 * @example
 * const { toast, copy } = useCopyToClipboard()
 * copy(account.account_number, 'Nomor rekening disalin: ' + account.account_number)
 */
export function useCopyToClipboard(toastDuration = 2500) {
    const toast = ref('')

    const copy = (text, message = null) => {
        navigator.clipboard.writeText(text).then(() => {
            toast.value = message ?? `Berhasil disalin: ${text}`
            setTimeout(() => (toast.value = ''), toastDuration)
        })
    }

    return { toast, copy }
}
