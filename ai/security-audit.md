# Security Audit

Hallazgos relevados hasta ahora (vía el Plan de Google Ads y las capturas del sitio). No es un audit de penetración — es lo que ya se detectó a simple vista.

## Hallazgos

- **SSL vencido en `3x3d.com.ar`** — dominio secundario asociado a la marca en buscadores. Riesgo: warning de seguridad en el navegador para quien lo encuentre buscando la marca. Acción sugerida: dar de baja o redirigir (punto 9 del plan).
- **Widget de Instagram con token vencido** — expone un mensaje de error de autorización (`wp_die`) públicamente en la home. No es una vulnerabilidad explotable, pero es una superficie de error visible que un atacante o competidor podría usar como señal de sitio desatendido.
- **Sin Google Tag / GA4** — no es un riesgo de seguridad, pero implica cero visibilidad sobre tráfico anómalo o abuso del formulario una vez activa la campaña paga.

## Pendiente de relevar (sin acceso al sitio real)

- Versión de WordPress y plugins instalados (el spinner roto sugiere al menos un plugin desactualizado o mal configurado).
- Si el formulario de contacto tiene protección anti-spam/CAPTCHA — relevante antes de recibir tráfico pago.
- Si el dominio principal tiene SSL vigente y HSTS configurado.
