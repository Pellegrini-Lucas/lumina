<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Mis Hábitos
        </x-slot>

        @php
            $habits = $this->getHabits();
        @endphp
        
        @if($habits->isEmpty())
            <div class="text-center py-6">
                <p class="text-sm text-gray-500 dark:text-gray-400">No tienes hábitos creados.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($habits as $habit)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                {{-- Columna 1: Círculo violeta con racha --}}
                                <td class="py-3 px-4 w-16">
                                    <div 
                                        class="relative inline-block"
                                        x-data="{ showTooltip: false }"
                                        @mouseenter="showTooltip = true"
                                        @mouseleave="showTooltip = false"
                                    >
                                        <div 
                                            class="w-10 h-10 rounded-full cursor-help transition-all duration-300"
                                            style="background-color: rgba(139, 92, 246, {{ $habit->getStreakOpacity() }})"
                                        ></div>
                                        
                                        {{-- Tooltip con la racha --}}
                                        <div 
                                            x-show="showTooltip"
                                            x-cloak
                                            x-transition
                                            class="absolute z-50 bottom-full left-1/2 -translate-x-1/2 mb-2 px-3 py-1.5 text-xs font-medium text-white bg-gray-900 dark:bg-gray-700 rounded-md shadow-lg whitespace-nowrap pointer-events-none"
                                        >
                                            Racha: {{ $habit->getCurrentStreak() }} día{{ $habit->getCurrentStreak() !== 1 ? 's' : '' }}
                                        </div>
                                    </div>
                                </td>

                                {{-- Columna 2: Nombre del hábito --}}
                                <td class="py-3 px-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $habit->name }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                        {{ $habit->frequency->getLabel() }}
                                    </div>
                                </td>

                                {{-- Columna 3: "Por completar" (solo si es debido hoy) --}}
                                <td class="py-3 px-4 text-sm">
                                    @if($habit->isDueToday() && !$habit->isCompletedToday())
                                        <span class="text-amber-600 dark:text-amber-400 font-medium">
                                            Por completar
                                        </span>
                                    @endif
                                </td>

                                {{-- Columna 4: Checkbox/tick para completar --}}
                                <td class="py-3 px-4 w-20 text-center">
                                    @if($habit->isDueToday() && !$habit->isCompletedToday())
                                        <button
                                            wire:click="completeHabit({{ $habit->id }})"
                                            wire:loading.attr="disabled"
                                            wire:target="completeHabit({{ $habit->id }})"
                                            type="button"
                                            class="inline-flex items-center justify-center w-5 h-5 rounded border-2 border-gray-300 dark:border-gray-600 hover:border-green-500 hover:bg-green-50 dark:hover:bg-green-900/20 transition-colors cursor-pointer disabled:opacity-50"
                                            title="Marcar como completado"
                                        >
                                        </button>
                                    @elseif($habit->isCompletedToday())
                                        <div class="inline-flex items-center justify-center w-5 h-5 rounded bg-green-500">
                                            <svg class="w-3.5 h-3.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
