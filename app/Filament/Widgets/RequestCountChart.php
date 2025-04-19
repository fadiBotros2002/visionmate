<?php

namespace App\Filament\Widgets;

use App\Models\BlindRequest;
use Filament\Widgets\ChartWidget;

class RequestCountChart extends ChartWidget
{
    protected static ?string $heading = 'Requests Status Distribution';

    protected function getData(): array
    {
        // Get the count of accepted requests
        $acceptedCount = BlindRequest::where('status', 'accepted')->count() ?: 0;

        // Get the count of expired requests
        $expiredCount = BlindRequest::where('status', 'expired')->count() ?: 0;

        // Get the count of pending requests
        $pendingCount = BlindRequest::where('status', 'pending')->count() ?: 0;

        // Return the data for the pie chart
        return [
            'datasets' => [
                [
                    'label' => 'Requests Status',
                    'data' => [$acceptedCount, $expiredCount, $pendingCount],
                    'backgroundColor' => ['#10B981', '#6366F1', '#F59E0B'],
                ],
            ],
            'labels' => ['Accepted', 'Expired', 'Pending'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
