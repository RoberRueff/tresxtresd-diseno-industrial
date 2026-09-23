# Arquitectura

- **Sitio viejo en producción:** WordPress en tresxtresd.com.ar (sin acceso ni código fuente acá) — se va a reemplazar, no a modificar.
- **Sitio nuevo (este repo):** HTML/CSS/JS estático, sin build ni framework — `index.html`, `diseno-industrial.html`, `impresion-3d.html`, `img/`. Confirmado por el usuario (2026-09-23) que el nuevo sitio NO es WordPress.
- **Backend:** `enviar.php` — único endpoint del repo, compartido por los 3 formularios (mismos `name` de campo en los 3 HTML: nombre, empresa, email, whatsapp, tipo, cantidad, mensaje, archivo). Recibe el POST y lo manda por `mail()` de PHP a info@tresxtresd.com.ar. Requiere hosting con PHP (DonWeb) — sin `enviar.php` subido junto a los HTML, ningún formulario funciona.
- **Config por página:** cada HTML tiene su propio bloque `TXT_CONFIG` al final (whatsapp, formEndpoint, adsLead, adsWhatsapp) — no hay un config compartido entre los 3 archivos, cambios ahí van de a uno.
- **Mockups previos en Artifact (claude.ai):** exploración visual anterior a que existiera este build local — ya no es la referencia activa, el trabajo real sigue en los HTML locales.

## Páginas del sitio nuevo

Cada grupo de anuncios de Google Ads apunta a la más relevante:

1. `index.html` — Home, con CTA claro y WhatsApp.
2. `diseno-industrial.html` — "Diseño industrial a medida".
3. `impresion-3d.html` — "Impresión 3D".

## Testing local

No hay PHP instalado en esta máquina de desarrollo — `enviar.php` se probó con `docker run php:8.2-cli` (ver historial de sesión). Antes de dar por probado un cambio en `enviar.php`, repetir ese test o uno equivalente; no asumir que "sin errores de sintaxis" alcanza.
