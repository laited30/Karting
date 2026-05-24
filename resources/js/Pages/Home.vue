<script setup>
// --- IMPORTS ---
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import RentKartModal from '@/Components/RentKartModal.vue';
import { ref, computed } from 'vue';

// --- PROPS DEFINITION ---
// Define props using standard Vue 3 syntax
const props = defineProps({
    karts: {
        type: Array,
        default: () => [] // Always good practice to provide a default value for arrays
    }
});

// --- STATE ---
const currentFilter = ref('ready');
const isModalOpen = ref(false);
const selectedKart = ref(null);

// --- COMPUTED PROPERTIES ---
// Filter karts based on the selected status
const filteredKarts = computed(() => {
    switch (currentFilter.value) {
        case 'ready':
            return props.karts.filter(kart => kart.is_active);
        case 'repair':
            return props.karts.filter(kart => !kart.is_active);
        case 'all':
        default:
            return props.karts;
    }
});

// --- UI HANDLERS & RIPPLE ---
// Note: While direct DOM manipulation (appendChild) is generally avoided in Vue, 
// for a simple transient visual effect like a ripple, this approach is acceptable 
// to avoid complex reactive state tracking per button.
const playRipple = (event) => {
    const button = event.currentTarget;
    const circle = document.createElement("span");
    
    // Calculate precise click coordinates for a better ripple effect origin
    const rect = button.getBoundingClientRect();
    const x = event.clientX - rect.left;
    const y = event.clientY - rect.top;

    circle.classList.add("ripple-effect");
    circle.style.left = `${x}px`;
    circle.style.top = `${y}px`;
    
    button.appendChild(circle);

    setTimeout(() => {
        circle.remove();
    }, 400);
};

const handleRentClick = (event, kart) => {
    // Prevent action if kart is not active
    if (!kart.is_active) return;

    playRipple(event);
    selectedKart.value = kart;
    
    // Delay modal opening slightly to let the user see the ripple effect
    setTimeout(() => {
        isModalOpen.value = true;
    }, 150);
};

const closeModal = () => {
    isModalOpen.value = false;
    
    // Delay clearing the selected kart to prevent visual jumping during modal close animation
    setTimeout(() => {
        selectedKart.value = null;
    }, 300);
};
</script>

