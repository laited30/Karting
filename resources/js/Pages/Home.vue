<script setup>
// --- IMPORTS ---
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import { ref, computed } from 'vue';

// --- PROPS DEFINITION ---
defineProps({
    karts: Array
}); 

// --- GENERAL REACTIVE STATE ---
const isModalOpen = ref(false);
const selectedKart = ref(null);
const selectedDate = ref(null);

// --- CALENDAR REACTIVE STATE ---
const calendarDate = ref(new Date());
const currentMonth = computed(() => calendarDate.value.getMonth());
const currentYear = computed(() => calendarDate.value.getFullYear());

const monthNames = [
    "January", "February", "March", "April", "May", "June",
    "July", "August", "September", "October", "November", "December"
];

// --- CALENDAR COMPUTED MATRIX LOGIC ---
const calendarDays = computed(() => {
    const year = currentYear.value;
    const month = currentMonth.value;

    let firstDayOfWeek = new Date(year, month, 1).getDay();
    firstDayOfWeek = firstDayOfWeek === 0 ? 6 : firstDayOfWeek - 1; 

    const totalDaysInMonth = new Date(year, month + 1, 0).getDate();
    const daysArray = [];

    for (let i = 0; i < firstDayOfWeek; i++) {
        daysArray.push({ dayNumber: null, isCurrentMonth: false });
    }

    for (let day = 1; day <= totalDaysInMonth; day++) {
        daysArray.push({ dayNumber: day, isCurrentMonth: true });
    }

    return daysArray;
});

// --- TIME SELECTION STORAGE & SCHEDULING ---
const selectedTimes = ref([]);
const bookedTimes = ref([]); // Сюди записуватимемо зайнятий час із бази
const availableHours = [11, 12, 13, 14, 15, 16, 17, 18];
const availableMinutes = ['00', '10', '20', '30', '40', '50'];

// Функція для завантаження зайнятого часу з сервера
const fetchBookedTimes = async () => {
    if (!selectedKart.value || !selectedDate.value) return;
    
    try {
        const response = await fetch(`/karts/${selectedKart.value.id}/booked-times?date=${selectedDate.value}`);
        if (response.ok) {
            bookedTimes.value = await response.json();
        }
    } catch (error) {
        console.error("Не вдалося завантажити заброньовані слоти:", error);
    }
};

// Хелпер для перевірки, чи слот зайнятий
const isTimeBooked = (hour, minute) => {
    return bookedTimes.value.includes(`${hour}:${minute}`);
};

const toggleTimeSelection = (hour, minute) => {
    const timeString = `${hour}:${minute}`;
    if (isTimeBooked(hour, minute)) return; // Запобіжник

    const index = selectedTimes.value.indexOf(timeString);
    if (index > -1) {
        selectedTimes.value.splice(index, 1);
    } else {
        selectedTimes.value.push(timeString);
    }
};

const isTimeSelected = (hour, minute) => {
    return selectedTimes.value.includes(`${hour}:${minute}`);
};

const handleConfirmRent = () => {
    router.post(route('bookings.store'), {
        kart_id: selectedKart.value.id,
        date: selectedDate.value,
        times: selectedTimes.value 
    }, {
        preserveScroll: true,
        onSuccess: () => {
            isModalOpen.value = false;
            setTimeout(() => {
                selectedKart.value = null;
                selectedDate.value = null;
                selectedTimes.value = [];
                bookedTimes.value = [];
            }, 300);
            alert('Успішно заброньовано!'); 
        },
        onError: (errors) => {
            console.error('Деталі помилки:', errors);
            if (errors.message) {
                alert(errors.message);
            } else {
                const validationErrors = Object.values(errors).flat().join('\n');
                alert('Помилка валідації:\n' + (validationErrors || 'Невідома помилка сервера.'));
            }
        }
    });
};

// --- UTILITY EFFECTS & UI HANDLERS ---
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
    calendarDate.value = new Date();
    
    setTimeout(() => {
        isModalOpen.value = true;
    }, 150);
};

const prevMonth = () => {
    calendarDate.value = new Date(currentYear.value, currentMonth.value - 1, 1);
};

const nextMonth = () => {
    calendarDate.value = new Date(currentYear.value, currentMonth.value + 1, 1);
};

const selectDateHandler = (dayNum) => {
    if (!dayNum) return;
    selectedDate.value = `${currentYear.value}-${String(currentMonth.value + 1).padStart(2, '0')}-${String(dayNum).padStart(2, '0')}`;
    selectedTimes.value = []; // Очищуємо вибраний час при зміні дати
    fetchBookedTimes();       // Завантажуємо зайняті слоти для нової дати
};

