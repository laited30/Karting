<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
 defineProps({

karts: Array // Приймаємо масив картів від Laravel

}); 
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-track-light overflow-hidden shadow-sm sm:rounded-lg p-6">
                    
                    <h3 class="text-lg font-bold mb-4">Наші картинги в прокаті:</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div v-for="kart in karts" :key="kart.id" class="border p-4 rounded shadow-sm">
                            <p class="font-semibold text-lg">{{ kart.number }}</p>
                            <p class="text-sm text-gray-500">Статус: {{ kart.is_active }}</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
