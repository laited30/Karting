<script setup>
// --- IMPORTS ---
// Importing core layouts, tools, and reactive hooks
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import { ref, computed } from 'vue';

// --- PROPS DEFINITION ---
// Defines data passed from the parent component/backend controller
defineProps({
    karts: Array // Collection of available go-kart objects containing id, number, and status
}); 

// --- GENERAL REACTIVE STATE ---
// Tracks basic modal UI visibility and currently selected kart context
const isModalOpen = ref(false);       // Controls the opening/closing state of the rental Modal dialog
const selectedKart = ref(null);       // Holds the full object data of the kart clicked for rent
const selectedDate = ref(null);       // Stores the finalized string representation of the chosen date (YYYY-MM-DD)

// --- CALENDAR REACTIVE STATE ---
// Manages the active visual viewport window of the interactive calendar
const calendarDate = ref(new Date()); // Tracks active state for current month/year navigation shifts
const currentMonth = computed(() => calendarDate.value.getMonth()); // Extracts active month index (0-11)
const currentYear = computed(() => calendarDate.value.getFullYear()); // Extracts active calendar year digits

// Array of English month names for the calendar header display
const monthNames = [
    "January", "February", "March", "April", "May", "June",
    "July", "August", "September", "October", "November", "December"
];

// --- CALENDAR COMPUTED MATRIX LOGIC ---
// Builds a sequential layout list mapping out empty padding grids and day numbers for the active month view
const calendarDays = computed(() => {
    const year = currentYear.value;
    const month = currentMonth.value;

    // Find the weekday index of the month's 1st day (0 = Sunday, 1 = Monday...)
    // Shift computation layout indexes to explicitly start weeks on a European standard Monday
    let firstDayOfWeek = new Date(year, month, 1).getDay();
    firstDayOfWeek = firstDayOfWeek === 0 ? 6 : firstDayOfWeek - 1; 

    // Extract the maximum total volume of calendar days matching the selected targeted month
    const totalDaysInMonth = new Date(year, month + 1, 0).getDate();

    const daysArray = [];

    // Push blank spacing placeholders to safely offset Grid items before Day 1 begins
    for (let i = 0; i < firstDayOfWeek; i++) {
        daysArray.push({ dayNumber: null, isCurrentMonth: false });
    }

    // Populate active calendar slots sequentially with valid incremented day numbers
    for (let day = 1; day <= totalDaysInMonth; day++) {
        daysArray.push({ dayNumber: day, isCurrentMonth: true });
    }

    return daysArray;
});

// --- TIME SELECTION STORAGE & SCHEDULING ---
// Houses selection logic and presets representing booking intervals
const selectedTimes = ref([]); // Tracks multiple formatted string selections (e.g., ["11:00", "12:30"])
const availableHours = [11, 12, 13, 14, 15, 16, 17, 18]; // Array defining columns for operating business hours
const availableMinutes = ['00', '10', '20', '30', '40', '50']; // Segmented minute slots displayed within columns

// Array manipulator toggling chosen hour-and-minute combinations into booking memory
const toggleTimeSelection = (hour, minute) => {
    const timeString = `${hour}:${minute}`;
    const index = selectedTimes.value.indexOf(timeString);
    
    // Remove slot if already selected, otherwise push it into the array queue
    if (index > -1) {
        selectedTimes.value.splice(index, 1);
    } else {
        selectedTimes.value.push(timeString);
    }
};

// Helper conditional: determines active selection highlighting matches inside template loops
const isTimeSelected = (hour, minute) => {
    return selectedTimes.value.includes(`${hour}:${minute}`);
};

// Orchestrates final rent operations, dispatches alerts, and clears modal layout cache safely
const handleConfirmRent = () => {
    // Notify the user with booking summary statistics
    alert(`Succesful rent!\nDate: ${selectedDate.value}\nTime: ${selectedTimes.value.join(', ')}\nKart: ${selectedKart.value.number}`);
    
    // Immediate dismissal of modal window views without passing functional event parameters
    isModalOpen.value = false;
    
    // Delay field data flush animations to align seamlessly with modal close transition timings
    setTimeout(() => {
        selectedKart.value = null;
        selectedDate.value = null;
        selectedTimes.value = [];
    }, 300);
};


// --- UTILITY EFFECTS & UI HANDLERS ---
// Generates an interactive floating CSS material wave effect on custom button nodes
const playRipple = (event) => {
    const button = event.currentTarget;
    const circle = document.createElement("span");
    circle.classList.add("ripple-effect");
    button.appendChild(circle);

    // Completely strip injected ripple DOM elements out of active layout tree after animation expiration
    setTimeout(() => {
        circle.remove();
    }, 400);
};

