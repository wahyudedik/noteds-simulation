/**
 * Simulation Show Page — Player Controls & Interactions
 *
 * Reads config from #player-wrapper data attributes:
 *   data-serve-url, data-play-url, data-simulation-id,
 *   data-comments-url, data-favorites-url, data-bookmarks-url,
 *   data-reactions-url, data-ratings-url, data-collections-url
 */

// ─── Player State ──────────────────────────────────────────────────────────────

var isPlaying = false;
var isFullscreen = false;
var isSticky = false;
var stickyThreshold = 0;
var scrollTicking = false;

function getPlayerConfig() {
    var wrapper = document.getElementById('player-wrapper');
    return {
        serveUrl: wrapper.dataset.serveUrl,
        playUrl: wrapper.dataset.playUrl,
        simulationId: wrapper.dataset.simulationId,
        commentsUrl: wrapper.dataset.commentsUrl,
        favoritesUrl: wrapper.dataset.favoritesUrl,
        bookmarksUrl: wrapper.dataset.bookmarksUrl,
        shareUrl: wrapper.dataset.shareUrl,
        reactionsUrl: wrapper.dataset.reactionsUrl,
        ratingsUrl: wrapper.dataset.ratingsUrl,
        collectionsUrl: wrapper.dataset.collectionsUrl,
    };
}

// ─── Player Controls ──────────────────────────────────────────────────────────

function playSimulation() {
    if (isPlaying) return;
    isPlaying = true;

    var poster = document.getElementById('player-poster');
    var container = document.getElementById('player-iframe-container');
    var controls = document.getElementById('player-controls');
    var iframe = document.getElementById('simulation-iframe');
    var config = getPlayerConfig();

    poster.classList.add('hidden');
    container.classList.remove('hidden');
    controls.classList.remove('hidden');
    iframe.src = config.serveUrl;
    updateStickyThreshold();

    fetch(config.playUrl, {
        method: 'GET',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    }).then(function (r) {
        if (!r.ok || !(r.headers.get('content-type') || '').includes('application/json')) return null;
        return r.json();
    }).catch(function () { });
}

function closeSimulation() {
    isPlaying = false;
    exitFullscreen();
    exitSticky();

    var container = document.getElementById('player-iframe-container');
    var controls = document.getElementById('player-controls');
    var poster = document.getElementById('player-poster');
    var iframe = document.getElementById('simulation-iframe');

    container.classList.add('hidden');
    controls.classList.add('hidden');
    poster.classList.remove('hidden');
    iframe.src = '';
}

function reloadSimulation() {
    var iframe = document.getElementById('simulation-iframe');
    if (iframe.src) { iframe.src = iframe.src; }
}

function toggleFullscreen() {
    isFullscreen ? exitFullscreen() : enterFullscreen();
}

function enterFullscreen() {
    isFullscreen = true;
    var playerWrapper = document.getElementById('player-wrapper');
    document.body.classList.add('fullscreen-mode');
    playerWrapper.classList.add('player-fullscreen');
    document.getElementById('icon-fullscreen-enter').classList.add('hidden');
    document.getElementById('icon-fullscreen-exit').classList.remove('hidden');
}

function exitFullscreen() {
    isFullscreen = false;
    var playerWrapper = document.getElementById('player-wrapper');
    document.body.classList.remove('fullscreen-mode');
    playerWrapper.classList.remove('player-fullscreen');
    document.getElementById('icon-fullscreen-enter').classList.remove('hidden');
    document.getElementById('icon-fullscreen-exit').classList.add('hidden');
}

function updateStickyThreshold() {
    var playerWrapper = document.getElementById('player-wrapper');
    var rect = playerWrapper.getBoundingClientRect();
    stickyThreshold = window.scrollY + rect.top + rect.height;
}

function enterSticky() {
    isSticky = true;
    var playerWrapper = document.getElementById('player-wrapper');
    playerWrapper.classList.add('player-sticky-active');
    playerWrapper.style.maxWidth = playerWrapper.parentElement.offsetWidth + 'px';
}

function exitSticky() {
    isSticky = false;
    var playerWrapper = document.getElementById('player-wrapper');
    playerWrapper.classList.remove('player-sticky-active');
    playerWrapper.style.maxWidth = '';
}

