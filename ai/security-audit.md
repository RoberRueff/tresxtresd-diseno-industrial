# Security Audit

Hallazgos relevados hasta ahora (vía el Plan de Google Ads y las capturas del sitio). No es un audit de penetración — es lo que ya se detectó a simple vista.

## Hallazgos

- **SSL vencido en `3x3d.com.ar`** — dominio secundario asociado a la marca en buscadores. Riesgo: warning de seguridad en el navegador para quien lo encuentre buscando la marca. Acción sugerida: dar de baja o redirigir (punto 9 del plan).
- **Widget de Instagram con token vencido** — expone un mensaje de error de autorización (`wp_die`) públicamente en la home. No es una vulnerabilidad explotable, pero es una superficie de error visible que un atacante o competidor podría usar como señal de sitio desatendido.
- **Sin Google Tag / GA4** — no es un riesgo de seguridad, pero implica cero visibilidad sobre tráfico anómalo o abuso del formulario una vez activa la campaña paga.

## Resuelto (sitio nuevo, 2026-09-25)

- Antispam completo en `enviar.php` + los 3 formularios (commit `3ef0df0`): campo trampa oculto (`sitio_alt`), tiempo mínimo de 3s en página, límite de 5 envíos/hora por IP, filtro de contenido (links, palabras de spam, alfabetos no latinos) y verificación de que el dominio del email exista (MX/A).
- `.htaccess` agregado en la raíz: fuerza HTTPS, headers HSTS/X-Content-Type-Options/X-Frame-Options/Referrer-Policy, gzip y cache de 1 mes para imágenes. Sin probar en DonWeb real — cada bloque usa `<IfModule>` por si falta algún módulo Apache.
- `img/logos/Cafe-Tortoni.png` (90 KB) reemplazado por `Cafe-Tortoni.jpg` (30 KB), mismo contenido visual.
- `gtag.js` suelto sacado de las 3 páginas (commits `3ef0df0`/`8eafff0`) — queda solo GTM, sin doble conteo de eventos.

## Pendiente de relevar (sin acceso al sitio real)

- Versión de WordPress y plugins instalados (el spinner roto sugiere al menos un plugin desactualizado o mal configurado).
- Si el dominio principal tiene SSL vigente — condición para que el redirect y el HSTS del `.htaccess` no dejen el sitio inaccesible.
