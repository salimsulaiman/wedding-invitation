<script setup>
defineProps({
    title: {
        type: String,
        required: true,
    },
    description: {
        type: String,
        default: null,
    },
})
</script>

<template>
    <div class="min-h-screen bg-pink-950">
        <div class="flex min-h-screen items-center justify-center p-4 lg:p-8">
            <div class="w-full max-w-7xl overflow-hidden rounded-[32px] bg-white shadow-2xl">
                <div class="grid min-h-[600px] lg:grid-cols-2">
                    <div class="relative hidden overflow-hidden bg-pink-900 lg:block">
                        <div class="absolute inset-0 bg-gradient-to-br from-pink-900 via-pink-950 to-black"></div>

                        <div class="absolute inset-0">
                            <div class="h-full w-full bg-center bg-cover"></div>
                        </div>

                        <div class="absolute inset-0 bg-black/20"></div>

                        <div class="relative flex h-full flex-col justify-between p-10">
                            <div class="flex items-center">
                                <div class="rounded-lg bg-white px-4 py-2">
                                    <span class="text-lg font-bold tracking-wide text-pink-900">
                                        LOGO
                                    </span>
                                </div>
                            </div>

                            <div class="max-w-md">
                                <h2 class="text-5xl font-black uppercase leading-none tracking-tight text-white">
                                    Welcome
                                    <br>
                                    Back
                                </h2>

                                <p class="mt-6 text-base leading-7 text-white/80">
                                    Securely access your dashboard and continue managing your projects, clients,
                                    and everything in one place.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-center px-6 py-10 sm:px-10 lg:px-16">
                        <div class="w-full max-w-md">
                            <div class="mb-10 flex items-center justify-center lg:hidden">
                                <div class="rounded-lg bg-pink-900 px-4 py-2">
                                    <span class="text-lg font-bold tracking-wide text-white">
                                        LOGO
                                    </span>
                                </div>
                            </div>

                            <h1 class="text-4xl font-black uppercase tracking-tight text-slate-900">
                                {{ title }}
                            </h1>

                            <p
                                v-if="description"
                                class="mt-3 text-sm leading-6 text-slate-500"
                            >
                                {{ description }}
                            </p>

                            <div class="mt-10">
                                <slot />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>