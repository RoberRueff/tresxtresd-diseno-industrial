# CLAUDE.md — Tres x Tres D

Referencia rápida de proyecto. El protocolo de sesión completo vive en `ai/context-loader.md` (invocado automáticamente por `~/.claude/CLAUDE.md`) — este archivo es el resumen para arrancar rápido.

## Qué es esto

El sitio actual en producción es WordPress (tresxtresd.com.ar, sin acceso), pero **el nuevo sitio NO se desarrolla en WordPress** — es HTML/CSS/JS estático (confirmado por el usuario 2026-09-23). `index.html`, `diseno-industrial.html`, `impresion-3d.html` ya no son solo mockups de referencia: son el sitio nuevo en construcción, pensado para subirse tal cual a hosting (DonWeb) reemplazando el WordPress actual. Además del plan de campaña de Google Ads, para Tres x Tres D, estudio real de diseño industrial e impresión 3D en CABA.

## Dónde está todo

- `index.html`, `diseno-industrial.html`, `impresion-3d.html` — las 3 páginas del sitio nuevo.
- `enviar.php` — backend mínimo (PHP `mail()`) para el formulario de `index.html`, envía a `info@tresxtresd.com.ar`. Requiere hosting con PHP (no probado aún en DonWeb real).
- `img/` — imágenes de los mockups (algunas son crops provisorios, ver `LEEME.txt`).
- `sin-publicar/` — plan de Google Ads, capturas del sitio real, logos de clientes.
- `ai/` — protocolo y estado: `context-loader.md`, `analysis.md`, `deploy-checklist.md`, `rules.md`, `guardrails.md`, `taxonomy.md`, `architecture.md`, `security-audit.md`, `checks.md`.

## Reglas que no se rompen

- Responder siempre en español.
- Nunca inventar IDs de tracking, tokens ni credenciales — placeholder explícito si no está confirmado por el usuario.
- Nunca usar `3x3d.com.ar` en ningún link o ejemplo (SSL vencido).
- Nunca mostrar tarifario ni rango de precio en sitio o anuncios — el precio lo confirma un diseñador después de contactar por WhatsApp o formulario.
- WhatsApp real ya cargado en los 3 mockups: `5491153399415`. No reemplazarlo sin indicación del usuario.
- Alcance vigente (fase 1): CABA + GBA, gastronomía/retail/oficinas. Alcance nacional/industrial es fase 2 — no expandir copy ni keywords sin que el usuario lo pida.

Detalle completo de cada regla en `ai/rules.md` y `ai/guardrails.md`.

## Antes de tocar algo

1. Seguir el orden de lectura de `ai/context-loader.md`.
2. Antes de dar un mockup por terminado, correr el checklist de `ai/checks.md`.
3. Los 3 HTML comparten la misma plantilla (mismo CSS, misma estructura de header/footer/WhatsApp) — un cambio de layout o copy compartido casi siempre va en las 3 páginas, no en una sola.
