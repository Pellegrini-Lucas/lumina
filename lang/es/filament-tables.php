<?php

return [

    'column_toggle' => [

        'heading' => 'Columnas',

    ],

    'columns' => [

        'text' => [
            'more_list_items' => 'y :count más',
        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Seleccionar/deseleccionar todos los elementos para acciones masivas.',
        ],

        'bulk_select_record' => [
            'label' => 'Seleccionar elemento :key para acciones masivas.',
        ],

        'bulk_select_group' => [
            'label' => 'Seleccionar grupo :title para acciones masivas.',
        ],

        'search' => [
            'label' => 'Buscar',
            'placeholder' => 'Buscar',
            'indicator' => 'Buscar',
        ],

    ],

    'summary' => [

        'heading' => 'Resumen',

        'subheadings' => [
            'all' => 'Todos :label',
            'group' => 'Resumen de :group',
            'page' => 'Esta página',
        ],

        'summarizers' => [

            'average' => [
                'label' => 'Promedio',
            ],

            'count' => [
                'label' => 'Conteo',
            ],

            'sum' => [
                'label' => 'Suma',
            ],

        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'Terminar de reordenar registros',
        ],

        'enable_reordering' => [
            'label' => 'Reordenar registros',
        ],

        'filter' => [
            'label' => 'Filtrar',
        ],

        'group' => [
            'label' => 'Agrupar',
        ],

        'open_bulk_actions' => [
            'label' => 'Acciones masivas',
        ],

        'toggle_columns' => [
            'label' => 'Alternar columnas',
        ],

    ],

    'empty' => [

        'heading' => 'Sin registros',

        'description' => 'Crea un :model para comenzar.',

    ],

    'filters' => [

        'actions' => [

            'remove' => [
                'label' => 'Quitar filtro',
            ],

            'remove_all' => [
                'label' => 'Quitar todos los filtros',
                'tooltip' => 'Quitar todos los filtros',
            ],

            'reset' => [
                'label' => 'Restablecer',
            ],

        ],

        'heading' => 'Filtros',

        'indicator' => 'Filtros activos',

        'multi_select' => [
            'placeholder' => 'Todos',
        ],

        'select' => [
            'placeholder' => 'Todos',
        ],

        'trashed' => [

            'label' => 'Registros eliminados',

            'only_trashed' => 'Solo registros eliminados',

            'with_trashed' => 'Con registros eliminados',

            'without_trashed' => 'Sin registros eliminados',

        ],

    ],

    'grouping' => [

        'fields' => [

            'aggregate' => [
                'label' => 'Agregado',
            ],

            'field' => [
                'label' => 'Campo',
            ],

            'direction' => [

                'label' => 'Dirección',

                'options' => [
                    'asc' => 'Ascendente',
                    'desc' => 'Descendente',
                ],

            ],

        ],

    ],

    'reorder_indicator' => 'Arrastra y suelta los registros en orden.',

    'selection_indicator' => [

        'selected_count' => '1 registro seleccionado|:count registros seleccionados',

        'actions' => [

            'select_all' => [
                'label' => 'Seleccionar todos :count',
            ],

            'deselect_all' => [
                'label' => 'Deseleccionar todos',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'Ordenar por',
            ],

            'direction' => [

                'label' => 'Dirección de ordenamiento',

                'options' => [
                    'asc' => 'Ascendente',
                    'desc' => 'Descendente',
                ],

            ],

        ],

    ],

    'pagination' => [

        'label' => 'Navegación de paginación',

        'overview' => 'Mostrando :first a :last de :total resultados',

        'fields' => [

            'records_per_page' => [

                'label' => 'Por página',

                'options' => [
                    'all' => 'Todos',
                ],

            ],

        ],

        'actions' => [

            'go_to_page' => [
                'label' => 'Ir a página :page',
            ],

            'next' => [
                'label' => 'Siguiente',
            ],

            'previous' => [
                'label' => 'Anterior',
            ],

        ],

    ],

];
