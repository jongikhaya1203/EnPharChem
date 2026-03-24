/**
 * EnPharChem YouTube Plugin
 * Built-in YouTube video player for training modules
 * Uses YouTube IFrame Player API for full embedded playback
 */

var EPYouTube = {
    apiReady: false,
    players: {},
    apiKey: '', // Works without API key using IFrame API
    searchCache: {},

    /**
     * Initialize the YouTube IFrame API
     */
    init: function() {
        if (document.getElementById('yt-api-script')) return;
        var tag = document.createElement('script');
        tag.id = 'yt-api-script';
        tag.src = 'https://www.youtube.com/iframe_api';
        document.head.appendChild(tag);
    },

    /**
     * Search YouTube and return video IDs via multiple fallback methods
     */
    search: function(query, callback, maxResults) {
        maxResults = maxResults || 3;
        var cacheKey = query + '_' + maxResults;

        if (this.searchCache[cacheKey]) {
            callback(this.searchCache[cacheKey]);
            return;
        }

        var self = this;

        // Try Invidious instances in order (public, no API key)
        var instances = [
            'https://vid.puffyan.us',
            'https://invidious.snopyta.org',
            'https://y.com.sb',
            'https://invidious.kavin.rocks'
        ];

        var tryInstance = function(idx) {
            if (idx >= instances.length) {
                // All instances failed - use hardcoded fallback videos
                callback(self.getFallbackVideos(query, maxResults));
                return;
            }

            var url = instances[idx] + '/api/v1/search?q=' + encodeURIComponent(query) + '&type=video&sort_by=relevance&page=1';

            fetch(url, { signal: AbortSignal.timeout(5000) })
                .then(function(r) {
                    if (!r.ok) throw new Error('HTTP ' + r.status);
                    return r.json();
                })
                .then(function(data) {
                    if (data && data.length > 0) {
                        var videos = data.slice(0, maxResults).map(function(v) {
                            return {
                                id: v.videoId,
                                title: v.title || query,
                                duration: v.lengthSeconds || 0,
                                thumbnail: 'https://img.youtube.com/vi/' + v.videoId + '/mqdefault.jpg',
                                channel: v.author || 'YouTube'
                            };
                        });
                        self.searchCache[cacheKey] = videos;
                        callback(videos);
                    } else {
                        tryInstance(idx + 1);
                    }
                })
                .catch(function() {
                    tryInstance(idx + 1);
                });
        };

        tryInstance(0);
    },

    /**
     * Fallback curated engineering videos per category keyword
     */
    getFallbackVideos: function(query, maxResults) {
        var q = query.toLowerCase();
        var videos = [];

        // Curated playlists that are known to work (large educational channels)
        var fallbackSets = {
            'hysys': [
                {id: 'PLkKMYm_kNVO_GP_-SIMK-PFqdNONznRok', title: 'HYSYS Process Simulation Course'},
            ],
            'chemical': [
                {id: 'PLkKMYm_kNVO_GP_-SIMK-PFqdNONznRok', title: 'Chemical Engineering Simulation'},
            ],
            'heat exchanger': [
                {id: 'PLZHQObOWTQDPD3MizzM2xVFitgF8hE_ab', title: 'Heat Transfer Fundamentals'},
            ],
            'default': [
                {id: 'PLZHQObOWTQDPD3MizzM2xVFitgF8hE_ab', title: 'Engineering Fundamentals'},
            ]
        };

        // Match by keyword
        var matched = fallbackSets['default'];
        for (var key in fallbackSets) {
            if (q.indexOf(key) !== -1) {
                matched = fallbackSets[key];
                break;
            }
        }

        return matched.slice(0, maxResults);
    },

    /**
     * Create an embedded YouTube player in a container
     */
    createPlayer: function(containerId, videoId, options) {
        options = options || {};
        var self = this;
        var container = document.getElementById(containerId);
        if (!container) return;

        // Build player HTML
        container.innerHTML = '';
        var playerDiv = document.createElement('div');
        playerDiv.id = containerId + '-yt-player';
        container.appendChild(playerDiv);

        if (this.apiReady) {
            this._initYTPlayer(playerDiv.id, videoId, options);
        } else {
            // Use direct iframe if API not ready
            container.innerHTML = '<iframe id="' + containerId + '-iframe" width="100%" height="100%" ' +
                'src="https://www.youtube-nocookie.com/embed/' + videoId + '?autoplay=1&rel=0&modestbranding=1&enablejsapi=1" ' +
                'frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" ' +
                'allowfullscreen style="aspect-ratio:16/9;border:none;"></iframe>';
        }
    },

    _initYTPlayer: function(elementId, videoId, options) {
        var self = this;
        try {
            var player = new YT.Player(elementId, {
                videoId: videoId,
                playerVars: {
                    autoplay: options.autoplay !== false ? 1 : 0,
                    rel: 0,
                    modestbranding: 1,
                    fs: 1,
                    cc_load_policy: 0,
                    origin: window.location.origin
                },
                events: {
                    onReady: function(event) {
                        if (options.onReady) options.onReady(event);
                    },
                    onStateChange: function(event) {
                        if (options.onStateChange) options.onStateChange(event);
                    },
                    onError: function(event) {
                        // On error, try fallback
                        if (options.onError) options.onError(event);
                    }
                }
            });
            self.players[elementId] = player;
        } catch(e) {
            // Fallback to iframe
            var el = document.getElementById(elementId);
            if (el) {
                el.outerHTML = '<iframe width="100%" height="100%" ' +
                    'src="https://www.youtube-nocookie.com/embed/' + videoId + '?autoplay=1&rel=0" ' +
                    'frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" ' +
                    'allowfullscreen style="aspect-ratio:16/9;border:none;"></iframe>';
            }
        }
    },

    /**
     * Build a complete video lesson widget with search, player, and related videos
     */
    buildLessonWidget: function(containerId, searchQuery, options) {
        options = options || {};
        var self = this;
        var container = document.getElementById(containerId);
        if (!container) return;

        var lessonTitle = options.title || searchQuery;
        var duration = options.duration || 30;

        // Show loading state
        container.innerHTML =
            '<div class="ep-yt-loading" style="aspect-ratio:16/9;background:#000;display:flex;align-items:center;justify-content:center;">' +
                '<div class="text-center">' +
                    '<div class="ep-yt-spinner"></div>' +
                    '<p style="color:#fff;font-size:13px;margin-top:12px;">Finding videos for: <em>' + lessonTitle.substring(0, 50) + '</em></p>' +
                '</div>' +
            '</div>';

        // Search for videos
        this.search(searchQuery, function(videos) {
            if (!videos || videos.length === 0) {
                self._renderFallback(container, searchQuery, lessonTitle);
                return;
            }

            var mainVideo = videos[0];
            var relatedVideos = videos.slice(0, 3);

            // Build widget HTML
            var html = '';

            // Main player area
            html += '<div class="ep-yt-main">';
            html += '  <div id="' + containerId + '-player" style="aspect-ratio:16/9;background:#000;"></div>';
            html += '</div>';

            // Video info bar
            html += '<div class="ep-yt-info" style="padding:12px 16px;background:#161b22;border-top:1px solid rgba(255,255,255,.06);">';
            html += '  <div style="font-weight:600;color:#fff;font-size:14px;margin-bottom:2px;" id="' + containerId + '-title">' + self._escHtml(mainVideo.title) + '</div>';
            html += '  <div style="color:#8b949e;font-size:12px;">' + self._escHtml(mainVideo.channel) + ' &bull; ' + self._formatDuration(mainVideo.duration) + '</div>';
            html += '</div>';

            // Related videos (max 3)
            if (relatedVideos.length > 1) {
                html += '<div style="padding:12px 16px;border-top:1px solid rgba(255,255,255,.06);background:#0d1117;">';
                html += '  <div style="font-size:12px;color:#8b949e;font-weight:600;margin-bottom:10px;"><i class="fab fa-youtube" style="color:#dc3545;margin-right:6px;"></i>Related Videos (' + relatedVideos.length + ')</div>';
                html += '  <div class="row g-2">';

                relatedVideos.forEach(function(v, i) {
                    html += '<div class="col-md-4">';
                    html += '  <div class="ep-yt-related-card" onclick="EPYouTube.switchVideo(\'' + containerId + '\', \'' + v.id + '\', \'' + self._escAttr(v.title) + '\', \'' + self._escAttr(v.channel) + '\')" style="background:#161b22;border:1px solid rgba(255,255,255,.06);border-radius:8px;overflow:hidden;cursor:pointer;transition:border-color .2s;" onmouseover="this.style.borderColor=\'#dc3545\'" onmouseout="this.style.borderColor=\'rgba(255,255,255,.06)\'">';
                    html += '    <div style="position:relative;">';
                    html += '      <img src="' + v.thumbnail + '" style="width:100%;aspect-ratio:16/9;object-fit:cover;display:block;opacity:.85;">';
                    html += '      <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;">';
                    html += '        <div style="width:36px;height:36px;border-radius:50%;background:rgba(220,53,69,.9);display:flex;align-items:center;justify-content:center;"><i class="fas fa-play" style="color:#fff;font-size:12px;margin-left:2px;"></i></div>';
                    html += '      </div>';
                    if (v.duration) {
                        html += '      <span style="position:absolute;bottom:4px;right:4px;background:rgba(0,0,0,.8);color:#fff;font-size:10px;padding:1px 5px;border-radius:3px;">' + self._formatDuration(v.duration) + '</span>';
                    }
                    html += '    </div>';
                    html += '    <div style="padding:8px;">';
                    html += '      <div style="color:#e6edf3;font-size:11px;font-weight:600;line-height:1.3;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">' + self._escHtml(v.title) + '</div>';
                    html += '      <div style="color:#8b949e;font-size:10px;margin-top:2px;">' + self._escHtml(v.channel) + '</div>';
                    html += '    </div>';
                    html += '  </div>';
                    html += '</div>';
                });

                html += '  </div>';
                html += '</div>';
            }

            // Action bar
            html += '<div style="padding:10px 16px;border-top:1px solid rgba(255,255,255,.06);background:#0d1117;display:flex;gap:8px;flex-wrap:wrap;align-items:center;">';
            html += '  <a href="https://www.youtube.com/watch?v=' + mainVideo.id + '" target="_blank" class="btn btn-sm btn-outline-danger"><i class="fab fa-youtube me-1"></i>Open on YouTube</a>';
            html += '  <a href="https://www.youtube.com/results?search_query=' + encodeURIComponent(searchQuery) + '" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="fas fa-search me-1"></i>More Videos</a>';
            html += '  <button class="btn btn-sm btn-outline-success ms-auto" onclick="markComplete(\'' + containerId + '\')"><i class="fas fa-check me-1"></i>Mark Complete</button>';
            html += '</div>';

            container.innerHTML = html;

            // Initialize the main player
            self.createPlayer(containerId + '-player', mainVideo.id, { autoplay: true });

        }, 3); // Max 3 results
    },

    /**
     * Switch the main player to a different video
     */
    switchVideo: function(containerId, videoId, title, channel) {
        var playerContainer = document.getElementById(containerId + '-player');
        var titleEl = document.getElementById(containerId + '-title');

        if (titleEl && title) titleEl.textContent = decodeURIComponent(title.replace(/\+/g, ' '));

        if (playerContainer) {
            playerContainer.innerHTML = '';
            this.createPlayer(containerId + '-player', videoId, { autoplay: true });
        }
    },

    _renderFallback: function(container, searchQuery, title) {
        container.innerHTML =
            '<div style="aspect-ratio:16/9;background:linear-gradient(135deg,#0a1628,#1a1d23);display:flex;align-items:center;justify-content:center;">' +
                '<div class="text-center p-4">' +
                    '<i class="fab fa-youtube" style="font-size:4rem;color:#dc3545;margin-bottom:16px;display:block;"></i>' +
                    '<p style="color:#fff;font-weight:700;font-size:16px;margin-bottom:4px;">' + this._escHtml(title) + '</p>' +
                    '<p style="color:#8b949e;font-size:13px;margin-bottom:20px;">Click below to watch on YouTube</p>' +
                    '<a href="https://www.youtube.com/results?search_query=' + encodeURIComponent(searchQuery) + '" target="_blank" class="btn btn-danger btn-lg"><i class="fab fa-youtube me-2"></i>Watch on YouTube</a>' +
                '</div>' +
            '</div>';
    },

    _formatDuration: function(seconds) {
        if (!seconds) return '';
        var m = Math.floor(seconds / 60);
        var s = seconds % 60;
        return m + ':' + (s < 10 ? '0' : '') + s;
    },

    _escHtml: function(str) {
        if (!str) return '';
        var div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    },

    _escAttr: function(str) {
        return encodeURIComponent(str || '');
    }
};

// YouTube IFrame API callback
function onYouTubeIframeAPIReady() {
    EPYouTube.apiReady = true;
}

// Auto-initialize
document.addEventListener('DOMContentLoaded', function() {
    EPYouTube.init();
});

// Plugin CSS
(function() {
    var style = document.createElement('style');
    style.textContent = '' +
        '.ep-yt-spinner { width:40px;height:40px;border:3px solid rgba(255,255,255,.15);border-top-color:#dc3545;border-radius:50%;animation:ep-yt-spin .8s linear infinite;margin:0 auto; }' +
        '@keyframes ep-yt-spin { to { transform:rotate(360deg); } }' +
        '.ep-yt-related-card:hover img { opacity:1 !important; }' +
        '.ep-yt-main iframe { display:block; }';
    document.head.appendChild(style);
})();
