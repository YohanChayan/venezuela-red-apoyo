<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const user = computed(
    () =>
        page.props.auth as unknown as {
            user: { name: string; roleLabel: string; canManage: boolean } | null;
        },
);
</script>

<template>
    <Head title="Mi cuenta" />

    <div class="flex min-h-screen items-center justify-center bg-slate-100 p-4">
        <div class="w-full max-w-md rounded-xl border border-slate-200 bg-white p-6 text-center shadow-sm">
            <p class="text-3xl">⏳</p>
            <h1 class="mt-2 text-xl font-bold">Hola, {{ user.user?.name }}</h1>
            <p class="mt-1 text-sm text-slate-500">
                Tu rol actual es <strong>{{ user.user?.roleLabel }}</strong>.
            </p>
            <p class="mt-3 rounded-lg bg-amber-50 px-4 py-3 text-sm text-amber-800">
                Tu cuenta está registrada pero <strong>pendiente de aprobación</strong>. El master debe asignarte un
                rol de administrador para que puedas gestionar el sistema.
            </p>

            <div class="mt-5 flex items-center justify-center gap-3">
                <Link
                    v-if="user.user?.canManage"
                    href="/admin"
                    class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white"
                >
                    Ir al panel
                </Link>
                <Link href="/" class="text-sm text-slate-500 hover:text-slate-700">Ir al sitio</Link>
                <Link href="/logout" method="post" as="button" class="text-sm text-red-600 hover:underline">
                    Cerrar sesión
                </Link>
            </div>
        </div>
    </div>
</template>
