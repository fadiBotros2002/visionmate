<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Users';
    protected static ?string $pluralModelLabel = 'Users';
    protected static ?string $modelLabel = 'User';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('username')->required()->unique(ignoreRecord: true),
                TextInput::make('phone')->required()->unique(ignoreRecord: true),
                TextInput::make('password')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null)
                    ->label('Password')
                    ->nullable()
                    ->afterStateHydrated(fn ($component, $state, $record) => $component->state('')),

                Select::make('role')
                    ->options([
                        'admin' => 'Admin',
                        'volunteer' => 'Volunteer',
                        'blind' => 'Blind',
                    ])
                    ->required(),

                TextInput::make('email')->email()->nullable(),
                TextInput::make('latitude')->numeric()->nullable(),
                TextInput::make('longitude')->numeric()->nullable(),

                FileUpload::make('identity_image')
                    ->image()
                    ->disk('public')
                    ->directory('identity_images')
                    ->nullable()
                    ->label('Identity Image')
                    ->preserveFilenames()
                    ->enableDownload(),

                TextInput::make('average_rating')
                    ->numeric()
                    ->label('Average Rating')
                    ->disabled()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user_id')->sortable(),
                TextColumn::make('username')->searchable(),
                TextColumn::make('phone'),
                TextColumn::make('role'),
                TextColumn::make('average_rating')->label('Avg. Rating')->numeric(),

               
                TextColumn::make('identity_image')
                    ->label('Identity Image')
                    ->formatStateUsing(function ($state) {
                        if ($state) {
                            return '<a href="' . url('storage/identity_images/' . basename($state)) . '" target="_blank">Open</a>';
                        }
                        return 'No Image';
                    })
                    ->html(),

                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
