import type { EnumOption } from '@/types/models';

/**
 * Shared presentation maps for the need / commitment lifecycle, so every
 * component renders statuses and priorities the same way.
 */

/** Background + text pill classes per lifecycle status. */
export const needStatusPill: Record<string, string> = {
    solicitada: 'bg-slate-100 text-slate-600',
    comprometida: 'bg-blue-100 text-blue-700',
    en_camino: 'bg-indigo-100 text-indigo-700',
    entregada: 'bg-teal-100 text-teal-700',
    confirmada: 'bg-green-100 text-green-700',
    cancelada: 'bg-slate-100 text-slate-400',
};

/** Text-only color per lifecycle status (used by the dashboard counters). */
export const needStatusText: Record<string, string> = {
    solicitada: 'text-slate-700',
    comprometida: 'text-blue-600',
    en_camino: 'text-indigo-600',
    entregada: 'text-teal-600',
    confirmada: 'text-green-600',
    cancelada: 'text-slate-400',
};

/** Left-border accent per priority (need cards). */
export const priorityBorder: Record<string, string> = {
    critica: 'border-l-red-500',
    alta: 'border-l-orange-500',
    media: 'border-l-amber-400',
    baja: 'border-l-slate-300',
};

/** Dot accent per priority (compact lists). */
export const priorityDot: Record<string, string> = {
    critica: 'bg-red-500',
    alta: 'bg-orange-500',
    media: 'bg-amber-500',
    baja: 'bg-slate-400',
};

/** Friendly label + emphasis for each lifecycle step a helper can take. */
export const commitmentActions: Record<string, { label: string; primary: boolean }> = {
    en_camino: { label: '🚚 Voy en camino', primary: true },
    entregada: { label: '📦 Entregado', primary: true },
    confirmada: { label: '✅ Confirmar recibido', primary: true },
    comprometida: { label: '↩ Reactivar', primary: false },
    cancelada: { label: '✕ Cancelar', primary: false },
};

export function statusPill(value: string): string {
    return needStatusPill[value] ?? 'bg-slate-100 text-slate-600';
}

export function statusText(value: string): string {
    return needStatusText[value] ?? 'text-slate-700';
}

export function actionMeta(transition: EnumOption): { label: string; primary: boolean } {
    return commitmentActions[transition.value] ?? { label: transition.label, primary: false };
}