// Bootstraps configuration states when initiating a rental order flow
const handleRentClick = (event, kart) => {
    playRipple(event); // Render ripple wave overlay indicator
    selectedKart.value = kart; // Cache target kart context references
    calendarDate.value = new Date(); // Reset active calendar display pagination back to real-time present month
    
    // Open dialog sequence matching hardware drawing speeds
    setTimeout(() => {
        isModalOpen.value = true;
    }, 150);
};

// Navigation method shifting current monthly viewpoint backward by exactly 1 calendar cycle
const prevMonth = () => {
    calendarDate.value = new Date(currentYear.value, currentMonth.value - 1, 1);
};

// Navigation method shifting current monthly viewpoint forward by exactly 1 calendar cycle
const nextMonth = () => {
    calendarDate.value = new Date(currentYear.value, currentMonth.value + 1, 1);
};

// Captures clicks on grid days and converts raw numerical inputs into complete YYYY-MM-DD date patterns
const selectDateHandler = (dayNum) => {
    if (!dayNum) return; // Prevent calculation errors if blank grid margins are mistakenly clicked
    selectedDate.value = `${currentYear.value}-${String(currentMonth.value + 1).padStart(2, '0')}-${String(dayNum).padStart(2, '0')}`;
};

// Gracefully handles modal cancellations by reversing tracking states using standard asset timers
const closeModal = (event) => {
    playRipple(event);
    setTimeout(() => { isModalOpen.value = false; }, 150);
    setTimeout(() => {
        selectedKart.value = null;
        selectedDate.value = null;
        selectedTimes.value = [];
    }, 300);
};
</script>

