# Context Loader — tresxtresd-diseno-industrial

Protocolo a ejecutar al inicio de cada sesión en este proyecto (invocado por `~/.claude/CLAUDE.md`).

## Orden de lectura

1. `sin-publicar/Plan de Google Ads – Tres x Tres D.md` — plan completo: diagnóstico del sitio, cambios requeridos, estructura de campañas, presupuesto.
2. `sin-publicar/*.jpeg` — capturas del sitio actual (home, impresión 3D, home con errores de Instagram visibles).
3. `ai/deploy-checklist.md` — qué falta resolver antes de lanzar la campaña.
4. `ai/analysis.md` — diagnóstico técnico del sitio ya relevado.
5. Artifacts publicados en la sesión (mockups del rediseño) — buscar con `Artifact action: list` si hace falta retomarlos.

## Estado del proyecto

El sitio viejo (WordPress) vive fuera de este repo, sin acceso. El sitio nuevo SÍ está acá: `index.html`, `diseno-industrial.html`, `impresion-3d.html` + `enviar.php` — HTML/CSS/JS estático, sin build, pensado para subir tal cual a hosting (DonWeb) y reemplazar el WordPress actual. Además vive acá el plan de campaña de Google Ads.

## Qué NO asumir

- No hay acceso a WordPress, GA4, ni Google Ads real — cualquier ID/credencial en archivos es placeholder salvo que diga lo contrario.
- El dominio `3x3d.com.ar` tiene SSL vencido — no usarlo en ningún link ni ejemplo.
