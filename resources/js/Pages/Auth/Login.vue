<script setup>
import AuthLayout from '@/Layouts/AuthLayout.vue'
import { Head, useForm } from '@inertiajs/vue3'


defineProps({
    status: {
        type: String,
        default: null,
    },
})

const form = useForm({
    email: '',
    password: '',
    remember: false,
})

const submit = () => {
    form.post(route('login.store'), {
        onFinish: () => form.reset('password'),
    })
}
</script>

<template>
    <Head title="Masuk" />

    <AuthLayout
        title="Welcome Back!"
        description="Please enter your email and password to continue."
    >
        <div
            v-if="status"
            class="mb-6 rounded-xl border border-pink-200 bg-pink-50 px-4 py-3 text-sm text-pink-700"
        >
            {{ status }}
        </div>

        <form
            class="space-y-6"
            @submit.prevent="submit"
        >
            <div>
                <label
                    for="email"
                    class="mb-2 block text-sm font-semibold text-slate-700"
                >
                    Email
                </label>

                <input
                    id="email"
                    v-model="form.email"
                    type="email"
                    autocomplete="username"
                    autofocus
                    required
                    placeholder="Enter your email"
                    class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-pink-600 focus:ring-4 focus:ring-pink-100"
                >

                <p
                    v-if="form.errors.email"
                    class="mt-2 text-sm text-red-600"
                >
                    {{ form.errors.email }}
                </p>
            </div>

            <div>
                <label
                    for="password"
                    class="mb-2 block text-sm font-semibold text-slate-700"
                >
                    Password
                </label>

                <input
                    id="password"
                    v-model="form.password"
                    type="password"
                    autocomplete="current-password"
                    required
                    placeholder="Enter your password"
                    class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-pink-600 focus:ring-4 focus:ring-pink-100"
                >

                <p
                    v-if="form.errors.password"
                    class="mt-2 text-sm text-red-600"
                >
                    {{ form.errors.password }}
                </p>
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 text-sm text-slate-600">
                    <input
                        v-model="form.remember"
                        type="checkbox"
                        class="h-4 w-4 rounded border-slate-300 text-pink-700 focus:ring-2 focus:ring-pink-200"
                    >

                    <span>Remember me</span>
                </label>

                <button
                    type="button"
                    class="text-sm font-semibold text-pink-700 transition hover:text-pink-800"
                >
                    Forgot password?
                </button>
            </div>

            <button
                type="submit"
                :disabled="form.processing"
                class="flex w-full items-center justify-center rounded-xl bg-pink-800 px-4 py-3 text-sm font-semibold text-white transition hover:bg-pink-900 focus:ring-4 focus:ring-pink-200 disabled:cursor-not-allowed disabled:opacity-60"
            >
                <span v-if="form.processing">
                    Signing in...
                </span>

                <span v-else>
                    Sign In
                </span>
            </button>
        </form>
    </AuthLayout>
</template>