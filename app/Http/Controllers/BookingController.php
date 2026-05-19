<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use Carbon\Carbon;
use App\Models\Kart;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        // 1. Валідація даних з фронтенду
        $validated = $request->validate([
            'kart_id' => 'required|exists:karts,id',
            'date'    => 'required|date|after_or_equal:today',
            'times'   => 'required|array',
            'times.*' => 'string|date_format:H:i'
        ]);

        $userId = auth()->id();
        $date = $validated['date'];

        // 2. Обробка кожного обраного часу
        foreach ($validated['times'] as $time) {
            // Створюємо Carbon об'єкти для початку та кінця поїздки (10 хвилин)
            $startTime = Carbon::parse("{$date} {$time}");
            $endTime = $startTime->copy()->addMinutes(10);

            // 3. Перевірка на конфлікти (чи не зайнятий карт в цей час)
            $conflict = Booking::where('kart_id', $validated['kart_id'])
                ->where('start_time', '<', $endTime)
                ->where('end_time', '>', $startTime)
                ->whereIn('status', ['pending', 'confirmed']) // Враховуємо тільки активні броні
                ->exists();

            if ($conflict) {
                return back()->withErrors([
                    'message' => "Час {$time} вже заброньовано для цього карта. Оберіть інший."
                ]);
            }

            // 4. Створення запису в базі
            Booking::create([
                'user_id'    => $userId,
                'kart_id'    => $validated['kart_id'],
                'start_time' => $startTime,
                'end_time'   => $endTime,
                'status'     => 'confirmed', // Або 'pending', залежно від логіки бізнесу
            ]);
        }

        return redirect()->back()->with('success', 'Успішно заброньовано!');
    }
    public function bookedTimes(Request $request, Kart $kart)
    {
        $date = $request->query('date');

        if (!$date) {
            return response()->json([]);
        }

        // Шукаємо всі активні бронювання для цього карта на вибрану дату
        $bookings = Booking::where('kart_id', $kart->id)
            ->whereDate('start_time', $date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->get();

        // Форматуємо час у масив рядків типу ["11:00", "11:10"]
        $bookedTimes = $bookings->map(function ($booking) {
            return Carbon::parse($booking->start_time)->format('H:i');
        })->unique()->values();

        return response()->json($bookedTimes);
    }
}