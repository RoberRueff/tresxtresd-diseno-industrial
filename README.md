# Tres x Tres D — Rediseño y campaña de Google Ads

Plan de campaña de Google Ads y mockups de rediseño para **Tres x Tres D**, estudio de diseño industrial e impresión 3D en CABA ([tresxtresd.com.ar](https://tresxtresd.com.ar)). Este repo no contiene el sitio en producción (WordPress, sin acceso) — es el material de referencia para lanzar la campaña y guiar el rediseño.

## Estructura

```
index.html                 Mockup Home
diseno-industrial.html     Mockup "Diseño industrial a medida"
impresion-3d.html          Mockup "Impresión 3D"
img/                       Imágenes de los mockups
LEEME.txt                  Qué completar antes de publicar (WhatsApp, tracking, endpoint del form)

sin-publicar/               Plan de Google Ads, capturas del sitio real, logos de clientes
ai/                          Protocolo de sesión, diagnóstico técnico y reglas del proyecto
```

## Ver los mockups

Son HTML estático, sin build. Para que las rutas relativas (`img/...`) funcionen, servirlos con un servidor local en vez de abrir el archivo directo:

```bash
python3 -m http.server 8000
```

Y abrir `http://localhost:8000/index.html`.

## Antes de publicar

Cada uno de los 3 HTML tiene un bloque `TXT_CONFIG` al final con lo que falta completar: fragmento del Google tag, `formEndpoint`, y las etiquetas de conversión de Google Ads (formulario y clic a WhatsApp). Detalle en `LEEME.txt` y en `ai/deploy-checklist.md`.

## Estado del proyecto

- Plan de campaña completo: `sin-publicar/Plan de Google Ads – Tres x Tres D.md`.
- Diagnóstico técnico del sitio real: `ai/analysis.md`.
- Checklist de lanzamiento: `ai/deploy-checklist.md`.
- Alcance actual: CABA + GBA, gastronomía/retail/oficinas (fase 1). Expansión nacional/industrial queda como fase 2.
