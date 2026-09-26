<?php

namespace App\Filament\Resources\Assets\Schemas;

use App\Models\Asset;
use App\Models\Category;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;


class AssetForm
{
    protected static function recalculateStock(Get $get, Set $set):void{
        $good = (int) $get('good_qty');
        $damaged = (int) $get('damaged_qty');
        $borrowed = (int) $get('borrowed_qty');
        $lost = (int) $get('lost_qty');

        $set('available_qty', $good-$borrowed);
        $set('total_qty', $good + $damaged + $borrowed + $lost);


    }
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                ->schema([
                    Fieldset::make('Asset Details')
                    ->schema([
                         Select::make('category_id')
                                    ->required()
                                    ->relationship('category', 'name')
                                    ->label('Category')
                                    ->reactive()
                                    ->afterStateUpdated(function(Get $get, Set $set)
                                    {
                                        $category = Category::find($get('category_id'));

                                        if(!$category){
                                            return;
                                        } 

                                        $prefix = strtoupper(Str::substr($category->name,0,3));

                                        $lastCode = Asset::where('code', 'like', $prefix. '%')
                                            ->orderBy('code','desc')
                                            ->value('code');

                                        if($lastCode){
                                            $number = (int) subStr($lastCode, 3);
                                            $nextNumber = $number + 1;
                                        }else{
                                            $nextNumber = 1;
                                        }

                                        $code = $prefix .str_pad($nextNumber, 3,'0', STR_PAD_LEFT);
                                        $set('code', $code);
                                    }),
                                TextInput::make('code')
                                    ->readOnly()
                                    ->reactive()
                                    ->required(),
                        TextInput::make('name')
                            ->required()
                            ->columnSpanFull(),
                        ]),
                        Toggle::make('is_available')
                            ->required()
                            ->label('Status'),
                ])->columnSpan(2),
                
                Fieldset::make('Asset Condition / Stock')
                    ->schema([
                        
                        TextInput::make('good_qty')
                            ->numeric()
                            ->required()
                            ->label('Good')
                            ->default(0)
                            ->reactive()
                            ->afterStateUpdated(fn(Get $get, Set $set)=>self::recalculateStock($get, $set)),
                        TextInput::make('damaged_qty')
                            ->numeric() 
                            ->required()
                            ->label('Damage')
                            ->default(0)
                            ->reactive()
                            ->afterStateUpdated(fn(Get $get, Set $set)=>self::recalculateStock($get, $set)),
                        TextInput::make('borrowed_qty')
                            ->numeric()
                            ->required()
                            ->label('Borrowed')
                            ->default(0)
                            ->reactive()
                            ->afterStateUpdated(fn(Get $get, Set $set)=>self::recalculateStock($get, $set)),
                        TextInput::make('lost_qty')
                            ->numeric()
                            ->required()
                            ->label('Lost')
                            ->default(0)->reactive()
                            ->afterStateUpdated(fn(Get $get, Set $set)=>self::recalculateStock($get, $set)),
                        TextInput::make('available_qty')
                            ->numeric()
                            ->belowContent('Available Asset for boarding')
                            ->readOnly()
                            ->required()
                            ->label('Available')
                            ->default(0),
                        TextInput::make('total_qty')
                            ->numeric()
                            ->required()
                            ->label('Total')
                            ->default(0),
                        ])->columnSpan(1),
                    
            ])->columns(3);
    }
}
