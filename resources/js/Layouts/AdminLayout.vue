<script setup>
import { Link, usePage, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import {
    LayoutDashboard,
    Users,
    Palette,
    Layers,
    ShoppingBag,
    Mail,
    Globe,
    MessageSquare,
    LogOut,
    Menu,
    X,
} from 'lucide-vue-next'

import { route } from 'ziggy-js'
import FlashMessage from '@/Components/Admin/FlashMessage.vue'

const current = (name) => route().current(name)

defineProps({
    title: {
        type: String,
        default: null,
    },
})

const page = usePage()
const sidebarOpen = ref(false)

const navigation = [
    {
        name: 'Dashboard',
        route: 'admin.dashboard',
        url: route('admin.dashboard'),
        icon: LayoutDashboard,
    },
    {
        name: 'Pengguna',
        route: 'admin.users.*',
        url: route('admin.users.index'),
        icon: Users,
    },
    // {
    //     name: 'Kategori Tema',
    //     route: 'admin.theme-categories.*',
    //     url: route('admin.theme-categories.index'),
    //     icon: Layers,
    // },
    {
        name: 'Tema',
        route: 'admin.themes.*',
        url: route('admin.themes.index'),
        icon: Palette,
    },
    {
        name: 'Pesanan',
        route: 'admin.orders.*',
        url: route('admin.orders.index'),
        icon: ShoppingBag,
    },
    {
        name: 'Undangan',
        route: 'admin.invitations.*',
        url: route('admin.invitations.index'),
        icon: Mail,
    },
    {
        name: 'Domain',
        route: 'admin.domains.*',
        url: route('admin.domains.index'),
        icon: Globe,
    },
    {
        name: 'Tamu & Ucapan',
        route: 'admin.guests.*',
        url: route('admin.guests.index'),
        icon: MessageSquare,
    },
]

const logout = () => {
    router.post(route('logout'))
}
</script>

<template>
    <div class="min-h-screen bg-slate-50">
        <!-- Overlay mobile -->
        <div
            v-if="sidebarOpen"
            class="fixed inset-0 z-40 bg-slate-900/40 lg:hidden"
            @click="sidebarOpen = false"
        ></div>

        <!-- Sidebar -->
        <aside
            class="fixed inset-y-0 left-0 z-50 w-64 transform border-r border-slate-200 bg-white transition-transform duration-200 lg:translate-x-0"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <div class="flex h-16 items-center justify-between border-b border-slate-200 px-5">
                <div class="flex items-center gap-2.5">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-pink-600">
                        <span class="text-xs font-bold text-white">U</span>
                    </div>
                    <span class="text-sm font-semibold tracking-tight text-slate-900">Undangin Admin</span>
                </div>
                <button class="text-slate-400 lg:hidden" @click="sidebarOpen = false">
                    <X class="h-5 w-5" />
                </button>
            </div>

            <nav class="space-y-1 px-3 py-4">
                <Link
                    v-for="item in navigation"
                    :key="item.href"
                    :href="item.url"
                    class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition"
                    :class="
                        route().current(item.route)
                            ? 'bg-pink-50 text-pink-700'
                            : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'
                    "
                >
                    <component :is="item.icon" class="h-4.5 w-4.5" />
                    {{ item.name }}
                </Link>
            </nav>
        </aside>

        <!-- Konten utama -->
        <div class="lg:pl-64">
            <!-- Topbar -->
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
                        <p class="text-xs text-slate-400">Admin</p>
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

            <!-- Slot halaman -->
            <main class="p-5 lg:p-8">
                <FlashMessage />
                <slot />
            </main>
        </div>
    </div>
</template>