// ─── Scroll & Resize Listeners ────────────────────────────────────────────────

window.addEventListener('scroll', function () {
    if (!scrollTicking) {
        window.requestAnimationFrame(function () {
            if (isPlaying && !isFullscreen) {
                var scrollPos = window.scrollY;
                if (scrollPos > stickyThreshold && !isSticky) { enterSticky(); }
                else if (scrollPos <= stickyThreshold && isSticky) { exitSticky(); }
            }
            scrollTicking = false;
        });
        scrollTicking = true;
    }
});

window.addEventListener('resize', function () {
    if (isPlaying) {
        var playerWrapper = document.getElementById('player-wrapper');
        if (isSticky) { playerWrapper.style.maxWidth = ''; }
        updateStickyThreshold();
        if (isSticky) { playerWrapper.style.maxWidth = playerWrapper.parentElement.offsetWidth + 'px'; }
    }
});

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && isFullscreen) { exitFullscreen(); }
});

// ─── Copy Link ────────────────────────────────────────────────────────────────

function copyLink() {
    var url = window.location.href;
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(url).then(function () {
            showToast('Link berhasil disalin!');
        });
    } else {
        var textarea = document.createElement('textarea');
        textarea.value = url;
        textarea.style.position = 'fixed';
        textarea.style.opacity = '0';
        document.body.appendChild(textarea);
        textarea.select();
        try {
            document.execCommand('copy');
            showToast('Link berhasil disalin!');
        } catch (err) {
            showToast('Gagal menyalin link');
        }
        document.body.removeChild(textarea);
    }
}

// ─── Favorite ─────────────────────────────────────────────────────────────────

function toggleFavorite() {
    var config = getPlayerConfig();
    ajaxPost(config.favoritesUrl, {}, function (result) {
        if (!result) return;
        var btn = document.getElementById('favorite-btn');
        var countEl = document.getElementById('favorite-count');
        var svg = btn.querySelector('svg');
        if (result.favorited) {
            btn.classList.add('text-red-500');
            svg.setAttribute('fill', 'currentColor');
        } else {
            btn.classList.remove('text-red-500');
            svg.setAttribute('fill', 'none');
        }
        countEl.textContent = '(' + (result.favorite_count || 0) + ')';
    });
}

// ─── Share Tracking ───────────────────────────────────────────────────────────

function trackShare(platform) {
    var config = getPlayerConfig();
    ajaxPost(config.shareUrl, { platform: platform }, function () { });
}

// ─── Bookmark ─────────────────────────────────────────────────────────────────

function toggleBookmark() {
    var config = getPlayerConfig();
    ajaxPost(config.bookmarksUrl, { simulation_id: config.simulationId }, function (result) {
        if (!result) return;
        var btn = document.getElementById('bookmark-btn');
        var text = document.getElementById('bookmark-text');
        if (result.bookmarked) {
            btn.classList.add('active');
            btn.querySelector('svg').setAttribute('fill', 'currentColor');
            text.textContent = 'Tersimpan';
        } else {
            btn.classList.remove('active');
            btn.querySelector('svg').setAttribute('fill', 'none');
            text.textContent = 'Bookmark';
        }
    });
}

// ─── Add to Collection ────────────────────────────────────────────────────────

function addToCollection(collectionId) {
    var config = getPlayerConfig();
    ajaxPost(config.collectionsUrl, {
        collection_id: collectionId,
        simulation_id: config.simulationId
    }, function (result) {
        if (!result) return;
        if (result.success) {
            setTimeout(function () { window.location.reload(); }, 500);
        }
    });
}

// ─── Reactions ────────────────────────────────────────────────────────────────

function toggleReaction(type) {
    var config = getPlayerConfig();
    ajaxPost(config.reactionsUrl, { simulation_id: config.simulationId, type: type }, function (result) {
        if (!result) return;
        var btn = document.getElementById('reaction-' + type);
        var countEl = document.getElementById('reaction-count-' + type);
        if (result.active) {
            btn.classList.add('active');
        } else {
            btn.classList.remove('active');
        }
        countEl.textContent = '(' + (result.count || 0) + ')';
    });
}