<template>
    <Head title="Home" />

    <AuthenticatedLayout>
        <!-- Navigation Filter -->
        <header class="bg-background-panel flex flex-row border-b border-background-button select-none overflow-x-auto">
            <button 
                @click="currentFilter = 'ready'"
                class="p-4 cursor-pointer transition-all duration-200 border-b-2 whitespace-nowrap hover:text-white"
                :class="currentFilter === 'ready' 
                    ? 'border-indigo-400 text-indigo-400 font-semibold' 
                    : 'border-transparent text-sup-white'"
            >
                Ready
            </button>
            <button 
                @click="currentFilter = 'repair'"
                class="p-4 cursor-pointer transition-all duration-200 border-b-2 whitespace-nowrap hover:text-white"
                :class="currentFilter === 'repair' 
                    ? 'border-indigo-400 text-indigo-400 font-semibold' 
                    : 'border-transparent text-sup-white'"
            >
                Not available
            </button>
            <button 
                @click="currentFilter = 'all'"
                class="p-4 cursor-pointer transition-all duration-200 border-b-2 whitespace-nowrap hover:text-white"
                :class="currentFilter === 'all' 
                    ? 'border-indigo-400 text-indigo-400 font-semibold' 
                    : 'border-transparent text-sup-white'"
            >
                All
            </button>
        </header>

        <!-- Main Content Grid -->
        <div class="min-h-screen bg-background pb-8 flex flex-col">
            <div class="grid grid-cols-1 md:grid-cols-5 overflow-hidden min-h-[500px]">
            
                <!-- Kart List (Scrollable if too many items) -->
                <div class="col-span-1 md:col-span-2 overflow-y-auto max-h-[65vh] custom-scrollbar">
                    <div 
                        v-for="kart in filteredKarts" 
                        :key="kart.id" 
                        class="group grid grid-cols-2 m-4 border border-background-button bg-background rounded shadow-sm overflow-hidden"
                    >
                        <div class="p-4 flex flex-col justify-center">
                            <p class="font-semibold text-lg">Kart #{{ kart.number }}</p>
                            <!-- Formatted boolean output for better UX -->
                            <p class="text-sm text-gray-400">
                                Status: <span :class="kart.is_active ? 'text-green-400' : 'text-red-400'">
                                    {{ kart.is_active ? 'Ready' : 'In Repair' }}
                                </span>
                            </p>
                        </div>
                        
                        <button 
                            @click="handleRentClick($event, kart)"
                            class="relative overflow-hidden text-2xl transition-all duration-300 md:translate-x-[150%] md:group-hover:translate-x-0 md:[clip-path:polygon(15%_0%,_100%_0%,_100%_100%,_0%_100%)] flex items-center justify-center"
                            :class="kart.is_active 
                                ? 'bg-[#70a324] hover:bg-[#51731d] text-white cursor-pointer' 
                                : 'bg-gray-600 text-gray-300 cursor-not-allowed opacity-75'"
                            :disabled="!kart.is_active"
                        >
                            {{ kart.is_active ? 'Rent' : 'Under repair' }}
                        </button>
                    </div>
                    
                    <!-- Empty State -->
                    <div v-if="filteredKarts.length === 0" class="p-8 text-center text-gray-400">
                        No karts found for this filter.
                    </div>
                </div>
                
                <!-- Video Background Section -->
                <div class="video-wrapper hidden md:block md:col-span-3">
                    <!-- Note: Ensure the video path is correct. In Vite, it's often better to place static assets in the /public folder and reference them as src="/untitled.mp4" -->
                    <video id="myVideo" src="../../views/untitled.mp4" autoplay muted loop playsinline></video>
                </div>
            </div>
            <div class="flex flex-col bg-background-panel2 w-auto mx-8 mt-8 p-10 rounded-lg justify-center items-center">
    
                <h2 class="mb-6 text-xl font-bold">Our Karts have</h2>
                
                <div class="grid grid-cols-5 gap-4 w-full">
                    <div class="karts-data-panels">
                        <img src="../../views/TO.png" alt="maintenance">
                        <p>Regular maintenance</p>
                    </div>

                    <div class="karts-data-panels">
                        <img src="../../views/health.png" alt="security">
                        <p>All security systems</p>
                    </div>

                    <div class="karts-data-panels">
                        <img src="../../views/horse.png" alt="horsepower">
                        <p>Engines with 100–180 horsepower</p>
                    </div>

                    <div class="karts-data-panels">
                        <img src="../../views/tire.png" alt="tyres">
                        <p>High-quality tyres</p>
                    </div>

                    <div class="karts-data-panels">
                        <img src="../../views/chill.png" alt="boarding">
                        <p>Designed for easy boarding</p>
                    </div>
                    <!--
                    <div class="w-full h-80 bg-background-panel rounded-md border border-gray-700">Regular maintenance</div>
                    <div class="w-full h-80 bg-background-panel rounded-md border border-gray-700">All security systems</div>
                    <div class="w-full h-80 bg-background-panel rounded-md border border-gray-700">Engines with 100–180 horsepower</div>
                    <div class="w-full h-80 bg-background-panel rounded-md border border-gray-700">High-quality tyres</div>
                    <div class="w-full h-80 bg-background-panel rounded-md border border-gray-700">Designed for easy boarding</div>
                    -->
                </div>
                
            </div>
            <div class="flex flex-col w-auto mx-8 mt-8 p-10 rounded-lg justify-center items-center">
    
                <h2 class="mb-6 text-xl font-bold bg-background-panel p-5 rounded-xl">Kontakt</h2>
                
                <div class="grid grid-cols-2 gap-4 w-full">
                    <div class="flex flex-col text-xl justify-center bg-background-panel p-5 rounded-xl">
                        <label for=""><i class="p-4 fa-solid fa-phone"></i> +48...........</label>
                        <label for=""><i class="p-4 fa-solid fa-envelope"></i> email@gmail.com</label>
                        <label for=""><i class="p-4 fa-brands fa-telegram"></i> +48...........</label>
                        <label for=""><i class="p-4 fa-brands fa-viber"></i> +48...........</label>
                        <label for=""><i class="p-4 fa-solid fa-map-location-dot"></i> I don`t know</label>
                    </div>

                    <div class="flex justify-center items-center">
                        <iframe 
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d10924.927634672535!2d172.50833657837282!3d-43.50987316839886!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x6d31f52e0d8e89e1%3A0xa6661a7f3c4ee0a2!2sCOO%20Apples!5e0!3m2!1sru!2spl!4v1779653869518!5m2!1sru!2spl" 
                            width="800" 
                            height="450" 
                            style="border:0;" 
                            allowfullscreen="" 
                            loading="lazy" 
                            referrerpolicy="no-referrer-when-downgrade"
                            class="rounded-lg">
                        </iframe>
                    </div>
                </div>
                
            </div>
        </div>

        <!-- Modal Window -->
        <RentKartModal 
            :show="isModalOpen" 
            :kart="selectedKart" 
            @close="closeModal" 
        />
        
    </AuthenticatedLayout>
</template>

<style scoped>
.video-wrapper {
    position: relative;
    display: inline-block;
    overflow: hidden;
    height: 100%;
}

#myVideo {
    display: block;
    width: 100%;
    height: 100%;
    object-fit: cover;
    opacity: 0.8;
}

.video-wrapper::before {
    content: "";
    position: absolute;
    top: 0;
    right: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, rgba(17, 17, 22, 1) 9%, rgba(17, 17, 22, 0.27) 70%), linear-gradient(0deg,rgba(17, 17, 22, 1) 0%, rgba(17, 17, 22, 0) 72%);
    z-index: 10;
    pointer-events: none;
}

/* Background gradient utility class (kept if used elsewhere) */
.background-photo-gradient {
    background: radial-gradient(circle, rgba(133, 38, 38, 1) 3%, rgba(61, 61, 61, 0) 70%);
    background-size: 170%;
}
.karts-data-panels{
    position: relative;
    font-size: large;
    padding: 10px;
    background-color: #1e1e24;
    border-radius: 20px;
    border: solid 1px rgb(81, 81, 143);
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    width: auto;
    height: 30vh;
}
.karts-data-panels img{
    width: 50%;
    margin-bottom: 10%;
}
/* Ripple Effect Styles */
:deep(.ripple-effect) {
    position: absolute;
    width: 160px;
    height: 160px;
    background: rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    transform: translate(-50%, -50%) scale(0);
    animation: full-wave 0.4s ease-out forwards;
    pointer-events: none;
}

@keyframes full-wave {
    0% { 
        transform: translate(-50%, -50%) scale(0); 
        opacity: 1; 
    }
    100% { 
        transform: translate(-50%, -50%) scale(1.5); 
        opacity: 0; 
    }
}

/* Optional: Custom Scrollbar for the list */
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background-color: #4b5563;
    border-radius: 10px;
}
</style>