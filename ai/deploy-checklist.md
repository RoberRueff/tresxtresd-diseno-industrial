# Deploy Checklist — Lanzamiento de la campaña

Tomado 1:1 de la sección "Cambios necesarios en el sitio antes de lanzar" del Plan de Google Ads. Esto es sobre el sitio en WordPress real, no sobre los mockups.

## Crítico (bloquea el lanzamiento)

- [ ] Instalar Google Tag / Google Ads y configurar conversiones (formulario, clic a WhatsApp, clic a teléfono).
- [ ] Arreglar o sacar el widget de Instagram roto (errores `wp_die`).
- [ ] Agregar WhatsApp con click-to-chat visible arriba de todo.

## Importante (para que la campaña rinda)

- [ ] Crear 2 páginas propias con URL: "Diseño industrial a medida" e "Impresión 3D".
- [ ] Sumar 1 caso de uso con foto por cliente mostrado (Dragón Sushi, Cúrcuma, Café Tortoni).
- [ ] Ampliar el formulario (teléfono/WhatsApp + detalle de pieza).
- [x] Los 3 formularios (`index.html`, `diseno-industrial.html`, `impresion-3d.html`) conectados a `enviar.php` (PHP `mail()`, envía a info@tresxtresd.com.ar). Probado localmente con Docker (`php:8.2-cli`) en las 3 páginas: valida nombre/email, rechaza inyección de headers, responde bien a GET/POST/datos inválidos, sin errores de JS. **No probado en hosting real** — falta confirmar que DonWeb tenga `mail()` habilitado, subir `enviar.php` junto con los 3 HTML, y que el mail no caiga en spam.
- [x] Precio: decidido que no se publica tarifario ni rango — un diseñador lo confirma después de contactar por WhatsApp o formulario. Ya reflejado en los mockups (`index.html`, `diseno-industrial.html`, `impresion-3d.html`).

## Deseable

- [ ] Corregir typo "Porfolio".
- [ ] Revisar velocidad de carga / spinner que no resuelve.
- [ ] Decidir si dar de baja o redirigir 3x3d.com.ar (SSL vencido).

## Antes de activar los anuncios

- [ ] Confirmar que las conversiones trackean bien con datos de prueba.
- [ ] Accesos de Google Ads y Google Analytics/Search Console compartidos o creados.
- [ ] Presupuesto final de arranque definido dentro de $180.000–250.000 ARS/mes.