// ─── Rating ───────────────────────────────────────────────────────────────────

function setRating(value) {
    var config = getPlayerConfig();
    ajaxPost(config.ratingsUrl, { simulation_id: config.simulationId, rating: value }, function (result) {
        if (!result) return;
        var stars = document.querySelectorAll('#rating-stars .rating-star');
        stars.forEach(function (star, index) {
            if (index < value) {
                star.classList.add('active', 'text-yellow-400');
                star.classList.remove('text-gray-300');
            } else {
                star.classList.remove('active', 'text-yellow-400');
                star.classList.add('text-gray-300');
            }
        });
        document.getElementById('rating-text').textContent = value + '/5';
    });
}

// ─── Follow ───────────────────────────────────────────────────────────────────

function toggleFollow(username) {
    ajaxPost('/follows/' + username + '/toggle', {}, function (result) {
        if (!result) return;
        var btn = document.getElementById('follow-btn');
        var text = document.getElementById('follow-text');
        if (result.following) {
            btn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
            btn.classList.add('bg-gray-200', 'hover:bg-gray-300', 'text-gray-700');
            text.textContent = 'Mengikuti';
        } else {
            btn.classList.remove('bg-gray-200', 'hover:bg-gray-300', 'text-gray-700');
            btn.classList.add('bg-blue-600', 'hover:bg-blue-700');
            text.textContent = 'Ikuti';
        }
    });
}

// ─── Comments ─────────────────────────────────────────────────────────────────

function postComment(parentId) {
    var config = getPlayerConfig();
    var inputId = parentId ? 'reply-input-' + parentId : 'comment-input';
    var input = document.getElementById(inputId);
    var content = input.value.trim();
    if (!content) return;

    var isMainComment = !parentId;
    if (isMainComment) {
        var submitBtn = document.getElementById('comment-submit-btn');
        var submitText = document.getElementById('comment-submit-text');
        var submitSpinner = document.getElementById('comment-submit-spinner');
        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-70', 'cursor-not-allowed');
        submitText.textContent = 'Mengirim...';
        submitSpinner.classList.remove('hidden');
    } else {
        var replyForm = document.getElementById('reply-form-' + parentId);
        if (replyForm && replyForm.__x) {
            replyForm.__x.$data.submitting = true;
        } else if (replyForm) {
            input.disabled = true;
            var replyBtn = replyForm.querySelector('button');
            if (replyBtn) {
                replyBtn.disabled = true;
                replyBtn.innerHTML = '<span class="flex items-center gap-1"><svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"></circle><path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" class="opacity-75"></path></svg> Mengirim...</span>';
            }
        }
    }

    var data = { simulation_id: config.simulationId, body: content };
    if (parentId) { data.parent_id = parentId; }

    ajaxPost(config.commentsUrl, data, function (result) {
        if (isMainComment) {
            var submitBtn = document.getElementById('comment-submit-btn');
            var submitText = document.getElementById('comment-submit-text');
            var submitSpinner = document.getElementById('comment-submit-spinner');
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
            submitText.textContent = 'Kirim';
            submitSpinner.classList.add('hidden');
        } else {
            var replyForm = document.getElementById('reply-form-' + parentId);
            if (replyForm && replyForm.__x) {
                replyForm.__x.$data.submitting = false;
            } else if (replyForm) {
                input.disabled = false;
                var replyBtn = replyForm.querySelector('button');
                if (replyBtn) {
                    replyBtn.disabled = false;
                    replyBtn.innerHTML = 'Kirim';
                }
            }
        }
        if (!result) return;
        if (result.success) {
            input.value = '';
            window.location.reload();
        }
    });
}

function deleteComment(commentId) {
    showConfirm('Hapus komentar ini?').then(function (confirmed) {
        if (!confirmed) return;
        var config = getPlayerConfig();
        var url = config.commentsUrl.replace('/store', '') + '/' + commentId;
        ajaxPost(url, { _method: 'DELETE' }, function (result) {
            if (!result) return;
            if (result.success) {
                window.location.reload();
            }
        });
    });
}

function toggleReplyForm(commentId) {
    var form = document.getElementById('reply-form-' + commentId);
    form.classList.toggle('show');
}
