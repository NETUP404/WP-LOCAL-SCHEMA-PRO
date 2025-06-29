# Nueva Funcionalidad: Asociación de Categorías Secundarias con Páginas

## 📋 Resumen de Cambios

Se ha implementado la funcionalidad solicitada para asociar categorías secundarias con páginas específicas del sitio web.

## 🆕 Características Nuevas

### 1. Campo "Página asociada" para categorías secundarias
- Cada categoría secundaria ahora tiene un desplegable para seleccionar una página específica
- Se muestran todas las páginas publicadas del sitio con título y URL
- Opción "Ninguna" disponible para no asociar a ninguna página

### 2. Lógica inteligente de visualización
- **Si hay coincidencia**: Solo se muestran los schemas de las categorías secundarias asociadas a la página actual
- **Si NO hay coincidencia**: Solo se muestra el schema principal/global
- **Tipos sin página asociada**: No se muestran en ninguna parte del sitio

### 3. Interfaz administrativa mejorada
- Reemplazo del multiselect por interface individual para cada tipo secundario
- Botones dinámicos para añadir/eliminar tipos secundarios
- Validaciones en tiempo real

## 🔄 Migración Automática

Los datos existentes se migran automáticamente:
- Tipos secundarios existentes se mantienen pero sin página asociada inicialmente
- El administrador debe configurar las páginas asociadas manualmente después de la actualización

## 💻 Cómo Usar

### Configuración en Admin
1. Ir a "WP Local Schema PRO" en el panel de administración
2. En la sección "Tipos/categorías secundarias":
   - Seleccionar un tipo de schema del desplegable
   - Elegir una página asociada del segundo desplegable
   - Usar "Añadir tipo secundario" para más tipos
   - Usar "Eliminar" para quitar tipos no deseados

### Comportamiento en Frontend
- Al visitar `/restaurante/` → Solo muestra schema "Restaurant" (si está asociado)
- Al visitar `/tienda/` → Solo muestra schema "Store" (si está asociado)  
- Al visitar `/sobre-nosotros/` → Solo muestra schema "LocalBusiness" (principal)

## 🛡️ Compatibilidad

- ✅ **Backward Compatible**: Instalaciones existentes funcionan sin problemas
- ✅ **Data Migration**: Datos antiguos se convierten automáticamente
- ✅ **Legacy Support**: El campo antiguo `schema_types_secundarios` se mantiene para compatibilidad

## 🧪 Casos de Uso Típicos

```
Configuración ejemplo:
- Principal: LocalBusiness
- Secundario 1: Restaurant → /restaurante/
- Secundario 2: Store → /tienda/
- Secundario 3: Hotel → (ninguna página)

Resultados:
- /restaurante/ → Muestra solo Restaurant schema
- /tienda/ → Muestra solo Store schema  
- /sobre-nosotros/ → Muestra solo LocalBusiness schema
- / → Muestra solo LocalBusiness schema
- Hotel nunca se muestra (sin página asociada)
```

## 🔧 Cambios Técnicos

### Archivos Modificados
- `includes/admin-panel.php` → Nueva interfaz administrativa
- `includes/output-schema.php` → Lógica de output basada en URLs
- `includes/save-options.php` → Sanitización del nuevo formato de datos
- `includes/helpers.php` → Función para obtener páginas publicadas

### Nueva Estructura de Datos
```php
'schema_types_secundarios_v2' => [
    [
        'type' => 'Restaurant',
        'page_url' => 'https://miweb.com/restaurante/'
    ],
    [
        'type' => 'Store', 
        'page_url' => ''  // Sin página asociada
    ]
]
```

## ⚠️ Consideraciones

1. **Tipos sin página**: No se mostrarán en ninguna parte del sitio
2. **URLs exactas**: La coincidencia debe ser exacta con la URL de la página
3. **Páginas publicadas**: Solo se muestran páginas con estado "publicado"
4. **Validación**: Tipos vacíos se filtran automáticamente al guardar

## 🎯 Objetivo Cumplido

✅ Campo "Página asociada" implementado  
✅ Desplegable con todas las páginas publicadas  
✅ Opción "Ninguna" disponible  
✅ Lógica de coincidencia de URLs implementada  
✅ Solo schemas relevantes se muestran por página  
✅ Schema principal se muestra cuando no hay coincidencias  
✅ Resto de funcionalidad del plugin se mantiene intacta  
✅ Implementación robusta y compatible