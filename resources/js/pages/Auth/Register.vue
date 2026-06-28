<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({ name: '', email: '', password: '', password_confirmation: '' });

function submit(): void {
    form.post('/register', { onFinish: () => form.reset('password', 'password_confirmation') });
}
</script>

<template>
    <Head title="Crear cuenta" />

    <div class="flex min-h-screen items-center justify-center bg-slate-100 p-4">
        <div class="w-full max-w-sm rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <Link href="/" class="text-sm text-slate-500 hover:text-slate-700">← Ir al sitio</Link>
            <h1 class="mt-2 mb-1 text-xl font-bold">Crear cuenta</h1>
            <p class="mb-5 text-sm text-slate-500">
                Tu cuenta queda <strong>pendiente</strong> hasta que el master te asigne un rol.
            </p>

            <form class="space-y-4" @submit.prevent="submit">
                <div class="space-y-1.5">
                    <Label for="name">Nombre</Label>
                    <Input id="name" v-model="form.name" required />
                    <p v-if="form.errors.name" class="text-xs text-red-600">{{ form.errors.name }}</p>
                </div>
                <div class="space-y-1.5">
                    <Label for="email">Correo</Label>
                    <Input id="email" v-model="form.email" type="email" autocomplete="email" required />
                    <p v-if="form.errors.email" class="text-xs text-red-600">{{ form.errors.email }}</p>
                </div>
                <div class="space-y-1.5">
                    <Label for="password">Contraseña</Label>
                    <Input id="password" v-model="form.password" type="password" autocomplete="new-password" required />
                    <p v-if="form.errors.password" class="text-xs text-red-600">{{ form.errors.password }}</p>
                </div>
                <div class="space-y-1.5">
                    <Label for="password_confirmation">Confirmar contraseña</Label>
                    <Input
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        type="password"
                        autocomplete="new-password"
                        required
                    />
                </div>
                <Button type="submit" class="w-full" :disabled="form.processing">Registrarme</Button>
            </form>

            <p class="mt-4 text-center text-sm text-slate-500">
                ¿Ya tienes cuenta?
                <Link href="/login" class="font-medium text-red-600 hover:underline">Inicia sesión</Link>
            </p>
        </div>
    </div>
</template>