const closeModal = (event) => {
    playRipple(event);
    setTimeout(() => { isModalOpen.value = false; }, 150);
    setTimeout(() => {
        selectedKart.value = null;
        selectedDate.value = null;
        selectedTimes.value = [];
        bookedTimes.value = [];
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

        <Modal :show="isModalOpen" maxWidth="40%" @close="closeModal">
            <div class="p-6 text-center bg-background-panel text-sup-white rounded-lg">
                
                <h3 class="text-xl font-bold mb-2">Rent Kart No. {{ selectedKart?.number }}</h3>
                <p class="text-sm text-gray-400 mb-6">Select your preferred date and time below:</p>
                
                <div class="flex flex-col md:flex-row gap-6 items-start justify-center">
                    
                    <div class="w-full md:w-1/2 border border-gray-700 rounded-lg p-4 bg-background">
                        <div class="flex justify-between items-center mb-4">
                            <button @click="prevMonth" class="text-gray-400 hover:text-border-green font-bold px-2 py-1 transition-colors">&lt; Prev</button>
                            <span class="font-bold text-md">{{ monthNames[currentMonth] }} {{ currentYear }}</span>
                            <button @click="nextMonth" class="text-gray-400 hover:text-border-green font-bold px-2 py-1 transition-colors">Next &gt;</button>
                        </div>

                        <div class="grid grid-cols-7 gap-1 text-xs font-bold text-gray-500 mb-2 text-center">
                            <div>Mo</div><div>Tu</div><div>We</div><div>Th</div><div>Fr</div><div>Sa</div><div>Su</div>
                        </div>

                        <div class="grid grid-cols-7 gap-1 text-sm text-center">
                            <div 
                                v-for="(item, index) in calendarDays" 
                                :key="index"
                                @click="selectDateHandler(item.dayNumber)"
                                class="p-2 rounded transition-all duration-200"
                                :class="[
                                    item.dayNumber ? 'cursor-pointer hover:bg-background-button' : '',
                                    selectedDate === `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(item.dayNumber).padStart(2, '0')}`
                                        ? 'bg-border-green text-background font-bold hover:brightness-110' 
                                        : 'text-gray-300'
                                ]"
                            >
                                {{ item.dayNumber }}
                            </div>
                        </div>
                    </div>

                    <div class="w-full md:w-1/2 text-left bg-background border border-gray-700 rounded-lg p-4 md:min-h-[310px] flex flex-col">
                        <h4 class="text-md font-bold mb-4">Select time for rent:</h4>
                        
                        <div v-if="!selectedDate" class="text-sm text-gray-500 flex items-center justify-center flex-1 text-center">
                            Please at first select the date
                        </div>

                        <div v-else class="overflow-x-auto pb-4 flex-1">
                            <div class="grid grid-cols-8 gap-4 min-w-[400px] text-center">
                                
                                <div v-for="hour in availableHours" :key="'hour-' + hour" class="flex flex-col gap-2 min-h-[80px]">
                                    
                                    <div class="font-bold text-gray-300 p-1 bg-background-input rounded-md text-xs">
                                        {{ hour }}:00
                                    </div>
                                    
                                    <div class="flex flex-col gap-1.5">
                                        <div
                                            v-for="minute in availableMinutes"
                                            :key="'time-' + hour + '-' + minute"
                                            @click="toggleTimeSelection(hour, minute)"
                                            class="p-2 border rounded-md text-xs text-center transition-all duration-200 select-none"
                                            :class="[
                                                isTimeBooked(hour, minute)
                                                    ? 'bg-red-600 text-white border-red-700 font-semibold cursor-not-allowed pointer-events-none opacity-90'
                                                    : selectedTimes.includes(hour + ':' + minute)
                                                        ? 'bg-border-green text-background border-border-green font-bold shadow-sm'
                                                        : 'bg-background-button text-gray-300 hover:bg-background-input border-gray-700'
                                            ]"
                                        >
                                            {{ minute }}
                                        </div>
                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-center gap-2 mt-6 border-t border-gray-700 pt-4">
                    <button 
                        @click="closeModal($event)" 
                        class="relative overflow-hidden bg-background-button hover:bg-background-input border border-gray-600 text-sup-white font-medium py-2 px-6 rounded-md transition duration-200"
                    >
                        Cancel
                    </button>
                    <button 
                        :disabled="!selectedDate || selectedTimes.length === 0"
                        @click="handleConfirmRent"
                        class="font-medium py-2 px-6 rounded-md transition duration-200"
                        :class="selectedDate && selectedTimes.length > 0 ? 'bg-border-green hover:brightness-110 text-background' : 'bg-background-input text-gray-500 cursor-not-allowed'"
                    >
                        Confirm Rent
                    </button>
                </div>
                
            </div>
        </Modal>
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