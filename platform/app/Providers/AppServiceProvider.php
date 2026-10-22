<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use TallStackUi\Facades\TallStackUi;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        TallStackUi::customize()->form('date')->block([
            'input.caret' => 'caret-transparent',
            'slot.icon-spacing' => 'ml-1.5 mr-2',
            'slot.icon-spacing-right' => 'mr-2',
            'wrapper.helpers' => 'custom-scrollbar flex items-center justify-between gap-2 overflow-auto pb-2',
            'wrapper.days-header' => 'mb-3 grid grid-cols-7',
            'wrapper.days-grid' => 'grid grid-cols-7',
            'floating.default' => 'absolute z-50 rounded-2xl border border-slate-600/40 bg-slate-900/96 shadow-[0_24px_60px_rgba(8,15,30,0.46)]',
            'floating.class' => 'w-[17rem] p-3',
            'floating.expanded' => 'h-[17rem]',
            'box.picker.button' => 'mb-6 flex items-center justify-between text-sm font-semibold text-slate-50',
            'box.picker.wrapper.first' => 'absolute left-0 top-0 flex h-full w-full select-none rounded-2xl border border-slate-600/40 bg-slate-900/96 p-3',
            'box.picker.wrapper.second' => 'flex w-full flex-wrap',
            'box.picker.wrapper.third' => 'flex h-12 w-full items-center justify-between px-1',
            'box.picker.label' => 'flex cursor-pointer items-center justify-between rounded-lg bg-slate-800/85 px-2 py-1 text-sm font-semibold text-slate-50',
            'box.picker.range' => 'flex h-6 w-1/4 cursor-pointer select-none items-center justify-center rounded-md p-1 text-center text-sm font-normal text-slate-300 hover:bg-slate-700/70 disabled:cursor-not-allowed disabled:text-slate-500',
            'box.picker.separator' => 'mx-1 text-slate-500',
            'label.days' => 'select-none text-center text-xs font-medium text-slate-400',
            'label.days-cell' => 'px-0.5',
            'label.month' => 'select-none text-lg font-bold text-slate-50',
            'label.year' => 'ml-1 select-none text-lg font-normal text-slate-300',
            'button.blank' => 'border border-transparent p-1 text-center text-sm',
            'button.day' => 'flex h-7 w-7 cursor-pointer items-center justify-center rounded-full text-center text-sm leading-none transition-all duration-200 ease-in-out hover:shadow-sm active:text-white disabled:cursor-not-allowed disabled:text-slate-500 focus:outline-hidden',
            'button.day-wrapper' => 'mb-2',
            'button.select' => 'text-slate-300 hover:bg-slate-700/70',
            'button.today' => 'font-bold text-rose-100 ring-1 ring-rose-200/30 bg-rose-200/8',
            'button.selected' => 'bg-rose-600 text-white hover:bg-rose-500/75',
            'button.helpers' => 'cursor-pointer select-none whitespace-nowrap rounded-md bg-slate-700/80 px-2 py-1 text-sm font-medium text-slate-100 hover:bg-slate-600/80',
            'button.navigate' => 'inline-flex cursor-pointer rounded-full p-1 text-slate-300 transition duration-100 ease-in-out hover:bg-slate-700/60 focus:outline-hidden',
            'icon.wrapper' => 'flex items-center gap-1.5',
            'icon.size' => 'h-5 w-5',
            'icon.clear' => 'hover:text-rose-300',
            'icon.navigate' => 'h-5 w-5 text-slate-300',
            'range.between' => 'bg-slate-700/60',
            'range.start' => 'rounded-l-full',
            'range.end' => 'h-7 w-7 rounded-r-full',
        ]);

        TallStackUi::customize()->form('input', 'form.date.input')->block([
            'input.wrapper' => 'flex rounded-lg ring-1 ring-slate-500/50 focus-within:ring-2 focus-within:ring-slate-400/70',
            'input.base' => 'w-full rounded-lg border-0 bg-transparent py-1.5 text-sm text-slate-100 placeholder:text-slate-400 focus:outline-hidden focus:ring-transparent sm:text-sm sm:leading-6',
            'input.slot' => 'flex select-none items-center whitespace-nowrap text-slate-300 sm:text-sm',
            'input.color.base' => 'text-slate-200 ring-slate-500/50',
            'input.color.background' => 'bg-slate-800/95',
            'input.color.disabled' => 'bg-slate-700/80 text-slate-400',
        ]);

        TallStackUi::customize()->floating('form.date.floating')->block([
            'wrapper' => 'absolute z-50 rounded-2xl border border-slate-600/40 bg-slate-900/96 shadow-[0_24px_60px_rgba(8,15,30,0.46)]',
        ]);
    }
}
