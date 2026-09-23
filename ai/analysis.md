# Análisis — Sitio actual (tresxtresd.com.ar)

Extraído del diagnóstico en el Plan de Google Ads. Referencia rápida, el detalle completo está en `sin-publicar/Plan de Google Ads – Tres x Tres D.md`.

## Estructura (corregido tras scrapear el sitio real, 2026-09-22)

El plan original decía "one-page, todo son anclas" — es inexacto. "Diseños" (`/disenios_profesionales/`) e "Impresiones 3d" (`/impresiones_3d/`) YA son páginas propias con URL. Solo "Porfolio" y "Contacto" son anclas (`#porfolio`, `#contacto`) dentro de cada página. El punto 4 del plan ("crear 2 páginas propias") está parcialmente resuelto — falta contenido (casos de uso, precio, WhatsApp), no la estructura.

Stack real: WordPress + Astra theme + Elementor. Fuentes reales: **Fjalla One** (títulos) + **Raleway** (cuerpo). Paleta real: fondo `#091118`/`#061114`, panel `#1C2125`, acento `#3AA6B9` (hover `#2997AA`), tint claro `#E9F8F9`.

## Problemas encontrados

- Widget de Instagram roto: dos errores `wp_die: No tienes autorización...` visibles apenas se entra a la home.
- Spinner de carga que no resuelve (otro plugin roto, sin identificar cuál).
- Typo "Porfolio" en el menú.
- Sin Google Tag / GA4 instalado — no hay forma de medir conversiones hoy.
- Sin WhatsApp visible — único canal de contacto es formulario por email.
- Formulario de contacto mínimo: nombre, email, mensaje. Sin teléfono ni detalle de proyecto (material, cantidad, archivo).
- Sin precios ni cotizador, a diferencia de competidores (Printonic, Portal Digital 3D) que sí lo ofrecen.
- Dominio secundario `3x3d.com.ar` con SSL vencido, asociado a la marca en buscadores.
- Inconsistencia visual entre secciones del sitio (ver capturas: home vs. página de Impresiones 3D usan estilos distintos).

## Prueba social existente

8 clientes reales confirmados por scraping (no 3 como decía el plan original): Dragón Sushi, Cúrcuma Sushi, Café Tortoni, Cremolatti, **Netflix**, Teruya Sushi, Madero Tango, Fabric Sushi. Netflix es el dato más fuerte y no estaba en el plan — vale la pena destacarlo en los anuncios. Sin caso de uso detallado por cliente en el sitio real (confirma el gap del punto 5 del plan), salvo dos piezas con foto y etiqueta en la página de Diseños: "Arquitectura 2023" e "Iluminación 2022–2025".

Instagram real confirmado: @tresxtresd. Facebook: facebook.com/p/tresxtresd-100063605107840. No hay `tel:` ni WhatsApp en el sitio (confirma el gap del punto 3 del plan).
