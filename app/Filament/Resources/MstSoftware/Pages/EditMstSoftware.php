<?php

namespace App\Filament\Resources\MstSoftware\Pages;


use App\Filament\Resources\MstSoftware\MstSoftwareResource;
use App\Filament\Widgets\SoftwareLicenseSummary;


use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;


use Livewire\Attributes\On;



class EditMstSoftware extends EditRecord
{


    protected static string $resource = MstSoftwareResource::class;







    /**
     * Widget tampil di bagian atas halaman edit
     */
    // protected function getHeaderWidgets(): array
    // {

    //     return [

    //         SoftwareLicenseSummary::class,

    //     ];

    // }









    protected function getHeaderActions(): array
    {

        return [





            /*
             |--------------------------------------------------------------------------
             | PRODUCT KEY MODAL
             |--------------------------------------------------------------------------
             */

            // Action::make('productKey')

            //     ->label('Product Key / License')

            //     ->icon('heroicon-o-key')

            //     ->color('primary')


            //     ->modalHeading('PRODUCT KEY / LICENSE')


            //     ->modalWidth('5xl')


            //     ->form([


            //         \Filament\Forms\Components\Placeholder::make('info')

            //             ->label('Informasi')

            //             ->content(
            //                 'Daftar product key dan license software'
            //             ),


            //     ])


            //     ->modalSubmitAction(false)

            //     ->modalCancelActionLabel('Tutup'),








            /*
             |--------------------------------------------------------------------------
             | REFRESH
             |--------------------------------------------------------------------------
             */

            Action::make('refresh')

                ->label('Refresh')

                ->icon('heroicon-o-arrow-path')

                ->color('gray')


                ->action(function () {



                    $this->record->refresh();



                    $this->fillForm();



                    $this->dispatch(
                        'refreshSoftware'
                    );





                    Notification::make()

                        ->title(
                            'Data berhasil diperbarui'
                        )

                        ->body(
                            'Data software dan statistik license telah diperbarui.'
                        )

                        ->success()

                        ->send();


                }),








            DeleteAction::make(),


        ];

    }









    /**
     * Listener refresh widget
     */
    #[On('refreshSoftware')]

    public function refreshSoftware(): void
    {


        $this->record->refresh();


        $this->fillForm();


    }



}