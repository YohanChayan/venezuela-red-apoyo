/**
 * Human-friendly Spanish date: relative for recent moments, absolute otherwise.
 */
export function humanDate(value: string | null | undefined): string {
    if (!value) {
        return '';
    }

    const date = new Date(value);
    const seconds = (Date.now() - date.getTime()) / 1000;

    if (seconds < 45) {
        return 'hace un momento';
    }
    if (seconds < 3600) {
        const minutes = Math.round(seconds / 60);
        return `hace ${minutes} min`;
    }
    if (seconds < 86400) {
        const hours = Math.round(seconds / 3600);
        return `hace ${hours} h`;
    }
    if (seconds < 7 * 86400) {
        const days = Math.round(seconds / 86400);
        return days === 1 ? 'ayer' : `hace ${days} días`;
    }

    return date.toLocaleDateString('es-VE', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
}

/**
 * Full, unambiguous date+time for tooltips.
 */
export function fullDate(value: string | null | undefined): string {
    if (!value) {
        return '';
    }

    return new Date(value).toLocaleString('es-VE', { dateStyle: 'long', timeStyle: 'short' });
}
