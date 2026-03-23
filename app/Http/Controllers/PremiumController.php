<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PremiumController extends Controller
{
    private $plans = [
        'quarterly' => 90,
        'yearly' => 365
    ];

    public function purchase(Request $request)
    {
        $request->validate([
            'plan' => 'required|in:quarterly,yearly'
        ]);

        $user = Auth::user();
        $plan = $request->input('plan');

        if ($plan == '') {
            return back()->with('error', 'Выберите один из доступных вариантов!');
        }

        // Проверяем не активна ли уже подписка
        if ($user->is_premium && $user->premium_until > now()) {
            return back()->with('error', 'У вас уже активна премиум подписка!');
        }

        // Обработка "платежа" (заглушка)
        $paymentSuccess = $this->processPayment($plan);
        
        if ($paymentSuccess) {
            $days = $this->plans[$plan];
            $premiumUntil = Carbon::now()->addDays($days);
            
            $user->update([
                'is_premium' => true,
                'premium_until' => $premiumUntil
            ]);

            session(['is_premium' => true]);

            return redirect()->route('profile');
        }

        return back()->with('error', 'Ошибка при обработке платежа');
    }

    /**
     * Проверка активности премиум подписки
     */
    public function checkPremiumStatus()
    {
        $user = Auth::user();
        
        // Автоматически снимаем премиум если срок истек
        if ($user->is_premium && $user->premium_until > now()) {
            $user->update(['is_premium' => false]);
        }

        $isPremium = $user->is_premium;
        session(['is_premium' => $isPremium]);

        return redirect()->route('profile');
    }

    private function processPayment(string $plan): bool
    {
        // В реальном приложении здесь будет интеграция с платежной системой
        // Пока всегда возвращаем true для демо
        return true;
    }
}