<template>
    <!-- Injects custom page titles into application document header components -->
    <Head title="Home" />

    <!-- Application wrapper core structural layout wrapper containing global style guidelines -->
    <AuthenticatedLayout>
        <!-- Slot injection area populating primary navigation bar headers -->
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-white-800">Home</h2>
        </template>

        <!-- Main section container housing inventory cards layout matrix -->
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-track-light overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold mb-4">Our go-karts are available for rent:</h3>
                    
                    <!-- Go-Kart grid layout generating item slots dynamically using backend props lists -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div v-for="kart in karts" :key="kart.id" class="border p-4 rounded shadow-sm">
                            <div>
                                <p class="font-semibold text-lg">{{ kart.number }}</p>
                                <p class="text-sm text-gray-500">Is ready: {{ kart.is_active }}</p>
                            </div>
                            
                            <!-- Main invocation trigger element starting the reservation routine configuration -->
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

        <!-- --- MODAL WINDOW OVERLAY POPUP --- -->
        <!-- Populates customized calendar interfaces inside atomic floating layout structures -->
        <Modal :show="isModalOpen" maxWidth="40%" @close="closeModal">
            <div class="p-6 text-center">
                
                <!-- Dynamic text header identifying target cart identifier numbers -->
                <h3 class="text-xl font-bold text-gray-900 mb-2">Rent Kart No. {{ selectedKart?.number }}</h3>
                <p class="text-sm text-gray-500 mb-6">Select your preferred date and time below:</p>
                
                <!-- Split columns wrapping calendar configuration on left side and scheduling tables on right side -->
                <div class="flex flex-col md:flex-row gap-6 items-start justify-center">
                    
                    <!-- LEFT COLUMN: The Interactive Calendar Module Block -->
                    <div class="w-full md:w-1/2 border rounded-lg p-4 bg-gray-50">
                        <!-- Navigation controls allowing pagination jumps across monthly calendar scopes -->
                        <div class="flex justify-between items-center mb-4">
                            <button @click="prevMonth" class="text-gray-600 hover:text-blue-600 font-bold px-2 py-1">&lt; Prev</button>
                            <span class="font-bold text-gray-800 text-md">{{ monthNames[currentMonth] }} {{ currentYear }}</span>
                            <button @click="nextMonth" class="text-gray-600 hover:text-blue-600 font-bold px-2 py-1">Next &gt;</button>
                        </div>

                        <!-- Weekdays row headers providing basic calendar positioning context metrics -->
                        <div class="grid grid-cols-7 gap-1 text-xs font-bold text-gray-400 mb-2 text-center">
                            <div>Mo</div><div>Tu</div><div>We</div><div>Th</div><div>Fr</div><div>Sa</div><div>Su</div>
                        </div>

                        <!-- Main Calendar Matrix Grid calculating month slots dynamically -->
                        <div class="grid grid-cols-7 gap-1 text-sm text-center">
                            <div 
                                v-for="(item, index) in calendarDays" 
                                :key="index"
                                @click="selectDateHandler(item.dayNumber)"
                                class="p-2 rounded transition-all duration-200"
                                :class="[
                                    item.dayNumber ? 'cursor-pointer hover:bg-blue-100' : '',
                                    // Applies distinct background modifications to explicitly flag the active target choice
                                    selectedDate === `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(item.dayNumber).padStart(2, '0')}`
                                        ? 'bg-blue-600 text-white font-bold hover:bg-blue-600' 
                                        : 'text-gray-700'
                                ]"
                            >
                                {{ item.dayNumber }}
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT COLUMN: Calendar-Style Horizontal Time Slots Scheduler Module -->
                    <div class="w-full md:w-1/2 text-left bg-gray-50 border rounded-lg p-4 md:min-h-[310px] flex flex-col">
                        <h4 class="text-md font-bold text-gray-800 mb-4">Select time for rent:</h4>
                        
                        <!-- Fallback block UI placeholder triggered when no date context is registered in system yet -->
                        <div v-if="!selectedDate" class="text-sm text-gray-400 flex items-center justify-center flex-1 text-center">
                            Please at first select the date
                        </div>

                        <!-- Active View Container: Activates horizontal scrolling parameters if columns outgrow view bounds -->
                        <div v-else class="overflow-x-auto pb-4 flex-1">
                            <!-- Matrix layout binding 8 parallel hour columns side by side at fixed column boundaries -->
                            <div class="grid grid-cols-8 gap-4 min-w-[400px] text-center ">
                                
                                <!-- Vertically grouped time slice arrays sorted under distinct parental hour keys -->
                                <div v-for="hour in availableHours" :key="'hour-' + hour" class="flex flex-col gap-2 min-h-[80px]">
                                    
                                    <!-- Column top header: Mirrors calendar design rules by setting fixed time markers -->
                                    <div class="font-bold text-gray-700 p-1 bg-gray-200 rounded-md text-xs">
                                        {{ hour }}:00
                                    </div>
                                    
                                    <!-- Sequential stack layout tracking clickable minute intervals directly under parent column marker -->
                                    <div class="flex flex-col gap-1.5">
                                        <!-- Dynamically switches button backgrounds if time calculation parameters match selection state -->
                                        <div
                                            v-for="minute in availableMinutes"
                                            :key="'time-' + hour + '-' + minute"
                                            @click="toggleTimeSelection(hour, minute)"
                                            class="p-2 border rounded-md text-xs text-center cursor-pointer transition-all duration-200 select-none"
                                            :class="selectedTimes.includes(hour + ':' + minute)
                                                ? 'bg-blue-600 text-white border-blue-600 font-bold shadow-sm'
                                                : 'bg-white text-gray-600 hover:bg-blue-100 border-gray-300'"
                                        >
                                            {{ minute }}
                                        </div>
                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Bottom navigation action menu housing transactional layout approval controllers -->
                <div class="flex justify-center gap-2 mt-6 border-t pt-4">
                    <!-- Standard rollback button closing operational screens safely -->
                    <button 
                        @click="closeModal($event)" 
                        class="relative overflow-hidden bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-6 rounded-md transition duration-200"
                    >
                        Cancel
                    </button>
                    <!-- Core processing button: validates system states prior to confirming transactional requests -->
                    <button 
                        :disabled="!selectedDate || selectedTimes.length === 0"
                        @click="handleConfirmRent"
                        class="font-medium py-2 px-6 rounded-md transition duration-200 text-white"
                        :class="selectedDate && selectedTimes.length > 0 ? 'bg-green-600 hover:bg-green-700' : 'bg-gray-300 cursor-not-allowed'"
                    >
                        Confirm Rent
                    </button>
                </div>
                
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Scoped injection rule allowing ripple classes to bypass isolated child component styles safely */
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
    pointer-events: none; /* Prevents visual effect elements from overriding mouse click captures */
}

/* Linear expansion timeline animating circular scale sizes outwards while applying fade masks */
@keyframes full-wave {
    0% { transform: translate(-50%, -50%) scale(0); opacity: 1; }
    100% { transform: translate(-50%, -50%) scale(1); opacity: 0; }
}
</style>