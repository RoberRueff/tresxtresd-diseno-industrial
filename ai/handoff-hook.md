# Hook de HANDOFF.md — traspaso de contexto entre sesiones

## Qué resuelve

Cuando una conversación con Claude Code se alarga mucho, seguir dentro de la misma sesión (o usar `/compact`) consume cada vez más contexto y encarece cada respuesta. La alternativa (`/clear`) es gratis pero borra toda la memoria de la sesión — incluyendo el estado del trabajo en curso.

Este hook resuelve eso: permite cerrar la sesión pesada, arrancar una nueva limpia, y que esa sesión nueva reciba automáticamente un resumen del estado en el que quedó el trabajo.

## Cómo funciona

1. Cuando la sesión está pesada, le pedís a Claude: **"guardá el estado actual en HANDOFF.md"**.
2. Hacés **`/clear`**.
3. El hook `SessionStart` (configurado en `.claude/settings.json`) se dispara al arrancar o al hacer `/clear`, lee `HANDOFF.md` y se lo inyecta como contexto a la sesión nueva.

## Configuración (ya aplicada)

En `.claude/settings.json`:

```json
{
  "hooks": {
    "SessionStart": [
      {
        "matcher": "startup|clear",
        "hooks": [
          {
            "type": "command",
            "command": "cat \"$CLAUDE_PROJECT_DIR/HANDOFF.md\" 2>/dev/null || true"
          }
        ]
      }
    ]
  }
}
```

- `matcher: "startup|clear"` → se ejecuta tanto al abrir Claude Code como al correr `/clear`.
- El `cat` no falla si `HANDOFF.md` no existe todavía (`|| true`).

## Uso

- **Guardar estado:** pedile a Claude "guardá el estado actual en HANDOFF.md" antes de limpiar la sesión. Claude debería resumir ahí: qué se está haciendo, qué falta, decisiones tomadas, y cualquier dato que no esté ya en CLAUDE.md o en el código.
- **`HANDOFF.md` es descartable:** no es memoria permanente del proyecto (eso va en `CLAUDE.md` y `ai/`). Es un archivo de traspaso puntual — se puede sobrescribir o borrar una vez que la sesión nueva absorbió el contexto.
- **No lo committees si tiene info transitoria irrelevante a futuro.** Si el resumen contiene algo que sí vale para todo el proyecto (una regla, una decisión de arquitectura), eso debería migrar a `CLAUDE.md` o al archivo correspondiente en `ai/`, no quedar solo en `HANDOFF.md`.

## Cuándo usar cada mecanismo

| Situación | Acción |
|---|---|
| Sesión larga, necesitás seguir con el mismo hilo | Pedir handoff a `HANDOFF.md` + `/clear` |
| Sesión larga, no necesitás continuidad | `/clear` directo (gratis, sin handoff) |
| Trabajo cerrado, nada que continuar | `/clear` directo |
| Info válida para todo el proyecto (reglas, decisiones fijas) | Va en `CLAUDE.md`, no en `HANDOFF.md` |
