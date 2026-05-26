# Integración OAuth con Spotify (diseño)

Este documento describe los pasos y endpoints necesarios para implementar una integración oficial con Spotify para importar playlists de usuario.

Resumen
- Requiere registrar una app en Spotify Developers y obtener `client_id` y `client_secret`.
- Debes implementar el flujo OAuth 2.0 Authorization Code + Refresh Token.
- Alcance necesario: `playlist-read-private playlist-read-collaborative user-library-modify user-read-private` (ajustar según necesidad).

Pasos para configurar
1. Registrar app en https://developer.spotify.com/dashboard
   - Añadir Redirect URI(s) (ej: `https://tudominio.com/spotify/callback`).
   - Copiar `CLIENT_ID` y `CLIENT_SECRET`.
2. Almacenar credenciales en `config/services.php` y `.env`:

   SPOTIFY_CLIENT_ID=xxx
   SPOTIFY_CLIENT_SECRET=yyy
   SPOTIFY_REDIRECT_URI=https://tudominio.com/spotify/callback

3. Rutas a añadir (Laravel)

- GET `/spotify/connect` -> redirige a Spotify Auth URL
- GET `/spotify/callback` -> recibe `code`, obtiene `access_token` y `refresh_token`
- POST `/spotify/import-playlist` -> obtiene playlists del usuario y las importa

4. Flujo OAuth (Authorization Code)

- Construir URL de autorización:
  https://accounts.spotify.com/authorize?response_type=code&client_id={CLIENT_ID}&scope={SCOPES}&redirect_uri={REDIRECT_URI}&state={STATE}

- En el callback intercambiar `code` por tokens en:
  POST https://accounts.spotify.com/api/token
  body: `grant_type=authorization_code&code={code}&redirect_uri={REDIRECT_URI}`
  auth: Basic(base64(CLIENT_ID:CLIENT_SECRET))

- Guardar `access_token`, `refresh_token`, `expires_in` (y `token_obtained_at`) en DB (tabla `oauth_tokens` o en `users` si prefieres).

5. Endpoints Spotify a usar
- Obtener playlists del usuario:
  GET https://api.spotify.com/v1/me/playlists
- Obtener items de playlist (paginado):
  GET https://api.spotify.com/v1/playlists/{playlist_id}/tracks
- Buscar/obtener track details (si necesitas enriquecer):
  GET https://api.spotify.com/v1/tracks/{id}

6. Scopes recomendados
- `playlist-read-private` para leer playlists privadas del usuario.
- `playlist-read-collaborative` para playlists colaborativas.
- `user-library-modify` si quieres guardar en la librería del usuario (opcional).
- `user-read-private` para obtener info del usuario.

7. Manejo de tokens
- Implementar refresh token: POST a `/api/token` con `grant_type=refresh_token`.
- Guardar `expires_at` y si caduca, refrescar antes de llamadas a la API.

8. Seguridad
- Validar `state` en el callback para proteger contra CSRF.
- Guardar tokens cifrados si vas a almacenar `refresh_token`.
- Limitar llamadas y manejar rate limits (Spotify devuelve 429 con `Retry-After`).

9. Propuesta de implementación en Laravel (esqueleto)

- Modelo `SpotifyToken` (user_id, access_token, refresh_token, expires_at)
- `SpotifyController` con métodos:
  - `connect()` -> redirect a Spotify
  - `callback(Request $r)` -> intercambia code y guarda tokens
  - `importPlaylists(Request $r)` -> lista playlists y por cada track llama al servicio de búsqueda local (ItunesService/SpotifyService) y guarda con `UserLike::updateOrCreate`

Código ejemplo (esqueleto):

Route::middleware('auth')->group(function() {
    Route::get('/spotify/connect', [SpotifyController::class, 'connect']);
    Route::get('/spotify/callback', [SpotifyController::class, 'callback']);
    Route::post('/spotify/import-playlist', [SpotifyController::class, 'importPlaylist']);
});

10. Consideraciones UX
- Usuario pulsa "Importar desde Spotify".
- Si no está conectado, se le redirige al flujo OAuth.
- Mostrar selector de playlists y barra de progreso durante la importación.
- Permitir desmarcar canciones antes de confirmar import.

11. Permisos de revisión y despliegue
- Registrar la URL de producción en Spotify Dashboard.
- Si la app será pública y requiere scopes sensibles, Spotify puede requerir revisión.

12. Alternativa rápida (sin OAuth)
- Pegar texto (ya implementado) o permitir subir CSV/JSON exportado desde Spotify.

---
Si quieres, puedo:
- Generar controladores/esqueletos y migraciones para tokens.
- Implementar el flujo OAuth mínimo y el importador de playlists usando la API de Spotify.
- O bien mejorar más el parser (más heurísticas y limpieza). Indica por cuál quieres que empiece primero.
