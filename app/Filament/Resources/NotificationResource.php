<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NotificationResource\Pages;
use App\Models\Notification;
use App\Models\User;
use App\Models\BlindRequest;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;

class NotificationResource extends Resource
{
    protected static ?string $model = Notification::class;

    protected static ?string $navigationIcon = 'heroicon-o-bell';
    protected static ?string $navigationLabel = 'Notifications';
    protected static ?string $pluralModelLabel = 'Notifications';

    // Admin notification form
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Optional: Select volunteer (if null, send to all volunteers)
                Select::make('volunteer_id')
                    ->label('Send To Volunteer')
                    ->options(User::where('role', 'volunteer')->pluck('username', 'user_id')->toArray())
                    ->searchable()
                    ->helperText('Leave empty to send to all volunteers')
                    ->nullable(),

                // Message field
                Textarea::make('message')
                    ->label('Message')
                    ->required(),

                // Type (hidden or fixed to 'admin')
                Hidden::make('type')->default('admin'),

                // Read status
                Select::make('is_read')
                    ->options([
                        true => 'Read',
                        false => 'Unread',
                    ])
                    ->default(false)
                    ->label('Read Status'),

                // Optional: custom created_at
                DateTimePicker::make('created_at')
                    ->label('Created At')
                    ->nullable(),
            ]);
    }

    // Notification table
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('notification_id')->sortable()->label('Notification ID'),
                TextColumn::make('volunteer.username')->label('Volunteer'),
                TextColumn::make('request.request_id')->label('Request ID'),
                TextColumn::make('message')->label('Message')->limit(50),
                BadgeColumn::make('type')
                    ->colors([
                        'primary' => 'certificate',
                        'info' => 'reminder',
                        'secondary' => 'general',
                        'warning' => 'admin',
                    ])
                    ->label('Type'),
                BadgeColumn::make('is_read')
                    ->colors([
                        'success' => true,
                        'danger' => false,
                    ])
                    ->label('Read Status'),
                TextColumn::make('created_at')->label('Created')->dateTime(),
            ])
            ->filters([
                // Filter by volunteer
                \Filament\Tables\Filters\SelectFilter::make('volunteer_id')
                    ->label('Volunteer')
                    ->options(User::pluck('username', 'user_id')->toArray()),

                // Filter by request
                \Filament\Tables\Filters\SelectFilter::make('request_id')
                    ->label('Request')
                    ->options(BlindRequest::pluck('request_id', 'request_id')->toArray()),

                // Filter by read status
                \Filament\Tables\Filters\SelectFilter::make('is_read')
                    ->label('Read Status')
                    ->options([
                        true => 'Read',
                        false => 'Unread',
                    ]),
            ])
            ->actions([
                \Filament\Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                \Filament\Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNotifications::route('/'),
            'create' => Pages\CreateNotification::route('/create'),
            'edit' => Pages\EditNotification::route('/{record}/edit'),
        ];
    }

    // Send notification to one or all volunteers
    public static function sendNotification(array $data): void
    {
        if (!empty($data['volunteer_id'])) {
            // Send to one volunteer
            Notification::create([
                'volunteer_id' => $data['volunteer_id'],
                'message'      => $data['message'],
                'type'         => 'admin',
                'is_read'      => false,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        } else {
            // Send to all volunteers
            $volunteers = User::where('role', 'volunteer')->get();
            foreach ($volunteers as $volunteer) {
                Notification::create([
                    'volunteer_id' => $volunteer->user_id,
                    'message'      => $data['message'],
                    'type'         => 'admin',
                    'is_read'      => false,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
            }
        }
    }
}
