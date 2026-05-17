<script setup>
// --- IMPORTS ---
// Importing core layouts, tools, and reactive hooks
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

// --- PROPS DEFINITION ---
defineProps({
    karts: Array // Array of available go-karts from backend
}); 

// --- REACTIVE STATE ---
const isModalOpen = ref(false);       // Controls visibility of the main modal overlay
const selectedKart = ref(null);       // Tracks which kart object was clicked for renting
const selectedDate = ref(null);       // Tracks the user's specifically chosen rental date

// --- CALENDAR REACTIVE STATE ---
// Standard Javascript Date tracking for active calendar screen navigation
const calendarDate = ref(new Date()); 
const currentMonth = computed(() => calendarDate.value.getMonth());
const currentYear = computed(() => calendarDate.value.getFullYear());

// Array of English month names for the calendar header display
const monthNames = [
    "January", "February", "March", "April", "May", "June",
    "July", "August", "September", "October", "November", "December"
];

// --- CALENDAR COMPUTED LOGIC ---
// Generating the exact composition of matrix days for the selected month view
const calendarDays = computed(() => {
    const year = currentYear.value;
    const month = currentMonth.value;

    // 1. Find the day of the week the 1st day of the month falls on (e.g., 0 = Sun, 1 = Mon...)
    // Adjusting formula to make Monday the absolute first day of the week (standard European layout)
    let firstDayOfWeek = new Date(year, month, 1).getDay();
    firstDayOfWeek = firstDayOfWeek === 0 ? 6 : firstDayOfWeek - 1; 

    // 2. Determine total amount of days in the current targeted month
    const totalDaysInMonth = new Date(year, month + 1, 0).getDate();

    const daysArray = [];

    // 3. Inject blank placeholder items to offset grid position before Day 1
    for (let i = 0; i < firstDayOfWeek; i++) {
        daysArray.push({ dayNumber: null, isCurrentMonth: false });
    }

    // 4. Inject active actual day numbers into the list array
    for (let day = 1; day <= totalDaysInMonth; day++) {
        daysArray.push({ dayNumber: day, isCurrentMonth: true });
    }

    return daysArray;
});

// --- FUNCTIONS / METHODS ---
// Renders the classic floating visual ripple effect upon clicking targets
const playRipple = (event) => {
    const button = event.currentTarget;
    const circle = document.createElement("span");
    circle.classList.add("ripple-effect");
    button.appendChild(circle);

    setTimeout(() => {
        circle.remove();
    }, 400);
};

// Initial handler to boot up rent sequence configurations
const handleRentClick = (event, kart) => {
    playRipple(event);
    selectedKart.value = kart;
    // Reset calendar viewport view back to current actual day upon opening fresh dialog
    calendarDate.value = new Date(); 
    
    setTimeout(() => {
        isModalOpen.value = true;
    }, 150);
};

// Navigation controller to shift months backward
const prevMonth = () => {
    calendarDate.value = new Date(currentYear.value, currentMonth.value - 1, 1);
};

// Navigation controller to shift months forward
const nextMonth = () => {
    calendarDate.value = new Date(currentYear.value, currentMonth.value + 1, 1);
};

// Stores chosen final date and logs selection context
const selectDateHandler = (dayNum) => {
    if (!dayNum) return;
    selectedDate.value = `${currentYear.value}-${String(currentMonth.value + 1).padStart(2, '0')}-${String(dayNum).padStart(2, '0')}`;
};

