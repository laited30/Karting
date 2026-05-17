<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

// Приймаємо дані від Laravel (якщо вони будуть передані)
defineProps({
    title: String,
    content: String
});
</script>

<template>
    <!-- Встановлюємо тег <title> для вкладки браузера -->
    <Head title="About us" />

    <!-- Загортаємо в головний макет, щоб зберегти меню та дизайн сайту -->
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-white-800 leading-tight">About us</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-backgroud-panel overflow-hidden shadow-sm sm:rounded-lg p-6 text-white-900">
                    <h3 class="text-lg font-bold mb-4">{{ title }}</h3>
                    <p>{{ content }}</p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>