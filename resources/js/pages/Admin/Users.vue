<script setup lang="ts">
import FlashMessage from '@/components/FlashMessage.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Head, Link, router, useForm } from '@inertiajs/vue3';

interface ManagedUser {
    id: number;
    username: string;
    role: string;
    roleLabel: string;
    isMaster: boolean;
}

defineProps<{
    users: ManagedUser[];
    assignableRoles: { value: string; label: string }[];
}>();

const form = useForm({ username: '', password: '', role: 'admin' });

function createUser(): void {
    form.post('/admin/usuarios', { preserveScroll: true, onSuccess: () => form.reset() });
}

function changeRole(user: ManagedUser, role: string): void {
    if (role !== user.role) {
        router.patch(`/admin/usuarios/${user.id}`, { role }, { preserveScroll: true });
    }
}

function removeUser(user: ManagedUser): void {
    if (confirm(`¿Eliminar la cuenta «${user.username}»?`)) {
        router.delete(`/admin/usuarios/${user.id}`, { preserveScroll: true });
    }
}
</script>

<template>
    <Head title="Usuarios" />

    <div class="min-h-screen bg-slate-100 text-slate-900">
        <header class="border-b border-slate-200 bg-white">
            <div class="mx-auto flex max-w-6xl items-center justify-between gap-3 px-4 py-3">
                <h1 class="flex items-center gap-2 font-bold"><span>👥</span> Gestión de usuarios</h1>
                <Link href="/admin" class="text-sm text-slate-500 hover:text-slate-700">← Volver al panel</Link>
            </div>
        </header>

        <FlashMessage />

        <main class="mx-auto max-w-6xl space-y-6 px-4 py-6">
            <section class="rounded-xl border border-slate-200 bg-white p-4">
                <h2 class="mb-3 font-semibold">Crear cuenta</h2>
                <form class="grid gap-3 sm:grid-cols-4" @submit.prevent="createUser">
                    <div class="space-y-1">
                        <Label for="username">Usuario</Label>
                        <Input id="username" v-model="form.username" autocomplete="off" />
                        <p v-if="form.errors.username" class="text-xs text-red-600">{{ form.errors.username }}</p>
                    </div>
                    <div class="space-y-1">
                        <Label for="password">Contraseña</Label>
                        <Input id="password" v-model="form.password" type="text" autocomplete="off" />
                        <p v-if="form.errors.password" class="text-xs text-red-600">{{ form.errors.password }}</p>
                    </div>
                    <div class="space-y-1">
                        <Label for="role">Rol</Label>
                        <select
                            id="role"
                            v-model="form.role"
                            class="h-9 w-full rounded-lg border border-slate-300 bg-white px-2 text-sm"
                        >
                            <option v-for="role in assignableRoles" :key="role.value" :value="role.value">
                                {{ role.label }}
                            </option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <Button type="submit" class="w-full" :disabled="form.processing">Crear</Button>
                    </div>
                </form>
                <p class="mt-2 text-xs text-slate-400">
                    Sin correo ni datos personales: solo usuario y contraseña. Luego comparte las credenciales en
                    privado.
                </p>
            </section>

            <section class="overflow-hidden rounded-xl border border-slate-200 bg-white">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-left text-slate-500">
                        <tr>
                            <th class="px-4 py-2 font-medium">Usuario</th>
                            <th class="px-4 py-2 font-medium">Rol</th>
                            <th class="px-4 py-2 font-medium"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="user in users" :key="user.id">
                            <td class="px-4 py-2 font-medium">{{ user.username }}</td>
                            <td class="px-4 py-2">
                                <span
                                    v-if="user.isMaster"
                                    class="rounded-full bg-slate-900 px-2.5 py-1 text-xs font-semibold text-white"
                                >
                                    👑 Master
                                </span>
                                <select
                                    v-else
                                    :value="user.role"
                                    class="h-8 rounded-md border border-slate-300 bg-white px-2 text-sm"
                                    @change="changeRole(user, ($event.target as HTMLSelectElement).value)"
                                >
                                    <option v-for="role in assignableRoles" :key="role.value" :value="role.value">
                                        {{ role.label }}
                                    </option>
                                </select>
                            </td>
                            <td class="px-4 py-2 text-right">
                                <button
                                    v-if="!user.isMaster"
                                    type="button"
                                    class="text-xs text-red-600 hover:underline"
                                    @click="removeUser(user)"
                                >
                                    Eliminar
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </section>
        </main>
    </div>
</template>
