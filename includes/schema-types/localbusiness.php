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
        'help'  => 'Número de teléfono de contacto con prefijo internacional.',
    ],
    //--- CAMPOS FISCALES ---
    'taxID' => [
        'type' => 'text',
        'label' => 'CIF/NIF/NIT',
        'help'  => 'Introduce el identificador fiscal único del negocio. Ejemplo: B12345678',
    ],
    'vatID' => [
        'type' => 'text',
        'label' => 'VAT ID',
        'help'  => 'Número de IVA intracomunitario si aplica. Ejemplo: ESB12345678',
    ],
    //--- POLÍTICAS LEGALES ---
    'privacyPolicy' => [
        'type' => 'url',
        'label' => 'Política de Privacidad',
        'help'  => 'Enlace a la política de privacidad de tu web.',
    ],
    'termsOfService' => [
        'type' => 'url',
        'label' => 'Términos y Condiciones',
        'help'  => 'Enlace a los términos y condiciones de tu web.',
    ],
    'cookiesPolicy' => [
        'type' => 'url',
        'label' => 'Política de Cookies',
        'help'  => 'Enlace a la política de cookies de tu web.',
    ],
    //--- DIRECCIÓN ---
    'address' => [
        'type' => 'group',
        'label' => 'Dirección física',
        'fields' => [
            'streetAddress' => [
                'type' => 'text',
                'label' => 'Calle y número',
            ],
            'addressLocality' => [
                'type' => 'text',
                'label' => 'Ciudad o localidad',
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
                'label' => 'Productos o servicios',
                'fields' => [
                    'name' => [
                        'type' => 'text',
                        'label' => 'Nombre',
                    ],
                    'url' => [
                        'type' => 'url',
                        'label' => 'Enlace',
                    ],
                    'availability' => [
                        'type' => 'text',
                        'label' => 'Disponibilidad (ej: InStock, OutOfStock)',
                    ],
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
        'type' => 'select',
        'label' => 'Métodos de pago aceptados',
        'help'  => 'Selecciona los métodos de pago aceptados.',
        'options' => [
            'Efectivo' => 'Efectivo',
            'Tarjeta' => 'Tarjeta',
            'Paypal' => 'Paypal',
            'Bizum' => 'Bizum',
            'Transferencia' => 'Transferencia',
            'Otro' => 'Otro',
        ],
        'multiple' => true,
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
        'type' => 'select',
        'label' => 'ISIC/NAICS (CNAE)',
        'help'  => 'Selecciona el código sectorial según clasificación internacional.',
        'options' => [
            '' => 'Seleccionar',
            'A' => 'Agricultura, ganadería y pesca',
            'C' => 'Industria manufacturera',
            'G' => 'Comercio al por mayor y al por menor',
            'I' => 'Hostelería',
            'J' => 'Información y comunicaciones',
            'K' => 'Actividades financieras y de seguros',
            'M' => 'Actividades profesionales, científicas y técnicas',
            'Q' => 'Sanidad y servicios sociales',
            'R' => 'Actividades artísticas, recreativas y de entretenimiento',
            'S' => 'Otros servicios',
            'Z' => 'Otro',
        ],
    ],
    'numberOfEmployees' => [
        'type' => 'select',
        'label' => 'Número de empleados',
        'help'  => 'Selecciona el rango de empleados.',
        'options' => [
            '' => 'Seleccionar',
            '0-10' => '0-10',
            '11-50' => '11-50',
            '51-200' => '51-200',
            '201-1000' => '201-1000',
            '1001+' => 'Más de 1000',
        ],
    ],
    'legalName' => [
        'type' => 'text',
        'label' => 'Nombre legal',
        'help'  => 'Denominación legal completa de la empresa.',
    ],
    'brand' => [
        'type' => 'text',
        'label' => 'Marca comercial',
        'help'  => 'Marca o nombre comercial habitual.',
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
    'faxNumber' => [
        'type' => 'text',
        'label' => 'Fax',
        'help'  => 'Número de fax de contacto (opcional).',
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
        'type' => 'select',
        'label' => 'Monedas aceptadas',
        'help'  => 'Selecciona las monedas aceptadas.',
        'options' => [
            'EUR' => 'Euro (EUR)',
            'USD' => 'Dólar (USD)',
            'GBP' => 'Libra (GBP)',
            'MXN' => 'Peso (MXN)',
            'COP' => 'Peso colombiano (COP)',
            'BRL' => 'Real brasileño (BRL)',
            'Otro' => 'Otro',
        ],
        'multiple' => true,
    ],
    'hasDriveThrough' => [
        'type' => 'select',
        'label' => 'Tiene drive-through',
        'help'  => '¿Ofrece servicio drive-through?',
        'options' => ['' => 'No especificar', 'true' => 'Sí', 'false' => 'No'],
    ],
    'smokingAllowed' => [
        'type' => 'select',
        'label' => 'Se permite fumar',
        'help'  => '¿Está permitido fumar en el local?',
        'options' => ['' => 'No especificar', 'true' => 'Sí', 'false' => 'No'],
    ],
    'specialOpeningHoursSpecification' => [
        'type' => 'repeater',
        'label' => 'Horarios especiales',
        'help'  => 'Ejemplo: festivos, verano, navidad...',
        'fields' => [
            'dayOfWeek' => [
                'type' => 'multitext',
                'label' => 'Días especiales',
            ],
            'opens' => [
                'type' => 'text',
                'label' => 'Hora de apertura',
            ],
            'closes' => [
                'type' => 'text',
                'label' => 'Hora de cierre',
            ],
        ]
    ],
];