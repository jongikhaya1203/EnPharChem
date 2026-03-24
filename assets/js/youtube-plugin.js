/**
 * EnPharChem YouTube Plugin v2
 * Uses Piped API (open-source YouTube proxy) for guaranteed playback
 * Falls back through multiple embed methods
 */

var EPYouTube = {
    searchCache: {},
    pipedInstances: [
        'https://pipedapi.kavin.rocks',
        'https://api.piped.privacydevs.net',
        'https://pipedapi.in.projectsegfau.lt'
    ],
    pipedFrontends: [
        'https://piped.video',
        'https://piped.privacydevs.net'
    ],

    search: function(query, callback, maxResults) {
        maxResults = maxResults || 3;
        var cacheKey = query + '_' + maxResults;
        if (this.searchCache[cacheKey]) { callback(this.searchCache[cacheKey]); return; }
        var self = this;

        var tryApi = function(idx) {
            if (idx >= self.pipedInstances.length) {
                callback(null);
                return;
            }
            var url = self.pipedInstances[idx] + '/search?q=' + encodeURIComponent(query) + '&filter=videos';
            fetch(url, { signal: AbortSignal.timeout(6000) })
                .then(function(r) { if (!r.ok) throw new Error(); return r.json(); })
                .then(function(data) {
                    var items = (data.items || data).filter(function(v) { return v.url || v.videoId; });
                    if (items.length > 0) {
                        var videos = items.slice(0, maxResults).map(function(v) {
                            var vid = v.url ? v.url.replace('/watch?v=', '') : v.videoId;
                            return {
                                id: vid,
                                title: v.title || query,
                                duration: v.duration || 0,
                                thumbnail: v.thumbnail || ('https://img.youtube.com/vi/' + vid + '/mqdefault.jpg'),
                                channel: v.uploaderName || v.author || 'YouTube'
                            };
                        });
                        self.searchCache[cacheKey] = videos;
                        callback(videos);
                    } else { tryApi(idx + 1); }
                })
                .catch(function() { tryApi(idx + 1); });
        };
        tryApi(0);
    },

    /**
     * Embed a video - tries Piped embed first (no restrictions), then YouTube
     */
    embedVideo: function(containerId, videoId) {
        var container = document.getElementById(containerId);
        if (!container) return;
        var pipedFrontend = this.pipedFrontends[0];

        // Piped embed works for ALL videos (no embed restrictions)
        container.innerHTML =
            '<iframe width="100%" height="100%" ' +
            'src="' + pipedFrontend + '/embed/' + videoId + '?autoplay=1" ' +
            'frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share; fullscreen" ' +
            'allowfullscreen style="aspect-ratio:16/9;border:none;"></iframe>';
    },

    /**
     * Build full lesson widget: main player + 3 related videos
     */
    buildLessonWidget: function(containerId, searchQuery, options) {
        options = options || {};
        var self = this;
        var container = document.getElementById(containerId);
        if (!container) return;
        var lessonTitle = options.title || searchQuery;

        // Loading state
        container.innerHTML =
            '<div style="aspect-ratio:16/9;background:#000;display:flex;align-items:center;justify-content:center;">' +
            '<div class="text-center"><div class="ep-yt-spinner"></div>' +
            '<p style="color:#fff;font-size:13px;margin-top:12px;">Finding videos...</p></div></div>';

        this.search(searchQuery, function(videos) {
            if (!videos || videos.length === 0) {
                self._renderFallback(container, searchQuery, lessonTitle);
                return;
            }

            var mainVideo = videos[0];
            var html = '';

            // Main player
            html += '<div id="' + containerId + '-player" style="aspect-ratio:16/9;background:#000;"></div>';

            // Video info
            html += '<div style="padding:12px 16px;background:#161b22;border-top:1px solid rgba(255,255,255,.06);">';
            html += '<div style="font-weight:600;color:#fff;font-size:14px;" id="' + containerId + '-title">' + self._esc(mainVideo.title) + '</div>';
            html += '<div style="color:#8b949e;font-size:12px;">' + self._esc(mainVideo.channel) + (mainVideo.duration ? ' &bull; ' + self._dur(mainVideo.duration) : '') + '</div>';
            html += '</div>';

            // Related videos (max 3)
            if (videos.length > 0) {
                html += '<div style="padding:12px 16px;border-top:1px solid rgba(255,255,255,.06);background:#0d1117;">';
                html += '<div style="font-size:12px;color:#8b949e;font-weight:600;margin-bottom:10px;"><i class="fab fa-youtube" style="color:#dc3545;margin-right:6px;"></i>Videos (' + videos.length + ')</div>';
                html += '<div class="row g-2">';
                videos.forEach(function(v, i) {
                    html += '<div class="col-md-4">';
                    html += '<div class="ep-yt-card" onclick="EPYouTube.switchVideo(\'' + containerId + '\',\'' + v.id + '\',\'' + self._escA(v.title) + '\')" style="background:#161b22;border:1px solid rgba(255,255,255,.06);border-radius:8px;overflow:hidden;cursor:pointer;transition:all .2s;" onmouseover="this.style.borderColor=\'#dc3545\';this.style.transform=\'translateY(-2px)\'" onmouseout="this.style.borderColor=\'rgba(255,255,255,.06)\';this.style.transform=\'none\'">';
                    html += '<div style="position:relative;"><img src="' + v.thumbnail + '" style="width:100%;aspect-ratio:16/9;object-fit:cover;display:block;' + (i === 0 ? 'border:2px solid #dc3545;border-radius:6px 6px 0 0;' : 'opacity:.8;') + '">';
                    html += '<div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;"><div style="width:36px;height:36px;border-radius:50%;background:rgba(220,53,69,.9);display:flex;align-items:center;justify-content:center;"><i class="fas fa-play" style="color:#fff;font-size:12px;margin-left:2px;"></i></div></div>';
                    if (v.duration) html += '<span style="position:absolute;bottom:4px;right:4px;background:rgba(0,0,0,.85);color:#fff;font-size:10px;padding:1px 6px;border-radius:3px;">' + self._dur(v.duration) + '</span>';
                    html += '</div>';
                    html += '<div style="padding:8px;"><div style="color:#e6edf3;font-size:11px;font-weight:600;line-height:1.3;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">' + self._esc(v.title) + '</div>';
                    html += '<div style="color:#8b949e;font-size:10px;margin-top:2px;">' + self._esc(v.channel) + '</div></div>';
                    html += '</div></div>';
                });
                html += '</div></div>';
            }

            // Actions
            html += '<div style="padding:10px 16px;border-top:1px solid rgba(255,255,255,.06);background:#0d1117;display:flex;gap:8px;flex-wrap:wrap;align-items:center;">';
            html += '<a href="https://www.youtube.com/watch?v=' + mainVideo.id + '" target="_blank" class="btn btn-sm btn-outline-danger"><i class="fab fa-youtube me-1"></i>YouTube</a>';
            html += '<a href="https://www.youtube.com/results?search_query=' + encodeURIComponent(searchQuery) + '" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="fas fa-search me-1"></i>More</a>';
            html += '<button class="btn btn-sm btn-outline-success ms-auto" onclick="markComplete(\'' + containerId + '\')"><i class="fas fa-check me-1"></i>Complete</button>';
            html += '</div>';

            container.innerHTML = html;
            self.embedVideo(containerId + '-player', mainVideo.id);
        }, 3);
    },

    switchVideo: function(containerId, videoId, title) {
        var titleEl = document.getElementById(containerId + '-title');
        if (titleEl && title) titleEl.textContent = decodeURIComponent(title);
        this.embedVideo(containerId + '-player', videoId);
    },

    _renderFallback: function(container, query, title) {
        container.innerHTML =
            '<div style="aspect-ratio:16/9;background:linear-gradient(135deg,#0a1628,#1a1d23);display:flex;align-items:center;justify-content:center;">' +
            '<div class="text-center p-4">' +
            '<i class="fab fa-youtube" style="font-size:4rem;color:#dc3545;margin-bottom:16px;display:block;"></i>' +
            '<p style="color:#fff;font-weight:700;font-size:16px;">' + this._esc(title) + '</p>' +
            '<p style="color:#8b949e;font-size:13px;margin-bottom:20px;">Click to watch on YouTube</p>' +
            '<a href="https://www.youtube.com/results?search_query=' + encodeURIComponent(query) + '" target="_blank" class="btn btn-danger"><i class="fab fa-youtube me-2"></i>Watch on YouTube</a>' +
            '</div></div>';
    },

    _dur: function(s) { if (!s) return ''; var m = Math.floor(s/60); return m + ':' + (s%60 < 10 ? '0' : '') + (s%60); },
    _esc: function(s) { if (!s) return ''; var d = document.createElement('div'); d.textContent = s; return d.innerHTML; },
    _escA: function(s) { return encodeURIComponent(s || ''); }
};

// Plugin CSS
(function() {
    var s = document.createElement('style');
    s.textContent = '.ep-yt-spinner{width:40px;height:40px;border:3px solid rgba(255,255,255,.15);border-top-color:#dc3545;border-radius:50%;animation:ep-yt-spin .8s linear infinite;margin:0 auto}@keyframes ep-yt-spin{to{transform:rotate(360deg)}}.ep-yt-card:hover img{opacity:1!important}';
    document.head.appendChild(s);
})();
