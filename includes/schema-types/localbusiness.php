<?php
return [
    //--- IDENTIDAD Y DATOS GENERALES ---
    'name' => [
        'type' => 'text',
        'label' => 'Nombre del Negocio',
        'help'  => 'Nombre oficial de la empresa, relevante para búsquedas locales y Google Maps.',
    ],
    'description' => [
        'type' => 'textarea',
        'label' => 'Descripción',
        'help'  => 'Incluye una descripción detallada con palabras clave locales, servicios, historia, etc.',
    ],
    'image' => [
        'type' => 'image',
        'label' => 'Imagen principal o logo',
        'help'  => 'Usa una imagen de alta calidad (preferiblemente cuadrada, mínimo 512x512px).',
    ],
    'url' => [
        'type' => 'url',
        'label' => 'Sitio web',
        'help'  => 'URL principal del negocio, pon siempre https://',
    ],
    'telephone' => [
        'type' => 'text',
        'label' => 'Teléfono',
        'help'  => 'Incluye prefijo internacional (ej: +34 para España).',
    ],
    'email' => [
        'type' => 'text',
        'label' => 'Email de contacto',
        'help'  => 'Correo principal para atención a clientes o consultas generales.',
    ],

    //--- DIRECCIÓN Y UBICACIÓN ---
    'address' => [
        'type' => 'group',
        'label' => 'Dirección Física',
        'fields' => [
            'streetAddress' => [
                'type' => 'text',
                'label' => 'Calle y número',
                'help'  => 'Dirección completa (calle, número, piso, etc).',
            ],
            'addressLocality' => [
                'type' => 'text',
                'label' => 'Ciudad/Población',
                'help'  => 'Ciudad donde se ubica el negocio.',
            ],
            'addressRegion' => [
                'type' => 'text',
                'label' => 'Provincia/Región',
                'help'  => 'Provincia, estado o comunidad autónoma.',
            ],
            'postalCode' => [
                'type' => 'text',
                'label' => 'Código Postal',
            ],
            'addressCountry' => [
                'type' => 'text',
                'label' => 'País',
            ],
        ]
    ],
    'geo' => [
        'type' => 'group',
        'label' => 'Coordenadas GPS',
        'fields' => [
            'latitude' => [
                'type' => 'text',
                'label' => 'Latitud',
                'help'  => 'Ejemplo: 40.416775',
            ],
            'longitude' => [
                'type' => 'text',
                'label' => 'Longitud',
                'help'  => 'Ejemplo: -3.703790',
            ],
        ]
    ],
    'hasMap' => [
        'type' => 'url',
        'label' => 'Enlace a Google Maps',
        'help'  => 'Pega el enlace directo de Google Maps a tu localización.',
    ],

    //--- HORARIOS ---
    'openingHoursSpecification' => [
        'type' => 'repeater',
        'label' => 'Horario comercial',
        'help'  => 'Especifica todas las franjas de apertura. Importante para SEO local.',
        'fields' => [
            'dayOfWeek' => [
                'type' => 'multitext',
                'label' => 'Días de la semana',
                'help'  => 'Ejemplo: Monday, Tuesday, etc. Uno por línea.',
            ],
            'opens' => [
                'type' => 'text',
                'label' => 'Hora de apertura',
                'help'  => 'Formato 24h: 09:00',
            ],
            'closes' => [
                'type' => 'text',
                'label' => 'Hora de cierre',
                'help'  => 'Formato 24h: 20:00',
            ],
        ]
    ],

    //--- REDES SOCIALES Y ENLACES EXTERNOS ---
    'sameAs' => [
        'type' => 'multitext',
        'label' => 'Redes sociales y perfiles externos',
        'help'  => 'Pon cada URL (Facebook, Instagram, TripAdvisor, Google Business, LinkedIn, etc) en una línea.',
    ],
    'googleBusiness' => [
        'type' => 'url',
        'label' => 'Perfil Google My Business',
        'help'  => 'Enlace público a tu ficha de Google Business.',
    ],

    //--- PRODUCTOS Y TIENDA ONLINE ---
    'hasOfferCatalog' => [
        'type' => 'group',
        'label' => 'Catálogo/Tienda Online externa',
        'fields' => [
            'name' => [
                'type' => 'text',
                'label' => 'Nombre de la tienda o catálogo',
            ],
            'url' => [
                'type' => 'url',
                'label' => 'URL tienda online',
            ],
            'itemListElement' => [
                'type' => 'repeater',
                'label' => 'Productos destacados',
                'help'  => 'Puedes añadir productos individuales desde una URL externa, con título, imagen y precio.',
                'fields' => [
                    'name' => [
                        'type' => 'text',
                        'label' => 'Nombre del producto',
                    ],
                    'url' => [
                        'type' => 'url',
                        'label' => 'URL del producto',
                    ],
                    'image' => [
                        'type' => 'image',
                        'label' => 'Imagen del producto',
                    ],
                    'description' => [
                        'type' => 'textarea',
                        'label' => 'Descripción',
                    ],
                    'offers' => [
                        'type' => 'group',
                        'label' => 'Oferta',
                        'fields' => [
                            'priceCurrency' => [
                                'type' => 'text',
                                'label' => 'Moneda (ej: EUR)',
                            ],
                            'price' => [
                                'type' => 'text',
                                'label' => 'Precio',
                            ],
                            'availability' => [
                                'type' => 'text',
                                'label' => 'Disponibilidad (ej: InStock, OutOfStock)',
                            ],
                        ]
                    ]
                ]
            ]
        ]
    ],

    //--- OPINIONES Y VALORACIONES ---
    'aggregateRating' => [
        'type' => 'group',
        'label' => 'Valoración global',
        'fields' => [
            'ratingValue' => [
                'type' => 'number',
                'label' => 'Valor medio (ej: 4.7)',
            ],
            'reviewCount' => [
                'type' => 'number',
                'label' => 'Cantidad de reseñas',
            ],
        ]
    ],
    'review' => [
        'type' => 'repeater',
        'label' => 'Opiniones de clientes',
        'help'  => 'Añade opiniones reales (nombre, fecha, texto, puntuación)',
        'fields' => [
            'author' => [
                'type' => 'text',
                'label' => 'Nombre del cliente',
            ],
            'datePublished' => [
                'type' => 'text',
                'label' => 'Fecha (YYYY-MM-DD)',
            ],
            'reviewBody' => [
                'type' => 'textarea',
                'label' => 'Texto de la opinión',
            ],
            'reviewRating' => [
                'type' => 'group',
                'label' => 'Puntuación',
                'fields' => [
                    'ratingValue' => [
                        'type' => 'number',
                        'label' => 'Valor (ej: 5)',
                    ]
                ]
            ]
        ]
    ],

    //--- SERVICIOS, ÁREAS, MÉTODOS DE PAGO, ETC ---
    'areaServed' => [
        'type' => 'text',
        'label' => 'Área de servicio',
        'help'  => 'Zonas, barrios, ciudades o países donde prestas servicio.',
    ],
    'department' => [
        'type' => 'repeater',
        'label' => 'Departamentos o sucursales',
        'fields' => [
            'name' => [
                'type' => 'text',
                'label' => 'Nombre del departamento/sucursal',
            ],
            'url' => [
                'type' => 'url',
                'label' => 'URL de la sucursal',
            ]
        ]
    ],
    'paymentAccepted' => [
        'type' => 'text',
        'label' => 'Métodos de pago aceptados',
        'help'  => 'Ejemplo: Efectivo, Tarjeta, Paypal, Bizum, etc.',
    ],
    'priceRange' => [
        'type' => 'text',
        'label' => 'Rango de precios',
        'help'  => 'Ejemplo: $, $$, $$$, o un rango estimado.',
    ],
    'awards' => [
        'type' => 'text',
        'label' => 'Premios o certificados',
        'help'  => 'Certificaciones, premios o reconocimientos relevantes.',
    ],

    //--- EXTRAS AVANZADOS SEO ---
    'founder' => [
        'type' => 'text',
        'label' => 'Fundador',
        'help'  => 'Nombre(s) del fundador si es relevante.',
    ],
    'foundingDate' => [
        'type' => 'text',
        'label' => 'Año de fundación',
        'help'  => 'Formato YYYY-MM-DD o solo año.',
    ],
    'isicV4' => [
        'type' => 'text',
        'label' => 'ISIC/NAICS (CNAE)',
        'help'  => 'Código sectorial según clasificación internacional.',
    ],
    'numberOfEmployees' => [
        'type' => 'number',
        'label' => 'Número de empleados',
    ],
    'legalName' => [
        'type' => 'text',
        'label' => 'Nombre legal',
    ],
    'taxID' => [
        'type' => 'text',
        'label' => 'CIF/NIF/NIT',
    ],
    'brand' => [
        'type' => 'text',
        'label' => 'Marca comercial',
    ],
    'alternateName' => [
        'type' => 'text',
        'label' => 'Otros nombres',
        'help'  => 'Alias, nombre corto, denominaciones alternativas.',
    ],
    'logo' => [
        'type' => 'image',
        'label' => 'Logo alternativo',
        'help'  => 'Puedes poner un logo alternativo si lo deseas.',
    ],
    'makesOffer' => [
        'type' => 'text',
        'label' => 'Servicios específicos',
        'help'  => 'Lista de servicios principales ofrecidos (separados por comas).',
    ],
    'parentOrganization' => [
        'type' => 'text',
        'label' => 'Empresa matriz',
        'help'  => 'Nombre de la organización superior si existe.',
    ],
    'slogan' => [
        'type' => 'text',
        'label' => 'Slogan',
        'help'  => 'Eslogan comercial del negocio.',
    ],
    'vatID' => [
        'type' => 'text',
        'label' => 'VAT ID',
        'help'  => 'Número de IVA intracomunitario si aplica.',
    ],
    'faxNumber' => [
        'type' => 'text',
        'label' => 'Fax',
    ],
    'contactPoint' => [
        'type' => 'group',
        'label' => 'Contacto adicional',
        'fields' => [
            'contactType' => [
                'type' => 'text',
                'label' => 'Tipo (ej: soporte, ventas, etc.)',
            ],
            'telephone' => [
                'type' => 'text',
                'label' => 'Teléfono',
            ],
            'email' => [
                'type' => 'text',
                'label' => 'Email',
            ],
        ]
    ],
    'hasCredential' => [
        'type' => 'text',
        'label' => 'Credenciales profesionales',
        'help'  => 'Certificados, licencias, etc.',
    ],
    'memberOf' => [
        'type' => 'text',
        'label' => 'Miembro de',
        'help'  => 'Asociaciones, cámaras, etc.',
    ],
    'currenciesAccepted' => [
        'type' => 'text',
        'label' => 'Monedas aceptadas',
        'help'  => 'Ejemplo: EUR, USD, etc.',
    ],
    'hasDriveThrough' => [
        'type' => 'select',
        'label' => 'Tiene drive-through',
        'options' => ['' => 'No especificar', 'true' => 'Sí', 'false' => 'No'],
    ],
    'smokingAllowed' => [
        'type' => 'select',
        'label' => 'Se permite fumar',
        'options' => ['' => 'No especificar', 'true' => 'Sí', 'false' => 'No'],
    ],
    'specialOpeningHoursSpecification' => [
        'type' => 'repeater',
        'label' => 'Horarios especiales',
        'help'  => 'Ejemplo: festivos, verano, navidad...',
        'fields' => [
            'opens' => [
                'type' => 'text',
                'label' => 'Apertura especial',
            ],
            'closes' => [
                'type' => 'text',
                'label' => 'Cierre especial',
            ],
            'validFrom' => [
                'type' => 'text',
                'label' => 'Desde (YYYY-MM-DD)',
            ],
            'validThrough' => [
                'type' => 'text',
                'label' => 'Hasta (YYYY-MM-DD)',
            ],
        ]
    ],
];