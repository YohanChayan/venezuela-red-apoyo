<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({ username: '', password: '', remember: false });

function submit(): void {
    form.post('/login', { onFinish: () => form.reset('password') });
}
</script>

<template>
    <Head title="Iniciar sesión" />

    <div class="flex min-h-screen items-center justify-center bg-slate-100 p-4">
        <div class="w-full max-w-sm rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <Link href="/" class="text-sm text-slate-500 hover:text-slate-700">← Ir al sitio</Link>
            <h1 class="mt-2 mb-1 text-xl font-bold">Iniciar sesión</h1>
            <p class="mb-5 text-sm text-slate-500">Acceso para gestores y administradores.</p>

            <form class="space-y-4" @submit.prevent="submit">
                <div class="space-y-1.5">
                    <Label for="username">Usuario</Label>
                    <Input id="username" v-model="form.username" type="text" autocomplete="username" required />
                    <p v-if="form.errors.username" class="text-xs text-red-600">{{ form.errors.username }}</p>
                </div>
                <div class="space-y-1.5">
                    <Label for="password">Contraseña</Label>
                    <Input id="password" v-model="form.password" type="password" autocomplete="current-password" required />
                    <p v-if="form.errors.password" class="text-xs text-red-600">{{ form.errors.password }}</p>
                </div>
                <label class="flex items-center gap-2 text-sm text-slate-600">
                    <input v-model="form.remember" type="checkbox" class="rounded border-slate-300" />
                    Recordarme
                </label>
                <Button type="submit" class="w-full" :disabled="form.processing">Entrar</Button>
            </form>

            <p class="mt-4 text-center text-xs text-slate-400">
                Acceso restringido. Las cuentas las crea el administrador maestro.
            </p>
        </div>
    </div>
</template>
