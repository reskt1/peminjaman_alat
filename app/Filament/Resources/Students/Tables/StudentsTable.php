<?php

namespace App\Filament\Resources\Students\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Grid;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;


class StudentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
        ->contentGrid([
                'xl' => 4,
                'lg' => 3,
                'md' => 2,
            ])
            ->columns([
                Grid::make([
                    'default' => 1
                ])->schema([
                    ImageColumn::make('profile_picture')
                    ->label('Profile Picture')
                    ->imageSize(200)
                    ->getStateUsing(function ($record) {
                        if (! $record->profile_picture) {
                            return null;
                        }

                        return asset('storage/'.$record->profile_picture);
                    }),
                    Stack::make([
                        TextColumn::make('user.name')
                    ->label('Student Name')
                    ->Searchable()
                    ->weight(FontWeight::Bold)
                    ->sortable(),

                TextColumn::make('nisn')
                    ->label('NISN')
                    ->searchable()
                    ->icon(Heroicon::Identification),
                TextColumn::make('classroom.name')
                    ->label('Class')
                    ->Searchable()
                    ->sortable()
                    ->icon(Heroicon::BuildingOffice),
                TextColumn::make('phone_number')
                    ->label('Phone Number')
                    ->searchable()
                    ->icon(Heroicon::Phone),
                TextColumn::make('gender')
                    ->label('Gender')
                    ->badge(),
                ]),
                    ]),

            ])
            ->filters([
                //
            ])
            ->recordActions([
                 ViewAction::make(),
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
