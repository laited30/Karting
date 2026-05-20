<script setup>
// --- IMPORTS ---
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import RentKartModal from '@/Components/RentKartModal.vue';
import { ref } from 'vue';

// --- PROPS DEFINITION ---
defineProps({
    karts: Array
}); 

// --- STATE ---
const isModalOpen = ref(false);
const selectedKart = ref(null);

// --- UI HANDLERS & RIPPLE ---
const playRipple = (event) => {
    const button = event.currentTarget;
    const circle = document.createElement("span");
    circle.classList.add("ripple-effect");
    button.appendChild(circle);

    setTimeout(() => {
        circle.remove();
    }, 400);
};

const handleRentClick = (event, kart) => {
    playRipple(event);
    selectedKart.value = kart;
    
    setTimeout(() => {
        isModalOpen.value = true;
    }, 150);
};

const closeModal = () => {
    isModalOpen.value = false;
    setTimeout(() => {
        selectedKart.value = null;
    }, 300);
};
</script>

<template>
    <Head title="Home" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-white-800">Home</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-background-panel overflow-hidden shadow-sm sm:rounded-lg p-6 text-sup-white">
                    <h3 class="text-lg font-bold mb-4">Our go-karts are available for rent:</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div v-for="kart in karts" :key="kart.id" class="border border-background-button bg-background p-4 rounded shadow-sm">
                            <div>
                                <p class="font-semibold text-lg">{{ kart.number }}</p>
                                <p class="text-sm text-white-400">Is ready: {{ kart.is_active }}</p>
                            </div>
                            
                            <button 
                                @click="handleRentClick($event, kart)"
                                class="relative overflow-hidden font-medium py-1 px-4 mt-3 rounded border transition-all duration-300"
                                :class="kart.is_active ? 'bg-transparent border-green-600 text-white-400' : 'bg-background-button border-gray-600 text-white-500 cursor-not-allowed'"
                                :disabled="!kart.is_active"
                            >
                                Rent
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal window -->
        <RentKartModal 
            :show="isModalOpen" 
            :kart="selectedKart" 
            @close="closeModal" 
        />

    </AuthenticatedLayout>
</template>

<style scoped>
    :deep(.ripple-effect) {
        position: absolute;
        top: 50%;
        left: 50%;
        width: 160px;
        height: 160px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        transform: translate(-50%, -50%) scale(0);
        animation: full-wave 0.4s ease-out forwards;
        pointer-events: none;
    }

    @keyframes full-wave {
        0% { transform: translate(-50%, -50%) scale(0); opacity: 1; }
        100% { transform: translate(-50%, -50%) scale(1); opacity: 0; }
    }
</style>