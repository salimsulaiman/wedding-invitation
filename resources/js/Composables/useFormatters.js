export function useFormatters() {
    const formatCurrency = (value) =>
        new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value ?? 0)

    const formatDate = (value) => {
        if (!value) return '-'
        return new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }).format(new Date(value))
    }

    const formatLongDate = (value) => {
        if (!value) return '-'

        return new Intl.DateTimeFormat('id-ID', {
            day: '2-digit',
            month: 'long',
            year: 'numeric',
        }).format(new Date(value))
    }

    const formatTime = (value) => {
        if (!value) return '-'

        return new Intl.DateTimeFormat('id-ID', {
            hour: '2-digit',
            minute: '2-digit',
            hour12: false,
        }).format(new Date(`1970-01-01T${value}`))
    }

    const formatDateTime = (value) => {
        if (!value) return '-'
        return new Intl.DateTimeFormat('id-ID', {
            day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit',
        }).format(new Date(value))
    }

    return { formatCurrency, formatDate, formatDateTime, formatLongDate, formatTime }
}