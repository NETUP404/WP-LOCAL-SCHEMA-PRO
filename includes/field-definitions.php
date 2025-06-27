<?php
if (!defined('ABSPATH')) exit;

function wp_lsp_get_schema_fields() {
    return [
        'LocalBusiness' => [
            'name' => [
                'label' => 'Nombre del negocio',
                'type' => 'text',
                'help' => 'Nombre oficial del negocio/local.',
            ],
            'description' => [
                'label' => 'Descripción',
                'type' => 'textarea',
                'help' => 'Breve descripción.',
            ],
            'image' => [
                'label' => 'Imagen principal',
                'type' => 'image',
                'help' => 'Logo o foto representativa.',
            ],
            'telephone' => [
                'label' => 'Teléfono',
                'type' => 'text',
                'help' => 'Teléfono de contacto principal.',
            ],
            'email' => [
                'label' => 'Email',
                'type' => 'text',
                'help' => 'Correo de contacto.',
            ],
            'url' => [
                'label' => 'URL principal',
                'type' => 'url',
                'help' => 'Web principal del negocio.',
            ],
            'address' => [
                'label' => 'Dirección',
                'type' => 'group',
                'fields' => [
                    'streetAddress' => ['label'=>'Calle y número','type'=>'text'],
                    'postalCode' => ['label'=>'Código postal','type'=>'text'],
                    'addressLocality' => ['label'=>'Ciudad','type'=>'text'],
                    'addressRegion' => ['label'=>'Provincia/Región','type'=>'text'],
                    'addressCountry' => ['label'=>'País','type'=>'text'],
                ],
                'help' => 'Dirección postal completa.',
            ],
            'geo' => [
                'label' => 'Coordenadas (lat/lon)',
                'type' => 'group',
                'fields' => [
                    'latitude' => ['label'=>'Latitud','type'=>'text'],
                    'longitude' => ['label'=>'Longitud','type'=>'text'],
                ],
                'help' => 'Opcional, mejora la precisión.',
            ],
            'hasMap' => [
                'label' => 'Enlace a mapa',
                'type' => 'url',
                'help' => 'URL de Google Maps o similar.',
            ],
            'openingHoursSpecification' => [
                'label' => 'Horario comercial',
                'type' => 'repeater',
                'fields' => [
                    'dayOfWeek' => [
                        'label' => 'Día de la semana',
                        'type' => 'select',
                        'options' => [
                            'Monday' => 'Lunes',
                            'Tuesday' => 'Martes',
                            'Wednesday' => 'Miércoles',
                            'Thursday' => 'Jueves',
                            'Friday' => 'Viernes',
                            'Saturday' => 'Sábado',
                            'Sunday' => 'Domingo',
                        ],
                    ],
                    'opens' => ['label'=>'Abre a las','type'=>'time'],
                    'closes' => ['label'=>'Cierra a las','type'=>'time'],
                ],
            ],
            'specialOpeningHoursSpecification' => [
                'label' => 'Horarios especiales',
                'type' => 'repeater',
                'fields' => [
                    'date' => ['label'=>'Fecha especial','type'=>'date'],
                    'opens' => ['label'=>'Abre a las','type'=>'time'],
                    'closes' => ['label'=>'Cierra a las','type'=>'time'],
                ],
                'help' => 'Festivos, vacaciones, etc.',
            ],
            'externalUrls' => [
                'label' => 'URLs externas (web, tienda, otras webs)',
                'type' => 'repeater',
                'fields' => [
                    'tipo' => [
                        'label' => 'Tipo de URL',
                        'type' => 'select',
                        'options' => [
                            'main' => 'Web Principal',
                            'shop' => 'Tienda Online',
                            'other' => 'Otra Web'
                        ],
                    ],
                    'url' => [
                        'label' => 'URL',
                        'type' => 'url',
                    ]
                ],
                'help' => 'Incluye aquí la web principal, tienda online y otras webs externas.',
            ],
            'sameAs' => [
                'label' => 'Perfiles sociales (uno por línea)',
                'type' => 'multitext',
                'help' => 'URLs de redes sociales.',
            ],
            'googleBusiness' => [
                'label' => 'Perfil de Google Business',
                'type' => 'url',
                'help' => 'Enlace a la ficha de Google.',
            ],
            'hasOfferCatalog' => [
                'label' => 'Catálogo de productos/servicios',
                'type' => 'textarea',
                'help' => 'Breve listado de productos/servicios.',
            ],
            'aggregateRating' => [
                'label' => 'Valoración media',
                'type' => 'group',
                'fields' => [
                    'ratingValue' => [
                        'label' => 'Nota media (0-5)',
                        'type' => 'number',
                        'attributes' => 'min="0" max="5" step="0.1"',
                    ],
                    'reviewCount' => [
                        'label' => 'Nº valoraciones',
                        'type' => 'number',
                        'attributes' => 'min="0" step="1"',
                    ]
                ],
                'help' => 'Valoraciones globales del negocio.',
            ],
            'review' => [
                'label' => 'Opiniones / Reseñas',
                'type' => 'repeater',
                'fields' => [
                    'author' => ['label'=>'Autor','type'=>'text'],
                    'datePublished' => ['label'=>'Fecha','type'=>'date'],
                    'reviewBody' => ['label'=>'Comentario','type'=>'textarea'],
                    'ratingValue' => ['label'=>'Nota (0-5)','type'=>'number','attributes'=>'min="0" max="5" step="0.1"'],
                ],
                'help' => 'Añade las reseñas más representativas.',
            ],
        ],
        'Store' => [
            'enableStoreSchema' => [
                'label' => 'Activar schema de tienda',
                'type' => 'select',
                'options' => ['no'=>'No','internal'=>'Tienda en este dominio','external'=>'Tienda en otro dominio'],
            ],
            'storeName' => [
                'label' => 'Nombre de la tienda',
                'type' => 'text',
            ],
            'storeType' => [
                'label' => 'Tipo de tienda',
                'type' => 'select',
                'options' => [
                    'Store' => 'Store (genérico)',
                    'AutomotiveStore'=>'Automotive',
                    'ClothingStore'=>'Clothing',
                    'ElectronicsStore'=>'Electronics',
                    'GroceryStore'=>'Grocery',
                    'HardwareStore'=>'Hardware',
                    'ShoeStore'=>'Shoe',
                    'ToyStore'=>'Toy',
                ],
            ],
            'storeUrl' => [
                'label' => 'URL de la tienda',
                'type' => 'url',
            ],
            'storeDescription' => [
                'label' => 'Descripción de la tienda',
                'type' => 'textarea',
            ],
            'storeImage' => [
                'label' => 'Imagen principal de la tienda',
                'type' => 'image',
            ],
            'storeTelephone' => [
                'label' => 'Teléfono de la tienda',
                'type' => 'text',
            ],
            'storeAddress' => [
                'label' => 'Dirección de la tienda',
                'type' => 'group',
                'fields' => [
                    'streetAddress' => ['label'=>'Calle y número','type'=>'text'],
                    'postalCode' => ['label'=>'Código postal','type'=>'text'],
                    'addressLocality' => ['label'=>'Ciudad','type'=>'text'],
                    'addressRegion' => ['label'=>'Provincia/Región','type'=>'text'],
                    'addressCountry' => ['label'=>'País','type'=>'text'],
                ],
            ],
            'storeOpeningHours' => [
                'label' => 'Horario comercial',
                'type' => 'repeater',
                'fields' => [
                    'dayOfWeek' => [
                        'label' => 'Día de la semana',
                        'type' => 'select',
                        'options' => [
                            'Monday' => 'Lunes',
                            'Tuesday' => 'Martes',
                            'Wednesday' => 'Miércoles',
                            'Thursday' => 'Jueves',
                            'Friday' => 'Viernes',
                            'Saturday' => 'Sábado',
                            'Sunday' => 'Domingo',
                        ],
                    ],
                    'opens' => ['label'=>'Abre a las','type'=>'time'],
                    'closes' => ['label'=>'Cierra a las','type'=>'time'],
                ],
            ],
            'storeProducts' => [
                'label' => 'Productos principales',
                'type' => 'repeater',
                'fields' => [
                    'productName' => ['label'=>'Nombre del producto','type'=>'text'],
                    'productPrice' => ['label'=>'Precio','type'=>'text'],
                    'productUrl' => ['label'=>'URL','type'=>'url'],
                    'productImage' => ['label'=>'Imagen','type'=>'image'],
                ],
            ],
            'storePayment' => [
                'label' => 'Métodos de pago aceptados',
                'type' => 'multitext',
            ],
            'storeShipping' => [
                'label' => 'Métodos de envío',
                'type' => 'multitext',
            ],
            'storeAggregateRating' => [
                'label' => 'Valoración media de la tienda',
                'type' => 'group',
                'fields' => [
                    'ratingValue' => [
                        'label' => 'Nota media (0-5)',
                        'type' => 'number',
                        'attributes' => 'min="0" max="5" step="0.1"',
                    ],
                    'reviewCount' => [
                        'label' => 'Nº valoraciones',
                        'type' => 'number',
                        'attributes' => 'min="0" step="1"',
                    ]
                ],
            ],
            'storeReviews' => [
                'label' => 'Opiniones sobre la tienda',
                'type' => 'repeater',
                'fields' => [
                    'author' => ['label'=>'Autor','type'=>'text'],
                    'datePublished' => ['label'=>'Fecha','type'=>'date'],
                    'reviewBody' => ['label'=>'Comentario','type'=>'textarea'],
                    'ratingValue' => ['label'=>'Nota (0-5)','type'=>'number','attributes'=>'min="0" max="5" step="0.1"'],
                ],
            ],
        ],
        'Organization' => [
            'orgName' => ['label'=>'Nombre organización','type'=>'text'],
            'orgLegalName' => ['label'=>'Nombre legal','type'=>'text'],
            'orgLogo' => ['label'=>'Logo','type'=>'image'],
            'orgUrl' => ['label'=>'URL','type'=>'url'],
            'orgContact' => ['label'=>'Email o Tel.','type'=>'text'],
            'orgAddress' => [
                'label' => 'Dirección',
                'type' => 'group',
                'fields' => [
                    'streetAddress' => ['label'=>'Calle y número','type'=>'text'],
                    'postalCode' => ['label'=>'Código postal','type'=>'text'],
                    'addressLocality' => ['label'=>'Ciudad','type'=>'text'],
                    'addressRegion' => ['label'=>'Provincia/Región','type'=>'text'],
                    'addressCountry' => ['label'=>'País','type'=>'text'],
                ]
            ]
        ],
        'Person' => [
            'personName' => ['label'=>'Nombre completo','type'=>'text'],
            'personJobTitle' => ['label'=>'Cargo','type'=>'text'],
            'personImage' => ['label'=>'Foto','type'=>'image'],
            'personUrl' => ['label'=>'URL','type'=>'url'],
            'personSameAs' => ['label'=>'Perfiles sociales (uno por línea)','type'=>'multitext'],
        ]
    ];
}