from fastapi import FastAPI, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
from typing import List
import spotipy
from spotipy.oauth2 import SpotifyClientCredentials
import random
import os
from dotenv import load_dotenv

load_dotenv()  # Carga automáticamente el archivo .env de la raíz del proyecto

# Spotipy espera SPOTIPY_CLIENT_ID / SPOTIPY_CLIENT_SECRET, pero el .env de Laravel usa SPOTIFY_*
# Hacemos el mapeo aquí para no tener que cambiar el .env
if not os.environ.get('SPOTIPY_CLIENT_ID'):
    os.environ['SPOTIPY_CLIENT_ID'] = os.environ.get('SPOTIFY_CLIENT_ID', '')
if not os.environ.get('SPOTIPY_CLIENT_SECRET'):
    os.environ['SPOTIPY_CLIENT_SECRET'] = os.environ.get('SPOTIFY_CLIENT_SECRET', '')

sp = spotipy.Spotify(client_credentials_manager=SpotifyClientCredentials())
app = FastAPI(title="Motor de Recomendación Musical IA")

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_methods=["*"],
    allow_headers=["*"],
)

@app.get("/")
def home():
    return {"message": "API is running 🚀", "service": "MusicDiscovery AI"}

# 1. Definir la estructura de los datos que Laravel enviará
class UserPreferences(BaseModel):
    genres: List[str]
    album_ids: List[str]
    track_ids: List[str]

# Función auxiliar para calcular promedios
def calculate_average_features(features_list):
    if not features_list:
        return {}
    
    # Filtramos valores nulos por si alguna pista no tiene audio features
    valid_features = [f for f in features_list if f]
    if not valid_features: return {}

    keys = ['danceability', 'energy', 'valence', 'tempo']
    avg_features = {key: sum(f[key] for f in valid_features) / len(valid_features) for key in keys}
    return avg_features

@app.post("/api/v1/generate-discovery")
async def generate_discovery(prefs: UserPreferences):
    try:
        # --- PASO 1: EXTRAER AUDIO FEATURES DE CANCIONES (Peso Principal) ---
        song_features_raw = sp.audio_features(prefs.track_ids)
        song_vector = calculate_average_features(song_features_raw)

        # --- PASO 2: EXTRAER AUDIO FEATURES DE ÁLBUMES (Peso Secundario) ---
        # Obtenemos un par de pistas de cada álbum para entender el "vibe" del álbum
        album_tracks_ids = []
        for album_id in prefs.album_ids[:5]: # Tomamos los 5 que eligió
            tracks = sp.album_tracks(album_id, limit=2)
            album_tracks_ids.extend([t['id'] for t in tracks['items']])
        
        album_features_raw = sp.audio_features(album_tracks_ids)
        album_vector = calculate_average_features(album_features_raw)

        # --- PASO 3: CALCULAR VECTOR DE USUARIO PONDERADO ---
        # Damos 70% de importancia a las canciones sueltas y 30% a los álbumes
        target_attributes = {}
        for key in ['danceability', 'energy', 'valence', 'tempo']:
            if key in song_vector and key in album_vector:
                target_attributes[f'target_{key}'] = (song_vector[key] * 0.7) + (album_vector[key] * 0.3)
            elif key in song_vector:
                target_attributes[f'target_{key}'] = song_vector[key]

        # --- PASO 4: CONSULTAR A LA "IA" DE SPOTIFY CON SEMILLAS Y VECTORES ---
        # Spotify permite máximo 5 semillas en total (combinando géneros, artistas, tracks)
        seed_genres = prefs.genres[:2] if prefs.genres else []
        seed_tracks = prefs.track_ids[:3] if prefs.track_ids else []
        
        recommendations = sp.recommendations(
            seed_genres=seed_genres,
            seed_tracks=seed_tracks,
            limit=50, # Pedimos 50 para tener margen de filtrado
            **target_attributes
        )

        # --- PASO 5: FILTRAR Y CLASIFICAR LA RESPUESTA ---
        recommended_tracks = recommendations['tracks']
        
        result_albums = {}
        result_singles = []
        result_playlist = []

        for track in recommended_tracks:
            album = track['album']
            
            # Evitar recomendar álbumes que el usuario ya seleccionó
            if album['id'] in prefs.album_ids:
                continue

            # Buscar 2 Álbumes distintos (asegurando que sean de tipo 'album', no singles)
            if len(result_albums) < 2 and album['album_type'] == 'album':
                if album['id'] not in result_albums:
                    result_albums[album['id']] = {
                        "id": album['id'],
                        "name": album['name'],
                        "artist": album['artists'][0]['name'],
                        "image": album['images'][0]['url'] if album['images'] else None,
                        "url": album['external_urls']['spotify']
                    }
            
            # Buscar 2 Canciones (Singles) que no pertenezcan a los álbumes recién recomendados
            elif len(result_singles) < 2 and album['id'] not in result_albums:
                result_singles.append({
                    "id": track['id'],
                    "name": track['name'],
                    "artist": track['artists'][0]['name'],
                    "image": album['images'][0]['url'] if album['images'] else None,
                    "url": track['external_urls']['spotify']
                })
            
            # El resto va a la playlist mixta semanal (limitamos a 15 canciones por ejemplo)
            elif len(result_playlist) < 15:
                result_playlist.append({
                    "id": track['id'],
                    "name": track['name'],
                    "artist": track['artists'][0]['name'],
                    "url": track['external_urls']['spotify']
                })

        # --- PASO 6: RETORNAR LA DATA A LARAVEL ---
        return {
            "discovery_dashboard": {
                "recommended_albums": list(result_albums.values()),
                "recommended_singles": result_singles,
                "weekly_playlist": result_playlist
            }
        }

    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))