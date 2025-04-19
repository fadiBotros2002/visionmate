<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;

class UserCountChart extends ChartWidget
{
    protected static ?string $heading = 'User Roles Distribution';

    protected function getData(): array
    {
        // احصائيات الأدوار
        $volunteerCount = User::where('role', 'volunteer')->count() ?: 0;
        $blindCount = User::where('role', 'blind')->count() ?: 0;

        return [
            'datasets' => [
                [
                    'label' => 'User Roles',
                    'data' => [$volunteerCount, $blindCount],
                    'backgroundColor' => ['#10B981', '#6366F1'], // ألوان مخصصة لكل فئة
                ],
            ],
            'labels' => ['Volunteer', 'Blind'],
        ];
    }

    protected function getType(): string
    {
        return 'pie'; // نوع الرسم البياني
    }
}
