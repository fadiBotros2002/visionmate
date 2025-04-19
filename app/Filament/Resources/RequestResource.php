<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RequestResource\Pages;
use App\Models\BlindRequest;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;

class RequestResource extends Resource
{
    protected static ?string $model = BlindRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-paper-airplane';
    protected static ?string $navigationLabel = 'Requests';
    protected static ?string $pluralModelLabel = 'Requests';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('request_id')->sortable(),
                TextColumn::make('blinds.username')->label('Blind'),
                TextColumn::make('volunteers.username')->label('Volunteer')->default('—'),
                BadgeColumn::make('status')
                    ->colors([
                        'primary' => 'pending',
                        'success' => 'accepted',
                        'danger' => 'expired',
                    ]),
                IconColumn::make('is_rated')
                    ->boolean()
                    ->label('Rated'),
                TextColumn::make('created_at')->label('Created')->dateTime(),
                // Add the blind_location column
                TextColumn::make('blind_location')->label('Blind Location')
                    ->getStateUsing(fn ($record) => $record->blind_location ?? '—'), // This ensures null values are shown as a dash
            ])
            ->filters([
                SelectFilter::make('blind_id')
                    ->relationship('blinds', 'username')
                    ->label('Filter by Blind'),
                SelectFilter::make('volunteer_id')
                    ->relationship('volunteers', 'username')
                    ->label('Filter by Volunteer'),
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'accepted' => 'Accepted',
                        'expired' => 'Expired',
                    ]),
            ])
            ->actions([]) // If no actions are needed
            ->bulkActions([]); // If no bulk actions are needed
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRequests::route('/'),
            // Do not add create page to hide the "Create New Request" button
        ];
    }

    // Disable the create page completely
    public static function canCreate(): bool
    {
        return false; // Prevents the "Create New Request" button from appearing
    }
}
