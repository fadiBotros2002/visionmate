<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;


class UserChart extends ChartWidget
{
    // عنوان الـ widget
    protected static ?string $heading = 'User Growth Over Time';

    // طريقة للحصول على البيانات التي سيتم عرضها
    protected function getData(): array
    {
        // استرجاع البيانات من نموذج User (عدد المستخدمين الجدد على مدار الشهر)
        $realData = Trend::model(User::class)
            ->between(
                start: now()->subMonths(1)->startOfMonth(),
                end: now()->endOfMonth(),
            )
            ->perMonth()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'New Users',
                    'data' => $realData->map(fn(TrendValue $value) => $value->aggregate),
                    'backgroundColor' => '#36A2EB',
                    'borderColor' => '#36A2EB',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $realData->map(fn(TrendValue $value) => $value->date),
        ];
    }

    // نوع الرسم البياني (مثل خط أو عمودي)
    protected function getType(): string
    {
        return 'line';  // هنا نستخدم الرسم البياني من النوع line
    }
}
