<?php $__env->startSection('title', 'Descubre tu música'); ?>

<?php $__env->startSection('content'); ?>
<div x-data="onboardingFlow()" class="relative min-h-screen pb-28">

    
    <div
        x-show="isInterludeActive"
        x-transition:enter="transition ease-out duration-700"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-700"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex flex-col items-center justify-center bg-black/90 backdrop-blur-xl"
        style="display: none;"
    >
        
        <div class="absolute top-1/3 left-1/4 w-64 h-64 bg-purple-600/20 rounded-full blur-3xl animate-float"></div>
        <div class="absolute bottom-1/3 right-1/4 w-48 h-48 bg-fuchsia-500/20 rounded-full blur-3xl animate-float" style="animation-delay: 1s;"></div>

        <h2
            x-text="interludeMessage"
            x-show="isInterludeActive"
            x-transition:enter="transition ease-out duration-1000 delay-200"
            x-transition:enter-start="opacity-0 translate-y-8"
            x-transition:enter-end="opacity-100 translate-y-0"
            class="relative z-10 text-4xl md:text-6xl font-black tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-white via-purple-200 to-fuchsia-300 text-center px-6 max-w-2xl"
        ></h2>

        <div class="mt-8 flex gap-2">
            <span class="w-2 h-2 rounded-full bg-accent animate-pulse"></span>
            <span class="w-2 h-2 rounded-full bg-accent-light animate-pulse" style="animation-delay:.2s"></span>
            <span class="w-2 h-2 rounded-full bg-fuchsia-400 animate-pulse" style="animation-delay:.4s"></span>
        </div>
    </div>

    
    <div x-show="pageLoaded" 
         x-transition:enter="transition ease-out duration-1000"
         x-transition:enter-start="opacity-0 -translate-y-8"
         x-transition:enter-end="opacity-100 translate-y-0"
         style="display: none;"
         class="relative z-10 max-w-5xl mx-auto px-6 pt-12 pb-8">
        <nav class="flex items-center justify-between mb-8">
            <a href="<?php echo e(route('home')); ?>" class="flex items-center gap-3 group">
                <img src="<?php echo e(asset('multimedia/img/logo_3d.png')); ?>" alt="Project Icon" class="h-14 w-14 object-contain transition-transform group-hover:scale-110">
                <span class="text-xl  text-gray-800 tracking-tight">| Descubrimientos</span>
            </a>

            
            <div class="flex items-center gap-3">
                <template x-for="i in 4" :key="i">
                    <div class="flex items-center gap-2">
                        <button 
                             @click="goToStep(i)"
                             :disabled="i > step"
                             class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all duration-500"
                             :class="[
                                step >= i ? 'bg-accent text-white shadow-lg shadow-accent/30' : 'bg-white border border-gray-200 text-gray-400',
                                i <= step ? 'cursor-pointer hover:bg-purple-600 hover:text-white' : 'cursor-not-allowed'
                             ]">
                            <span x-text="i"></span>
                        </button>
                        <div x-show="i < 4" class="w-8 h-0.5 rounded-full transition-colors duration-500"
                             :class="step > i ? 'bg-accent' : 'bg-gray-200'"></div>
                    </div>
                </template>
            </div>
        </nav>
    </div>

    
    <div x-show="step === 1"
         x-transition:enter="transition ease-out duration-700 delay-300"
         x-transition:enter-start="opacity-0 translate-y-12"
         x-transition:enter-end="opacity-100 translate-y-0"
         style="display: none;"
         class="max-w-5xl mx-auto px-6 w-full flex flex-col items-center">

        <div class="mb-2 w-full flex flex-col items-center text-center">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white border border-gray-200 shadow-sm text-xs font-medium text-gray-600 mb-4">
                <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
                Paso 1 de 3
            </div>
            <h1 class="text-4xl md:text-5xl font-black leading-tight mb-2 text-gray-900">
                ¿Qué géneros prefieres?
            </h1>
            <p class="text-lg text-gray-500">Selecciona entre 3 y 5 géneros para empezar.</p>
        </div>

        
        <div class="w-full max-w-3xl mx-auto flex justify-start mt-10 mb-[-1.5rem] h-12 relative z-20 px-2 md:px-0">
            <div class="relative flex items-center justify-start transform origin-left transition-all duration-500 ease-out" 
                 :class="isGenreSearchExpanded ? 'w-full max-w-sm' : 'w-12'">
                
                
                <button 
                    @click="isGenreSearchExpanded = true; $nextTick(() => $refs.genreSearchInput.focus())"
                    x-show="!isGenreSearchExpanded"
                    x-transition:enter="transition-opacity duration-300 delay-200"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition-opacity duration-100"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="absolute w-12 h-12 rounded-full bg-white/90 shadow-sm border border-purple-200 flex items-center justify-center text-purple-500 hover:bg-white hover:scale-110 hover:shadow-md hover:-translate-y-1 transition-all"
                >
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </button>
                
                
                <div x-show="isGenreSearchExpanded"
                     x-transition:enter="transition-all duration-500 ease-out"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition-all duration-300 ease-in"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="w-full relative shadow-lg rounded-full"
                     @click.outside="if(genreSearchQuery === '') isGenreSearchExpanded = false">
                     
                    <input 
                        x-ref="genreSearchInput"
                        type="text" 
                        x-model="genreSearchQuery" 
                        placeholder="Escribe un género..." 
                        class="block w-full pl-12 pr-12 py-3 border-2 border-purple-300 rounded-full bg-white text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-4 focus:ring-purple-400/30 focus:border-purple-500 transition-all font-medium"
                    >
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <div class="absolute inset-y-0 right-0 pr-2 flex items-center">
                        <button @click="if(genreSearchQuery === '') { isGenreSearchExpanded = false; } else { genreSearchQuery = ''; $refs.genreSearchInput.focus(); }" 
                                class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-full transition-colors">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        
        <div x-show="genres.length > 0" class="mt-12 mb-10 w-full relative" x-ref="genreGridContainer">
            
            <svg class="absolute inset-0 w-full h-full pointer-events-none z-0" style="overflow: visible;">
                <template x-for="(conn, idx) in gridConnections" :key="idx">
                    <line :x1="conn.x1" :y1="conn.y1" :x2="conn.x2" :y2="conn.y2" stroke="#a855f7" stroke-width="2" class="opacity-60" stroke-linecap="round"></line>
                </template>
            </svg>
            <div class="relative w-full max-w-3xl mx-auto">
                
                <button x-show="genreSearchQuery === '' && currentGenrePage > 0"
                        @click="currentGenrePage--; updateConnections()"
                        class="absolute -left-12 md:-left-20 top-1/2 -translate-y-1/2 rounded-full bg-white text-purple-600 shadow-md border border-purple-100 hover:bg-purple-50 hover:scale-110 transition-all duration-300 w-12 h-12 flex items-center justify-center z-20 cursor-pointer">
                    <svg class="w-6 h-6 pr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                </button>

                <div class="flex flex-wrap justify-center gap-3 relative z-10 w-full pb-10 pt-4">
                    <template x-for="(genre, index) in filteredGenres" :key="genre">
                        <button
                            :id="'tgt-genre-' + index"
                            :data-genre="genre"
                            @click="toggleGenre(genre)"
                            class="rounded-full font-medium transition-all duration-500 relative flex justify-center items-center cursor-pointer"
                            :class="[
                                selectedGenres.includes(genre)
                                    ? 'bg-purple-100 border-2 border-purple-400 text-purple-900 shadow-[0_0_15px_rgba(168,85,247,0.4)] scale-110 z-20 px-6 py-2.5 font-bold'
                                    : 'bg-white/90 text-gray-700 shadow-sm border border-transparent hover:bg-white hover:shadow-md hover:-translate-y-1 hover:text-purple-600 z-10',
                                
                                /* Clean visual sizing without offsets */
                                (!selectedGenres.includes(genre) && index % 4 === 0) ? 'text-lg px-6 py-2' : '',
                                (!selectedGenres.includes(genre) && index % 4 === 1) ? 'text-sm px-4 py-1.5' : '',
                                (!selectedGenres.includes(genre) && index % 4 === 2) ? 'text-base px-5 py-2' : '',
                                (!selectedGenres.includes(genre) && index % 4 === 3) ? 'text-sm px-4 py-1.5' : ''
                            ]"
                            style="animation: fadeUp 0.5s ease backwards;"
                            :style="`animation-delay: ${(index % 25) * 0.02}s`"
                        >
                            <span x-text="genre" class="capitalize"></span>
                            
                            
                            <svg x-show="selectedGenres.includes(genre)" class="absolute -top-1 -right-1 w-5 h-5 bg-purple-500 text-white rounded-full p-1 border-2 border-white shadow-sm" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                            </svg>
                        </button>
                    </template>
                </div>

                
                <button x-show="genreSearchQuery === '' && currentGenrePage < maxGenrePage"
                        @click="currentGenrePage++; updateConnections()"
                        class="absolute -right-12 md:-right-20 top-1/2 -translate-y-1/2 rounded-full bg-white text-purple-600 shadow-md border border-purple-100 hover:bg-purple-50 hover:scale-110 transition-all duration-300 w-12 h-12 flex items-center justify-center z-20 cursor-pointer">
                    <svg class="w-6 h-6 pl-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </button>
            </div>
            
            
            <div x-show="filteredGenres.length === 0 && genres.length > 0" class="w-full py-12 text-center text-gray-500">
                <i class="fa-solid fa-ghost text-4xl mb-3 opacity-20"></i>
                <p>No se encontraron géneros que coincidan con "<span x-text="genreSearchQuery"></span>"</p>
            </div>
        </div>

        
        <div x-show="genres.length === 0" class="flex items-center justify-center py-20 w-full">
            <div class="flex items-center gap-3 text-purple-500">
                <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <span class="text-gray-600 font-medium">Cargando biblioteca musical...</span>
            </div>
        </div>

        
        <div class="w-full max-w-4xl flex items-end justify-between mt-auto">
            <div class="text-gray-500 text-sm font-medium pt-8">
                <span x-text="selectedGenres.length" class="text-purple-600 font-bold"></span> / 5 seleccionados
            </div>
            
            <div class="relative flex flex-col items-center">
                
                <svg class="w-28 h-14 overflow-hidden" viewBox="0 0 100 50">
                    <path class="text-gray-200" stroke-width="8" stroke="currentColor" fill="none" stroke-linecap="round" d="M 10,50 A 40,40 0 0,1 90,50" />
                    <!-- Progress arc based on 0-5 -->
                    <path class="text-purple-600 transition-all duration-500 ease-out" stroke-width="8" stroke="currentColor" fill="none" stroke-linecap="round" 
                          :stroke-dasharray="125"
                          :stroke-dashoffset="125 - (125 * (Math.min(selectedGenres.length, 5) / 5))"
                          d="M 10,50 A 40,40 0 0,1 90,50" />
                </svg>
                <div class="absolute bottom-0 font-black text-purple-900 text-lg">
                    <span x-text="Math.round(Math.min(selectedGenres.length, 5) / 5 * 100) + '%'"></span>
                </div>
            </div>

            <button
                @click="nextStep(2, 'Buen gusto...')"
                :disabled="selectedGenres.length < 3"
                class="px-8 py-3 rounded-full font-bold text-sm tracking-wide transition-all duration-300 flex items-center gap-2"
                :class="selectedGenres.length >= 3
                    ? 'bg-purple-600 hover:bg-purple-700 text-white shadow-lg shadow-purple-500/40 hover:scale-105 hover:-translate-y-1'
                    : 'bg-gray-200 text-gray-400 cursor-not-allowed'"
            >
                Continuar &rarr;
            </button>
        </div>
    </div>

    
    <div x-show="step === 2"
         x-transition:enter="transition ease-out duration-500"
         x-transition:enter-start="opacity-0 translate-y-8"
         x-transition:enter-end="opacity-100 translate-y-0"
         style="display: none;"
         class="max-w-4xl mx-auto px-6">

        <div class="mb-10 w-full flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white border border-gray-200 shadow-sm text-xs font-medium text-gray-600 mb-4">
                    <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
                    Paso 2 de 3
                </div>
                <h1 class="text-4xl md:text-5xl font-black leading-tight mb-2 text-gray-900">
                    Álbumes
                </h1>
                <p class="text-lg text-gray-500">Selecciona 5 álbumes que representen tu identidad musical.</p>
            </div>

            <div class="flex flex-col items-end gap-4 w-full md:w-auto">
                
                <div class="relative w-full md:w-96">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input
                        type="text"
                        x-model="albumQuery"
                        @input.debounce.400ms="searchAlbums()"
                        placeholder="Buscar álbumes musicales..."
                        class="w-full pl-12 pr-4 py-4 rounded-3xl bg-white/70 border border-purple-200 text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:border-transparent focus:ring-purple-400 transition-all shadow-sm"
                    />
                    <div x-show="isSearchingAlbums" class="absolute right-4 top-1/2 -translate-y-1/2">
                        <svg class="w-5 h-5 animate-spin text-accent-light" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="flex flex-col md:flex-row gap-8 w-full">
            
            
            <div class="flex-1 flex flex-col">
                
                <div x-show="albumResults.length > 0" class="order-1 mb-8">
                    <h3 class="font-bold text-gray-700 mb-4">Resultados de búsqueda</h3>
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                        <template x-for="album in albumResults" :key="album.id">
                            <div @click="toggleAlbum(album)"
                                 class="group cursor-pointer rounded-xl overflow-hidden transition-all duration-300"
                                 :class="selectedAlbums.find(a => a.id === album.id) ? 'ring-2 ring-accent shadow-lg shadow-accent/20 scale-[1.02]' : 'hover:scale-105'">
                                <div class="relative aspect-square bg-gray-100">
                                    <img :src="album.image || 'https://picsum.photos/seed/' + album.id + '/400/400'" :alt="album.name" class="w-full h-full object-cover">
                                    
                                    <div x-show="selectedAlbums.find(a => a.id === album.id)"
                                         class="absolute inset-0 bg-purple-500/40 flex items-center justify-center backdrop-blur-[2px]">
                                        <svg class="w-12 h-12 text-white drop-shadow-md" fill="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="p-3 bg-white border-t border-gray-100">
                                    <p class="text-xs font-bold text-gray-800 truncate" x-text="album.name"></p>
                                    <p class="text-[10px] text-gray-500 truncate" x-text="album.artist + (album.year ? ' · ' + album.year : '')"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                
                <div x-show="recommendedAlbumsByGenre.length > 0" class="order-2 mb-8">
                    <h3 class="font-bold text-gray-700 mb-4">Sugerencias basadas en tus géneros</h3>
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                        <template x-for="album in recommendedAlbumsByGenre" :key="'rec-' + album.id">
                            <div @click="toggleAlbum(album)"
                                 class="group cursor-pointer rounded-xl overflow-hidden transition-all duration-300"
                                 :class="selectedAlbums.find(a => a.id === album.id) ? 'ring-2 ring-accent shadow-lg shadow-accent/20 scale-[1.02]' : 'hover:scale-105'">
                                <div class="relative aspect-square bg-gray-100">
                                    <img :src="album.image || 'https://picsum.photos/seed/' + album.id + '/400/400'" :alt="album.name" class="w-full h-full object-cover">
                                    
                                    <div x-show="selectedAlbums.find(a => a.id === album.id)"
                                         class="absolute inset-0 bg-purple-500/40 flex items-center justify-center backdrop-blur-[2px]">
                                        <svg class="w-12 h-12 text-white drop-shadow-md" fill="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="p-3 bg-white border-t border-gray-100">
                                    <p class="text-xs font-bold text-gray-800 truncate" x-text="album.name"></p>
                                    <p class="text-[10px] text-gray-500 truncate" x-text="album.artist"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            
            <div class="w-full md:w-32 lg:w-40 flex-shrink-0">
                <div x-show="selectedAlbums.length > 0" class="sticky top-6 flex flex-col items-center">
                    <h3 class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-4 text-center">Seleccionados</h3>
                    <div class="flex flex-col gap-3 items-center">
                        <template x-for="album in selectedAlbums" :key="'sel-' + album.id">
                            <div class="relative group w-20 h-20 md:w-24 md:h-24 transition-transform hover:scale-105">
                                <img :src="album.image" :alt="album.name" class="w-full h-full rounded-xl object-cover ring-2 ring-accent/60 shadow-md">
                                <button @click="toggleAlbum(album)" class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 hover:bg-red-600 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity shadow-lg">
                                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="w-full max-w-4xl flex items-end justify-between mt-auto">
            <div class="text-gray-500 text-sm font-medium pt-8">
                <span x-text="selectedAlbums.length" class="text-purple-600 font-bold"></span> / 5 seleccionados
            </div>
            
            <div class="relative flex flex-col items-center">
                
                <svg class="w-28 h-14 overflow-hidden" viewBox="0 0 100 50">
                    <path class="text-gray-200" stroke-width="8" stroke="currentColor" fill="none" stroke-linecap="round" d="M 10,50 A 40,40 0 0,1 90,50" />
                    <path class="text-purple-600 transition-all duration-500 ease-out" stroke-width="8" stroke="currentColor" fill="none" stroke-linecap="round" 
                          :stroke-dasharray="125"
                          :stroke-dashoffset="125 - (125 * (Math.min(selectedAlbums.length, 5) / 5))"
                          d="M 10,50 A 40,40 0 0,1 90,50" />
                </svg>
                <div class="absolute bottom-0 font-black text-purple-900 text-lg">
                    <span x-text="Math.round(Math.min(selectedAlbums.length, 5) / 5 * 100) + '%'"></span>
                </div>
            </div>

            <button
                @click="nextStep(3, 'Interesante selección...')"
                :disabled="selectedAlbums.length < 5"
                class="px-8 py-3 rounded-full font-bold text-sm tracking-wide transition-all duration-300 flex items-center gap-2"
                :class="selectedAlbums.length >= 5
                    ? 'bg-purple-600 hover:bg-purple-700 text-white shadow-lg shadow-purple-500/40 hover:scale-105 hover:-translate-y-1'
                    : 'bg-gray-200 text-gray-400 cursor-not-allowed'"
            >
                Continuar &rarr;
            </button>
        </div>
    </div>

    
    <div x-show="step === 3"
         x-transition:enter="transition ease-out duration-500"
         x-transition:enter-start="opacity-0 translate-y-8"
         x-transition:enter-end="opacity-100 translate-y-0"
         style="display: none;"
         class="max-w-4xl mx-auto px-6">

        <div class="mb-10 w-full flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white border border-gray-200 shadow-sm text-xs font-medium text-gray-600 mb-4">
                    <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
                    Paso 3 de 3
                </div>
                <h1 class="text-4xl md:text-5xl font-black leading-tight mb-2 text-gray-900">
                    Canciones
                </h1>
                <p class="text-lg text-gray-500">Busca y selecciona 5 canciones que siempre te acompañan.</p>
            </div>

            <div class="flex flex-col items-end gap-4 w-full md:w-auto">
                
                <div class="relative w-full md:w-96">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input
                        type="text"
                        x-model="trackQuery"
                        @input.debounce.400ms="searchTracks()"
                        placeholder="Buscar canciones musicales..."
                        class="w-full pl-12 pr-4 py-4 rounded-3xl bg-white/70 border border-purple-200 text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:border-transparent focus:ring-purple-400 transition-all shadow-sm"
                    />
                    <div x-show="isSearchingTracks" class="absolute right-4 top-1/2 -translate-y-1/2">
                        <svg class="w-5 h-5 animate-spin text-accent-light" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="flex flex-col md:flex-row gap-8 w-full">
            
            
            <div class="flex-1 flex flex-col">
                
                <div x-show="trackResults.length > 0" class="order-1 mb-8">
                    <h3 class="font-bold text-gray-700 mb-4">Resultados de búsqueda</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <template x-for="track in trackResults" :key="track.id">
                            <div @click="toggleTrack(track)"
                                 class="flex items-center gap-4 p-3 rounded-xl cursor-pointer transition-all duration-300"
                                 :class="selectedTracks.find(t => t.id === track.id)
                                    ? 'bg-purple-100 ring-1 ring-purple-300 shadow-sm scale-[1.01]'
                                    : 'bg-white hover:bg-gray-50 border border-transparent hover:border-gray-200'">
                                <img :src="track.image || 'https://picsum.photos/seed/' + track.id + '/100/100'" :alt="track.name" class="w-12 h-12 rounded-lg object-cover shadow-sm">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-800 truncate" x-text="track.name"></p>
                                    <p class="text-xs text-gray-500 truncate" x-text="track.artist + ' · ' + track.album"></p>
                                </div>
                                <span class="text-xs text-gray-400 font-medium" x-text="track.duration"></span>
                                <div x-show="selectedTracks.find(t => t.id === track.id)" class="text-purple-600">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                                    </svg>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                
                <div x-show="recommendedTracksByGenre.length > 0" class="order-2 mb-8">
                    <h3 class="font-bold text-gray-700 mb-4">Sugerencias para ti</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <template x-for="track in recommendedTracksByGenre" :key="'rec-track-' + track.id">
                            <div @click="toggleTrack(track)"
                                 class="flex items-center gap-4 p-3 rounded-xl cursor-pointer transition-all duration-300"
                                 :class="selectedTracks.find(t => t.id === track.id)
                                    ? 'bg-purple-100 ring-1 ring-purple-300 shadow-sm scale-[1.01]'
                                    : 'bg-white hover:bg-gray-50 border border-transparent hover:border-gray-200'">
                                <img :src="track.image || 'https://picsum.photos/seed/' + track.id + '/100/100'" :alt="track.name" class="w-12 h-12 rounded-lg object-cover shadow-sm">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-800 truncate" x-text="track.name"></p>
                                    <p class="text-xs text-gray-500 truncate" x-text="track.artist + (track.album ? ' · ' + track.album : '')"></p>
                                </div>
                                <div x-show="selectedTracks.find(t => t.id === track.id)" class="text-purple-600">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                                    </svg>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            
            <div class="w-full md:w-32 lg:w-40 flex-shrink-0">
                <div x-show="selectedTracks.length > 0" class="sticky top-6 flex flex-col items-center">
                    <h3 class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-4 text-center">Seleccionadas</h3>
                    <div class="flex flex-col gap-3 items-center">
                        <template x-for="track in selectedTracks" :key="'sel-' + track.id">
                            <div class="relative group w-20 h-20 md:w-24 md:h-24 transition-transform hover:scale-105">
                                <img :src="track.image" :alt="track.name" class="w-full h-full rounded-xl object-cover ring-2 ring-accent/60 shadow-md">
                                <button @click="toggleTrack(track)" class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 hover:bg-red-600 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity shadow-lg">
                                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                                <div class="absolute bottom-0 inset-x-0 bg-black/60 rounded-b-xl px-1 py-1 overflow-hidden backdrop-blur-sm">
                                    <p class="text-[9px] text-white truncate text-center font-medium" x-text="track.name"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="w-full max-w-4xl flex items-end justify-between mt-auto">
            <div class="text-gray-500 text-sm font-medium pt-8">
                <span x-text="selectedTracks.length" class="text-purple-600 font-bold"></span> / 5 seleccionadas
            </div>
            
            <div class="relative flex flex-col items-center">
                
                <svg class="w-28 h-14 overflow-hidden" viewBox="0 0 100 50">
                    <path class="text-gray-200" stroke-width="8" stroke="currentColor" fill="none" stroke-linecap="round" d="M 10,50 A 40,40 0 0,1 90,50" />
                    <path class="text-purple-600 transition-all duration-500 ease-out" stroke-width="8" stroke="currentColor" fill="none" stroke-linecap="round" 
                          :stroke-dasharray="125"
                          :stroke-dashoffset="125 - (125 * (Math.min(selectedTracks.length, 5) / 5))"
                          d="M 10,50 A 40,40 0 0,1 90,50" />
                </svg>
                <div class="absolute bottom-0 font-black text-purple-900 text-lg">
                    <span x-text="Math.round(Math.min(selectedTracks.length, 5) / 5 * 100) + '%'"></span>
                </div>
            </div>

            <button
                @click="nextStep(4, 'Creo que ya nos conocemos.')"
                :disabled="selectedTracks.length < 5"
                class="px-8 py-3 rounded-full font-bold text-sm tracking-wide transition-all duration-300 flex items-center gap-2"
                :class="selectedTracks.length >= 5
                    ? 'bg-purple-600 hover:bg-purple-700 text-white shadow-lg shadow-purple-500/40 hover:scale-105 hover:-translate-y-1'
                    : 'bg-gray-200 text-gray-400 cursor-not-allowed'"
            >
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                Descubrir mi música
            </button>
        </div>
    </div>

    
    <div x-show="step === 4"
         x-transition:enter="transition ease-out duration-1000"
         x-transition:enter-start="opacity-0 translate-y-12"
         x-transition:enter-end="opacity-100 translate-y-0"
         style="display: none;">

        
        <div x-show="isLoadingDashboard" class="max-w-4xl mx-auto px-6 py-20 text-center">
            <div class="inline-flex items-center gap-3 text-purple-500 text-lg">
                <svg class="w-6 h-6 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <span class="text-gray-600 font-medium">Analizando tu perfil musical...</span>
            </div>
        </div>

        
        <div x-show="!isLoadingDashboard && dashboardData">

            
            <section class="relative overflow-hidden bg-white/40 shadow-sm border-b border-purple-100 rounded-b-3xl">
                <div class="absolute inset-0 bg-gradient-to-br from-white via-purple-50 to-purple-100 animate-gradient opacity-90"></div>
                <div class="absolute top-20 left-10 w-64 h-64 bg-purple-300/40 rounded-full blur-3xl animate-float"></div>
                <div class="absolute bottom-10 right-20 w-48 h-48 bg-purple-200/40 rounded-full blur-3xl animate-float" style="animation-delay: 2s"></div>

                <div class="relative z-10 max-w-7xl mx-auto px-6 pt-12 pb-16">
                    <div class="flex flex-col lg:flex-row items-center gap-12">
                        
                        <div class="flex-1 space-y-6">
                            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white border border-purple-200 shadow-sm text-xs font-medium text-purple-700">
                                <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
                                Generado con IA
                            </div>
                            <h1 class="text-5xl lg:text-7xl font-black leading-tight text-gray-900">
                                Tu Mix<br>
                                <span class="bg-gradient-to-r from-purple-500 via-fuchsia-500 to-pink-500 bg-clip-text text-transparent">Semanal</span>
                            </h1>
                            <p class="text-lg text-gray-600 max-w-md" x-text="(dashboardData?.weekly_playlist?.length || 0) + ' canciones seleccionadas especialmente para ti, basadas en tus gustos musicales.'"></p>

                            <div class="flex items-center gap-4 py-4">
                                <button @click="saveAllLikes()" class="px-6 py-2.5 bg-pink-100 hover:bg-pink-200 text-pink-700 rounded-full font-bold text-sm tracking-wide transition-all shadow-sm flex items-center gap-2">
                                    <i class="fa-solid fa-heart"></i>
                                    Guardar en Me Gusta
                                </button>
                                
                                <div class="relative" x-data="{ openExport: false }">
                                    <button @click="openExport = !openExport" @click.away="openExport = false" class="px-6 py-2.5 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 rounded-full font-bold text-sm tracking-wide transition-all shadow-sm flex items-center gap-2">
                                        <i class="fa-solid fa-file-export"></i>
                                        Exportar
                                        <i class="fa-solid fa-chevron-down text-xs ml-1"></i>
                                    </button>
                                    <div x-show="openExport" x-transition class="absolute left-0 mt-2 w-48 bg-white rounded-xl shadow-xl border border-gray-100 py-2 z-50">
                                        <button @click="exportList('Spotify'); openExport = false" class="w-full text-left px-4 py-2 hover:bg-gray-50 text-sm text-gray-700 flex items-center gap-2">
                                            <i class="fa-brands fa-spotify text-green-500"></i> Copiar para Spotify
                                        </button>
                                        <button @click="exportList('Apple Music'); openExport = false" class="w-full text-left px-4 py-2 hover:bg-gray-50 text-sm text-gray-700 flex items-center gap-2">
                                            <i class="fa-brands fa-apple text-gray-900"></i> Copiar para Apple Music
                                        </button>
                                        <button @click="exportList('YouTube'); openExport = false" class="w-full text-left px-4 py-2 hover:bg-gray-50 text-sm text-gray-700 flex items-center gap-2">
                                            <i class="fa-brands fa-youtube text-red-500"></i> Copiar para YouTube
                                        </button>
                                    </div>
                                </div>
                            </div>
                            </div>
                            
                            <div class="flex flex-wrap gap-2">
                                <template x-for="genre in selectedGenres" :key="'pill-' + genre">
                                    <span class="px-3 py-1 text-xs font-medium rounded-full bg-purple-100 border border-purple-200 text-purple-800 capitalize shadow-sm" x-text="genre"></span>
                                </template>
                            </div>
                        </div>

                        
                        <div class="flex-1 w-full max-w-lg">
                            <div class="bg-white/80 backdrop-blur-xl rounded-3xl p-6 border border-purple-100 shadow-xl space-y-2">
                                <template x-for="(track, index) in (dashboardData?.weekly_playlist || []).slice(0, 8)" :key="'pl-' + track.id">
                                    <div @click="playTrack(track, index)"
                                         class="flex items-center gap-4 p-3 rounded-xl cursor-pointer transition-all duration-200"
                                         :class="currentTrack?.id === track.id ? 'bg-purple-100 ring-1 ring-purple-300' : 'hover:bg-purple-50'">
                                        <span class="text-sm font-bold text-purple-400 w-6 text-right" x-text="index + 1"></span>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-semibold text-gray-900 truncate" x-text="track.name"></p>
                                            <p class="text-xs text-gray-500 truncate" x-text="track.artist"></p>
                                        </div>
                                        <div class="flex items-center gap-3 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <button @click.stop="toggleLike(track, 'song')" class="text-gray-300 hover:text-pink-500 transition-colors" :class="likesCache['song_'+track.id] ? '!text-pink-500' : ''" title="Me Gusta">
                                                <i class="fa-solid fa-heart text-lg"></i>
                                            </button>
                                            </button>
                                            <a :href="'https://www.youtube.com/results?search_query=' + encodeURIComponent(track.artist + ' ' + track.name)" target="_blank" @click.stop class="text-red-500 hover:text-red-600 transition-colors" title="Abrir en YouTube">
                                                <i class="fa-brands fa-youtube text-lg"></i>
                                            </a>
                                        </div>
                                    </div>
                                </template>

                                
                                <template x-if="(dashboardData?.weekly_playlist || []).length > 8 && !showAllPlaylist">
                                    <div class="pt-2 text-center">
                                        <button @click="showAllPlaylist = true" class="text-xs font-semibold text-purple-600 hover:text-purple-800 transition-colors">
                                            <span x-text="'Ver todas las ' + dashboardData.weekly_playlist.length + ' canciones'"></span>
                                        </button>
                                    </div>
                                </template>

                                
                                <template x-if="showAllPlaylist">
                                    <div class="space-y-1 pt-1">
                                        <template x-for="(track, index) in (dashboardData?.weekly_playlist || []).slice(8)" :key="'pl2-' + track.id">
                                            <div @click="playTrack(track, index + 8)"
                                                 class="flex items-center gap-4 p-3 rounded-xl cursor-pointer transition-all duration-200"
                                                 :class="currentTrack?.id === track.id ? 'bg-purple-100 ring-1 ring-purple-300' : 'hover:bg-purple-50'">
                                                <span class="text-sm font-bold text-purple-400 w-6 text-right" x-text="index + 9"></span>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-semibold text-gray-900 truncate" x-text="track.name"></p>
                                                    <p class="text-xs text-gray-500 truncate" x-text="track.artist"></p>
                                                </div>
                                                <div class="flex items-center gap-3 opacity-0 group-hover:opacity-100 transition-opacity">
                                                    <button @click.stop="toggleLike(track, 'song')" class="text-gray-300 hover:text-pink-500 transition-colors" :class="likesCache['song_'+track.id] ? '!text-pink-500' : ''" title="Me Gusta">
                                                        <i class="fa-solid fa-heart text-lg"></i>
                                                    </button>
                                                    </button>
                                                    <a :href="'https://www.youtube.com/results?search_query=' + encodeURIComponent(track.artist + ' ' + track.name)" target="_blank" @click.stop class="text-red-500 hover:text-red-600 transition-colors" title="Abrir en YouTube">
                                                        <i class="fa-brands fa-youtube text-lg"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </template>
                                        <div class="pt-2 text-center">
                                            <button @click="showAllPlaylist = false" class="text-xs font-semibold text-purple-600 hover:text-purple-800 transition-colors">
                                                Mostrar menos
                                            </button>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            
            <section class="max-w-7xl mx-auto px-6 py-16">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-900">Álbumes para Ti</h2>
                        <p class="text-gray-500 mt-1">Descubiertos por nuestra IA</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <template x-for="album in (dashboardData?.recommended_albums || [])" :key="'rec-' + album.id">
                        <a :href="'https://www.youtube.com/results?search_query=' + encodeURIComponent(album.artist + ' ' + album.name)" target="_blank" class="group cursor-pointer flex gap-5 bg-white/80 border border-purple-100 rounded-2xl shadow-sm p-4 hover:bg-white hover:shadow-md transition-all duration-300">
                            <div class="relative overflow-hidden rounded-xl shadow-lg flex-shrink-0 w-32 h-32">
                                <img :src="album.image || 'https://picsum.photos/seed/' + album.id + '/400/400'" :alt="album.name"
                                     class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-all duration-300 flex items-center justify-center">
                                    <div class="w-10 h-10 bg-red-500 rounded-full flex items-center justify-center shadow-2xl opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-y-2 group-hover:translate-y-0">
                                        <i class="fa-solid fa-play text-white ml-1 text-sm"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0 flex flex-col justify-center">
                                <h3 class="font-bold text-gray-900 text-lg truncate group-hover:text-purple-600 transition-colors" x-text="album.name"></h3>
                                <p class="text-sm text-gray-500 mt-1" x-text="album.artist"></p>
                                <div class="mt-3 flex items-center justify-between">
                                    <span class="inline-flex items-center gap-1 text-xs text-red-500 font-medium">
                                        <i class="fa-brands fa-youtube"></i>
                                        Abrir en YouTube
                                    </span>
                                    <button @click.prevent="toggleLike(album, 'album')" class="text-gray-300 hover:text-pink-500 transition-colors opacity-0 group-hover:opacity-100" :class="likesCache['album_'+album.id] ? '!text-pink-500 !opacity-100' : ''" title="Me Gusta">
                                        <i class="fa-solid fa-heart text-xl"></i>
                                    </button>
                                    </button>
                                </div>
                            </div>
                        </a>
                    </template>
                </div>
            </section>

            
            <section class="max-w-7xl mx-auto px-6 py-8">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-900">Joyas Ocultas</h2>
                        <p class="text-gray-500 mt-1">Singles que podrían convertirse en tus favoritos</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <template x-for="single in (dashboardData?.recommended_singles || [])" :key="'single-' + single.id">
                        <a :href="'https://www.youtube.com/results?search_query=' + encodeURIComponent(single.artist + ' ' + single.name)" target="_blank"
                           class="bg-white/80 border border-purple-100 rounded-2xl p-4 flex items-center gap-4 cursor-pointer hover:bg-white hover:shadow-md transition-all duration-300 group shadow-sm">
                            <div class="relative flex-shrink-0">
                                <img :src="single.image || 'https://picsum.photos/seed/' + single.id + '/300/300'" :alt="single.name" class="w-16 h-16 rounded-lg object-cover shadow-lg group-hover:shadow-purple-300 transition-shadow">
                                <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                    <div class="w-8 h-8 bg-red-500/90 rounded-full flex items-center justify-center">
                                        <i class="fa-solid fa-play text-white ml-0.5 text-xs"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-gray-900 text-sm truncate group-hover:text-purple-600 transition-colors" x-text="single.name"></p>
                                <p class="text-xs text-gray-500 mt-0.5" x-text="single.artist"></p>
                            </div>
                            <span class="inline-block px-2 py-0.5 text-[10px] font-bold rounded-full bg-purple-100 text-purple-700 uppercase tracking-wider">Single</span>
                        </a>
                    </template>
                </div>
            </section>
        </div>
    </div>

    