// Clean closure operations resetting overall memory states gracefully
const closeModal = (event) => {
    playRipple(event);
    setTimeout(() => {
        isModalOpen.value = false;
    }, 150);

    setTimeout(() => {
        selectedKart.value = null;
        selectedDate.value = null;
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
                <div class="bg-track-light overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold mb-4">Our go-karts are available for rent:</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div v-for="kart in karts" :key="kart.id" class="border p-4 rounded shadow-sm">
                            <div>
                                <p class="font-semibold text-lg">{{ kart.number }}</p>
                                <p class="text-sm text-gray-500">Is ready: {{ kart.is_active }}</p>
                            </div>
                            
                            <button 
                                @click="handleRentClick($event, kart)"
                                class="relative overflow-hidden text-white font-medium py-1 px-4 rounded border transition-all duration-300"
                                :class="kart.is_active ? 'bg-green-600 border-green-600' : 'bg-gray-400 border-gray-400 cursor-not-allowed'"
                                :disabled="!kart.is_active"
                            >
                                Rent
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- --- MODAL WINDOW OVERLAY --- -->
        <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm">
            <!-- Expanded Modal Layout to naturally hold the calendar layout widget -->
            <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4 shadow-xl transform transition-all text-center">
                
                <h3 class="text-xl font-bold text-gray-900 mb-2">Rent Kart No. {{ selectedKart?.number }}</h3>
                <p class="text-sm text-gray-500 mb-4">Select your preferred date from the calendar below:</p>

                <!-- --- INTEGRATED CALENDAR UI COMPONENT --- -->
                <div class="border rounded-lg p-4 bg-gray-50 mb-4">
                    <!-- Calendar Navigation Header section control components -->
                    <div class="flex justify-between items-center mb-4">
                        <button @click="prevMonth" class="text-gray-600 hover:text-blue-600 font-bold px-2 py-1">&lt; Prev</button>
                        <span class="font-bold text-gray-800 text-md">{{ monthNames[currentMonth] }} {{ currentYear }}</span>
                        <button @click="nextMonth" class="text-gray-600 hover:text-blue-600 font-bold px-2 py-1">Next &gt;</button>
                    </div>

                    <!-- Static Days indicators row framework definitions -->
                    <div class="grid grid-cols-7 gap-1 text-xs font-bold text-gray-400 mb-2 text-center">
                        <div>Mo</div><div>Tu</div><div>We</div><div>Th</div><div>Fr</div><div>Sa</div><div>Su</div>
                    </div>

                    <!-- Dynamic Days grid structural rendering loop layout block element -->
                    <div class="grid grid-cols-7 gap-1 text-sm text-center">
                        <div 
                            v-for="(item, index) in calendarDays" 
                            :key="index"
                            @click="selectDateHandler(item.dayNumber)"
                            class="p-2 rounded transition-all duration-200"
                            :class="[
                                item.dayNumber ? 'cursor-pointer hover:bg-blue-100' : '',
                                selectedDate === `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(item.dayNumber).padStart(2, '0')}`
                                    ? 'bg-blue-600 text-white font-bold hover:bg-blue-600' 
                                    : 'text-gray-700'
                            ]"
                        >
                            {{ item.dayNumber }}
                        </div>
                    </div>
                </div>

                <!-- Showing selection confirmation string text message status dynamically -->
                <div v-if="selectedDate" class="mb-4 text-sm text-green-600 font-medium">
                    Selected Date: <span class="underline">{{ selectedDate }}</span>
                </div>

                <!-- Control Buttons for closing modal interface -->
                <div class="flex justify-center gap-2 mt-4">
                    <button 
                        @click="closeModal($event)" 
                        class="relative overflow-hidden bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-6 rounded-md transition duration-200"
                    >
                        Cancel
                    </button>
                    <button 
                        :disabled="!selectedDate"
                        @click="alert(`Confirmed for date: ${selectedDate}`)"
                        class="font-medium py-2 px-6 rounded-md transition duration-200 text-white"
                        :class="selectedDate ? 'bg-green-600 hover:bg-green-700' : 'bg-gray-300 cursor-not-allowed'"
                    >
                        Confirm Rent
                    </button>
                </div>
                
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
:deep(.ripple-effect) {
    position: absolute;
    top: 50%;
    left: 50%;
    width: 160px;
    height: 160px;
    background: rgba(255, 255, 255, 0.5);
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