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
    if (!kart.is_active) return;

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

        <div class="grid grid-cols-5 h-full overflow-hidden">
            <div class="col-span-2">
                <div v-for="kart in karts" :key="kart.id" class="group grid grid-cols-2 m-4 border border-background-button bg-background rounded shadow-sm overflow-hidden">
                    <div class="p-4 grid grid-cols-2">
                        <p class="font-semibold text-lg">{{ kart.number }}</p>
                        <p class="text-sm text-white-400">Is ready: {{ kart.is_active }}</p>
                    </div>
                    
                    <button 
                        @click="handleRentClick($event, kart)"
                        class="relative overflow-hidden text-[2rem] transition-all duration-200 translate-x-[150%] group-hover:translate-x-0 [clip-path:polygon(15%_0%,_100%_0%,_100%_100%,_0%_100%)]"
                        :class="kart.is_active 
                            ? 'bg-[#70a324] hover:bg-[#51731d] text-white' 
                            : 'bg-gray-500 text-gray-200 cursor-not-allowed '"
                        :disabled="!kart.is_active"
                    >
                        {{ kart.is_active ? 'Rent' : 'Under repair' }}
                    </button>
                </div>
            </div>
            
            <div class="col-span-3 relative background-photo-gradient">
                <img 
                    src="../../views/kart.png" 
                    alt="Kart" 
                    class="absolute top-1/2 -right-[40%] -translate-x-1/2 -translate-y-1/2 w-[60rem]"
                >
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
.background-photo-gradient{
    background: #852626;
    background: radial-gradient(circle, rgba(133, 38, 38, 1) 3%, rgba(61, 61, 61, 0) 70%);
    background-size: 170%;
}
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