</div>

<script>
function onboardingFlow() {
    return {
        // --- Onboarding State ---
        step: 0,
        pageLoaded: false,
        isInterludeActive: false,
        interludeMessage: '',

        // Step 1: Genres
        genres: [],
        random25: [],
        selectedGenres: [],
        genreSearchQuery: '',
        isGenreSearchExpanded: false,
        gridConnections: [],
        currentGenrePage: 0,
        
        get maxGenrePage() {
            return Math.max(0, Math.ceil(this.genres.length / 25) - 1);
        },

        get filteredGenres() {
            let results = [];
            
            if (this.genreSearchQuery === '') {
                const start = this.currentGenrePage * 25;
                results = this.genres.slice(start, start + 25);
            } else {
                results = this.genres.filter(genre => 
                    genre.toLowerCase().includes(this.genreSearchQuery.toLowerCase())
                ).slice(0, 25);
            }
            
            // Ensure selected genres are always visible at the top, without duplicates
            const combined = [...this.selectedGenres];
            results.forEach(genre => {
                if (!combined.includes(genre)) {
                    combined.push(genre);
                }
            });
            
            return combined;
        },

        updateConnections() {
            this.$nextTick(() => {
                const container = this.$refs.genreGridContainer;
                if (!container) return;
                
                const rect = container.getBoundingClientRect();
                this.gridConnections = [];
                const points = [];
                
                // Get center points of selected DOM elements
                this.selectedGenres.forEach(g => {
                    const el = container.querySelector(`[data-genre="${g}"]`);
                    if (el && el.offsetParent !== null) { // exists and is visible
                        const elRect = el.getBoundingClientRect();
                        points.push({
                            x: elRect.left + (elRect.width / 2) - rect.left,
                            y: elRect.top + (elRect.height / 2) - rect.top,
                            genre: g
                        });
                    }
                });
                
                // Connect them sequentially
                for(let i = 0; i < points.length - 1; i++) {
                    this.gridConnections.push({
                        x1: points[i].x,
                        y1: points[i].y,
                        x2: points[i+1].x,
                        y2: points[i+1].y
                    });
                }
            });
        },

        // Step 2: Albums
        albumQuery: '',
        isAlbumSearchExpanded: false,
        albumResults: [],
        selectedAlbums: [],
        isSearchingAlbums: false,
        recommendedAlbumsByGenre: [],
        isFetchingRecommendations: false,

        // Step 3: Tracks
        trackQuery: '',
        trackResults: [],
        selectedTracks: [],
        isSearchingTracks: false,
        recommendedTracksByGenre: [],
        isFetchingTrackRecommendations: false,

        // Step 4: Dashboard
        isLoadingDashboard: false,
        dashboardData: null,

        // Player
        currentTrack: null,
        isPlaying: false,
        progress: 0,
        currentTime: '0:00',
        showAllPlaylist: false,
        progressInterval: null,
        likesCache: {},

        init() {
            this.loadGenres();
            setTimeout(() => {
                this.pageLoaded = true;
                this.step = 1;
            }, 100);
        },

        // --- Genres ---
        async loadGenres() {
            try {
                const res = await fetch('<?php echo e(url("discovery/genres")); ?>');
                const all = await res.json();
                this.genres = all.sort();
            } catch (e) {
                console.error('Failed to load genres:', e);
                const all = ['pop', 'rock', 'hip-hop', 'electronic', 'jazz', 'r-n-b', 'indie', 'latin', 'classical', 'metal', 'k-pop', 'country', 'blues', 'folk', 'reggae'];
                this.genres = all.sort();
            }
            // Listen for search input changes to update connections if elements move
            this.$watch('genreSearchQuery', () => {
                this.currentGenrePage = 0;
                this.updateConnections();
            });
            
            // Listen for window resize to fix lines
            window.addEventListener('resize', () => {
                if(this.step === 1) this.updateConnections();
            });
        },

        toggleGenre(genre) {
            const idx = this.selectedGenres.indexOf(genre);
            if (idx >= 0) {
                this.selectedGenres.splice(idx, 1);
            } else if (this.selectedGenres.length < 5) {
                this.selectedGenres.push(genre);
            }
            this.updateConnections();
        },

        // --- Albums ---
        async searchAlbums() {
            if (this.albumQuery.length < 2) {
                this.albumResults = [];
                return;
            }
            this.isSearchingAlbums = true;
            try {
                const res = await fetch(`<?php echo e(url('discovery/search')); ?>?q=${encodeURIComponent(this.albumQuery)}&type=album`);
                this.albumResults = await res.json();
            } catch (e) {
                console.error('Album search failed:', e);
            }
            this.isSearchingAlbums = false;
        },

        toggleAlbum(album) {
            const idx = this.selectedAlbums.findIndex(a => a.id === album.id);
            if (idx >= 0) {
                this.selectedAlbums.splice(idx, 1);
            } else if (this.selectedAlbums.length < 5) {
                this.selectedAlbums.push(album);
            }
        },

        // --- Tracks ---
        async searchTracks() {
            if (this.trackQuery.length < 2) {
                this.trackResults = [];
                return;
            }
            this.isSearchingTracks = true;
            try {
                const res = await fetch(`<?php echo e(url('discovery/search')); ?>?q=${encodeURIComponent(this.trackQuery)}&type=track`);
                this.trackResults = await res.json();
            } catch (e) {
                console.error('Track search failed:', e);
            }
            this.isSearchingTracks = false;
        },

        toggleTrack(track) {
            const idx = this.selectedTracks.findIndex(t => t.id === track.id);
            if (idx >= 0) {
                this.selectedTracks.splice(idx, 1);
            } else if (this.selectedTracks.length < 5) {
                this.selectedTracks.push(track);
            }
        },

        // --- Step transitions ---
        nextStep(targetStep, message) {
            this.interludeMessage = message;
            this.isInterludeActive = true;

            setTimeout(() => {
                this.step = targetStep;

                if (targetStep === 2) {
                    this.fetchAlbumRecommendationsByGenre();
                }

                if (targetStep === 3) {
                    this.fetchTrackRecommendationsByGenre();
                }

                if (targetStep === 4) {
                    this.fetchRecommendations();
                }

                setTimeout(() => {
                    this.isInterludeActive = false;
                }, 600);
            }, 2500);
        },

        goToStep(targetStep) {
            // Only allow navigation if target step is less than current step
            // or if it's currently allowed (already unlocked)
            if (targetStep < this.step) {
                this.step = targetStep;
                // Specifically for returning to step 1, re-trigger line connections
                if (targetStep === 1) {
                    this.$nextTick(() => {
                        this.updateConnections();
                    });
                }
            } else if (targetStep > this.step) {
                // If they are trying to click forward using the top bar, 
                // we'll rely on our existing nextStep logic if requirements are met
                if (this.step === 1 && this.selectedGenres.length >= 3) {
                    this.nextStep(2, 'Volviendo a Álbumes...');
                } else if (this.step === 2 && this.selectedAlbums.length >= 5) {
                    this.nextStep(3, 'Volviendo a Canciones...');
                }
            }
        },

        // --- AI Recommendations ---
        async fetchAlbumRecommendationsByGenre() {
            if (this.selectedGenres.length === 0) return;
            this.isFetchingRecommendations = true;
            try {
                // To fetch 5 albums based on genres, we can use the discovery API generate route or search based on genres.
                // Since we don't have a specific backend route just for genre->albums right now, 
                // we'll fetch from the existing endpoint by passing empty album/track arrays,
                // and extract the recommended items.
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                const res = await fetch('<?php echo e(url("discovery/generate")); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        genres: this.selectedGenres,
                        album_ids: [],
                        track_ids: [],
                    }),
                });
                const data = await res.json();
                
                // We'll map the recommended_albums from the payload to our top grid format
                if (data.recommended_albums && data.recommended_albums.length > 0) {
                    this.recommendedAlbumsByGenre = data.recommended_albums.slice(0, 4);
                } else if (data.weekly_playlist && data.weekly_playlist.length > 0) {
                    // Fallback to tracks converted to album format if no albums directly available
                    this.recommendedAlbumsByGenre = data.weekly_playlist.slice(0, 4).map(track => ({
                        id: track.id,
                        name: track.album || track.name,
                        artist: track.artist,
                        image: track.image
                    }));
                }
            } catch (e) {
                console.error('Failed to fetch genre-based album recommendations:', e);
            }
            this.isFetchingRecommendations = false;
        },

        async fetchTrackRecommendationsByGenre() {
            if (this.selectedGenres.length === 0 && this.selectedAlbums.length === 0) return;
            this.isFetchingTrackRecommendations = true;
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                const res = await fetch('<?php echo e(url("discovery/generate")); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        genres: this.selectedGenres,
                        album_ids: this.selectedAlbums.map(a => a.id),
                        track_ids: [],
                    }),
                });
                const data = await res.json();
                
                // Track recommendations from weekly playlist
                if (data.weekly_playlist && data.weekly_playlist.length > 0) {
                    this.recommendedTracksByGenre = data.weekly_playlist.slice(0, 8); // Top 8 suggestions
                }
            } catch (e) {
                console.error('Failed to fetch genre-based track recommendations:', e);
            }
            this.isFetchingTrackRecommendations = false;
        },

        async fetchRecommendations() {
            this.isLoadingDashboard = true;
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                const res = await fetch('<?php echo e(url("discovery/generate")); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        genres: this.selectedGenres,
                        album_ids: this.selectedAlbums.map(a => a.id),
                        track_ids: this.selectedTracks.map(t => t.id),
                    }),
                });
                this.dashboardData = await res.json();
            } catch (e) {
                console.error('Failed to fetch recommendations:', e);
                this.dashboardData = { weekly_playlist: [], recommended_albums: [], recommended_singles: [] };
            }
            this.isLoadingDashboard = false;
        },

        // --- Player ---
        playTrack(track, index) {
            this.currentTrack = track;
            this.isPlaying = true;
            this.progress = 0;
            this.currentTime = '0:00';
            this.simulateProgress();
        },

        // --- Likes & Export ---
        checkAuth() {
            if (!<?php echo e(Auth::check() ? 'true' : 'false'); ?>) {
                if (confirm('Debes iniciar sesión para usar los Me Gusta. ¿Quieres ir al login ahora?')) {
                    window.location.href = '<?php echo e(route("login")); ?>';
                }
                return false;
            }
            return true;
        },

        async toggleLike(item, type) {
            if (!this.checkAuth()) return;

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            if (!csrfToken) return;

            const cacheKey = type + '_' + item.id;
            const currentlyLiked = this.likesCache[cacheKey];

            // Optimistic update
            this.likesCache[cacheKey] = !currentlyLiked;

            try {
                const res = await fetch('<?php echo e(url("likes/toggle")); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        type: type,
                        spotify_id: item.id,
                        name: item.name,
                        artist_name: item.artist,
                        image_url: item.image || item.url || null
                    }),
                });
                const data = await res.json();
                if (data.status) {
                    this.likesCache[cacheKey] = data.liked;
                }
            } catch (e) {
                console.error('Failed to toggle like', e);
                // Revert on error
                this.likesCache[cacheKey] = currentlyLiked;
            }
        },

        async saveAllLikes() {
            if (!this.checkAuth()) return;

            const playlist = this.dashboardData?.weekly_playlist || [];
            if (playlist.length === 0) return;

            // Notify user it started
            alert('Guardando ' + playlist.length + ' canciones en tus Me Gusta...');

            let successCount = 0;
            for (const track of playlist) {
                // If not already liked in cache, send request
                const cacheKey = 'song_' + track.id;
                if (!this.likesCache[cacheKey]) {
                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                        const res = await fetch('<?php echo e(url("likes/toggle")); ?>', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                type: 'song',
                                spotify_id: track.id,
                                name: track.name,
                                artist_name: track.artist,
                                image_url: track.image
                            }),
                        });
                        const data = await res.json();
                        if (data.liked) {
                            this.likesCache[cacheKey] = true;
                            successCount++;
                        }
                    } catch (e) {
                        console.error('Failed to save track', track.name, e);
                    }
                }
            }
            alert(`¡Se guardaron las canciones correctamente en tus Me Gusta!`);
        },

        exportList(platform) {
            const playlist = this.dashboardData?.weekly_playlist || [];
            if (playlist.length === 0) return;

            let text = `Mi Mix Semanal - Exportado para ${platform}\n\n`;
            playlist.forEach((track, i) => {
                text += `${i + 1}. ${track.name} - ${track.artist}\n`;
            });
            text += `\nGenerado con Recicled App. ¡Busca estas canciones en ${platform}!`;

            navigator.clipboard.writeText(text).then(() => {
                alert(`¡Lista copiada al portapapeles! Puedes pegarla en cualquier herramienta para crear playlists en ${platform}.`);
            }).catch(err => {
                console.error('Error copying to clipboard', err);
                alert('No se pudo copiar al portapapeles.');
            });
        },

        togglePlay() {
            this.isPlaying = !this.isPlaying;
            if (this.isPlaying) {
                this.simulateProgress();
            } else {
                clearInterval(this.progressInterval);
            }
        },

        stopPlayer() {
            this.currentTrack = null;
            this.isPlaying = false;
            this.progress = 0;
            clearInterval(this.progressInterval);
        },

        nextTrackPlayer() {
            if (!this.currentTrack) return;
            const playlist = this.dashboardData?.weekly_playlist || [];
            const idx = playlist.findIndex(t => t.id === this.currentTrack.id);
            if (idx >= 0 && idx < playlist.length - 1) {
                this.playTrack(playlist[idx + 1], idx + 1);
            }
        },

        previousTrack() {
            if (!this.currentTrack) return;
            const playlist = this.dashboardData?.weekly_playlist || [];
            const idx = playlist.findIndex(t => t.id === this.currentTrack.id);
            if (idx > 0) {
                this.playTrack(playlist[idx - 1], idx - 1);
            }
        },

        simulateProgress() {
            clearInterval(this.progressInterval);
            this.progressInterval = setInterval(() => {
                if (this.progress < 100) {
                    this.progress += 0.5;
                    const totalSeconds = Math.floor(this.progress * 2.4);
                    const mins = Math.floor(totalSeconds / 60);
                    const secs = totalSeconds % 60;
                    this.currentTime = mins + ':' + String(secs).padStart(2, '0');
                } else {
                    this.nextTrackPlayer();
                }
            }, 1000);
        }
    };
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.discovery', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\RecicledApp\resources\views/discovery/onboarding.blade.php ENDPATH**/ ?>