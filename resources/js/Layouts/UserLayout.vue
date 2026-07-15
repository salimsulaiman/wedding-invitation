<script setup>
import { Link, usePage, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import {
    LayoutDashboard,
    Mail,
    ShoppingBag,
    Landmark,
    LogOut,
    Menu,
    X,
} from 'lucide-vue-next'

defineProps({
    title: {
        type: String,
        default: null,
    },
})

const page = usePage()
const sidebarOpen = ref(false)

const navigation = [
    { name: 'Dashboard', href: 'user.dashboard', icon: LayoutDashboard },
    { name: 'Undangan Saya', href: 'user.invitations.index', icon: Mail },
    { name: 'Riwayat Pesanan', href: 'user.orders.index', icon: ShoppingBag },
    { name: 'Rekening Tersimpan', href: 'user.bank-accounts.index', icon: Landmark },
]

const logout = () => {
    router.post(route('logout'))
}
</script>

<template>
    <div class="min-h-screen bg-slate-50">
        <div
            v-if="sidebarOpen"
            class="fixed inset-0 z-40 bg-slate-900/40 lg:hidden"
            @click="sidebarOpen = false"
        ></div>

        <aside
            class="fixed inset-y-0 left-0 z-50 w-64 transform border-r border-slate-200 bg-white transition-transform duration-200 lg:translate-x-0"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <div class="flex h-16 items-center justify-between border-b border-slate-200 px-5">
                <div class="flex items-center gap-2.5">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-pink-600">
                        <span class="text-xs font-bold text-white">U</span>
                    </div>
                    <span class="text-sm font-semibold tracking-tight text-slate-900">Undangin</span>
                </div>
                <button class="text-slate-400 lg:hidden" @click="sidebarOpen = false">
                    <X class="h-5 w-5" />
                </button>
            </div>

            <nav class="space-y-1 px-3 py-4">
                <Link
                    v-for="item in navigation"
                    :key="item.href"
                    :href="route(item.href)"
                    class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition"
                    :class="
                        route().current(item.href)
                            ? 'bg-pink-50 text-pink-700'
                            : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'
                    "
                >
                    <component :is="item.icon" class="h-4.5 w-4.5" />
                    {{ item.name }}
                </Link>
            </nav>
        </aside>

        <div class="lg:pl-64">
            <header class="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-slate-200 bg-white px-5 lg:px-8">
                <div class="flex items-center gap-3">
                    <button class="text-slate-500 lg:hidden" @click="sidebarOpen = true">
                        <Menu class="h-5 w-5" />
                    </button>
                    <h1 v-if="title" class="text-base font-semibold text-slate-900">{{ title }}</h1>
                </div>

                <div class="flex items-center gap-3">
                    <div class="hidden text-right sm:block">
                        <p class="text-sm font-medium text-slate-900">{{ page.props.auth.user.name }}</p>
                        <p class="text-xs text-slate-400">Customer</p>
                    </div>
                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-pink-100 text-sm font-semibold text-pink-700">
                        {{ page.props.auth.user.name.charAt(0).toUpperCase() }}
                    </div>
                    <button
                        title="Keluar"
                        class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-50 hover:text-slate-600"
                        @click="logout"
                    >
                        <LogOut class="h-4.5 w-4.5" />
                    </button>
                </div>
            </header>

            <main class="p-5 lg:p-8">
                <slot />
            </main>
        </div>
    </div>
</template>