let currentTrack = 0;
let durations = [];
let totalDuration = 0;
let loaded = 0;
let isPlaying = false;
let lastVolume = 1;

const player = new Audio();
const playPauseBtn = document.getElementById('playPauseBtn');
const playicon = document.getElementById('playicon');
const pauseicon = document.getElementById('pauseicon');
const volumeSlider = document.getElementById('volumeSlider');
const volumeLabel = document.getElementById('volumeLabel');
const volumeupicon = document.getElementById('volumeupicon');
const volumedownicon = document.getElementById('volumedownicon');
const volumeofficon = document.getElementById('volumeofficon');
const progressBar = document.getElementById('progressBar');
const currentTimeSpan = document.getElementById('currentTime');
const totalDurationSpan = document.getElementById('totalDuration');
const audioList = document.getElementById('audioList');
const rewindBtn = document.getElementById('rewindBtn');
const forwardBtn = document.getElementById('forwardBtn');


// Maak de lijst van audio-bestanden als divs
audioFiles.forEach((file, idx) => {
    // Verwijder nummer aan het begin, .mp3 aan het eind, vervang _ door spatie
    let displayName = file
        .replace(/^\d+\s*-*\s*/g, '') // nummers en streepje/spatie aan begin
        .replace(/\.mp3$/i, '')      // .mp3 aan het eind
        .replace(/_/g, ' ');         // underscores naar spatie

    const div = document.createElement('div');
    div.textContent = displayName;
    div.className = "audio-item";
    div.style.cursor = "pointer";
    div.addEventListener('click', function() {
        currentTrack = idx;
        playTrack(idx);
        highlightCurrentTrack();
    });
    audioList.appendChild(div);
});

rewindBtn.addEventListener('click', function() {
    // 15 seconden terug, ook over vorige track heen
    let played = 0;
    for (let i = 0; i < currentTrack; i++) played += durations[i] || 0;
    let totalPlayed = played + player.currentTime;
    let newTime = Math.max(0, totalPlayed - 10);

    // Zoek juiste track en tijd
    let acc = 0, idx = 0;
    while (idx < durations.length && acc + durations[idx] < newTime) {
        acc += durations[idx];
        idx++;
    }
    currentTrack = idx;
    loadTrack(currentTrack);
    player.currentTime = newTime - acc;
    if (isPlaying) player.play();
    highlightCurrentTrack();
});

forwardBtn.addEventListener('click', function() {
    // 15 seconden vooruit, ook over volgende track heen
    let played = 0;
    for (let i = 0; i < currentTrack; i++) played += durations[i] || 0;
    let totalPlayed = played + player.currentTime;
    let newTime = Math.min(totalDuration, totalPlayed + 10);

    // Zoek juiste track en tijd
    let acc = 0, idx = 0;
    while (idx < durations.length && acc + durations[idx] < newTime) {
        acc += durations[idx];
        idx++;
    }
    currentTrack = idx;
    loadTrack(currentTrack);
    player.currentTime = newTime - acc;
    if (isPlaying) player.play();
    highlightCurrentTrack();
});

// Highlight de actieve track
function highlightCurrentTrack() {
    Array.from(audioList.children).forEach((el, idx) => {
        el.style.background = (idx === currentTrack) ? "var(--clr-searchbutton)" : "transparent";
    });
}

// Roep highlight aan bij trackwissel
const origPlayTrack = playTrack;
playTrack = function(idx) {
    origPlayTrack(idx);
    highlightCurrentTrack();
};

// Initieel highlighten na laden
document.addEventListener('DOMContentLoaded', highlightCurrentTrack);

// Tijd formatteren naar hh:mm:ss
function formatTime(seconds) {
    seconds = Math.floor(seconds);
    const h = Math.floor(seconds / 3600);
    const m = Math.floor((seconds % 3600) / 60);
    const s = seconds % 60;
    if (h > 0) {
        return (h < 10 ? "0" : "") + h + ":" +
               (m < 10 ? "0" : "") + m + ":" +
               (s < 10 ? "0" : "") + s;
    } else {
        return (m < 10 ? "0" : "") + m + ":" +
               (s < 10 ? "0" : "") + s;
    }
}

// Laad alle durations en bereken totalDuration
function getDurations(files, dir) {
    files.forEach((file, idx) => {
        const audio = new Audio(dir + file);
        audio.addEventListener('loadedmetadata', function() {
            durations[idx] = audio.duration;
            totalDuration += audio.duration;
            loaded++;
            if (loaded === files.length) {
                totalDurationSpan.textContent = formatTime(totalDuration);
                loadTrack(0);
            }
        });
    });
}

// Laad een track
function loadTrack(idx) {
    player.src = audioDir + audioFiles[idx];
    player.currentTime = 0;
    progressBar.value = 0;
    updateCurrentTime();
}

// Speel een track af
function playTrack(idx) {
    loadTrack(idx);
    player.play();
    isPlaying = true;
    playicon.style.display = 'none';
    pauseicon.style.display = 'inline';
}

// Play/pause knop
playPauseBtn.addEventListener('click', function() {
    if (!isPlaying) {
        player.play();
        isPlaying = true;
        playicon.style.display = 'none';
        pauseicon.style.display = 'inline';
    } else {
        player.pause();
        isPlaying = false;
        playicon.style.display = 'inline';
        pauseicon.style.display = 'none';
    }
});

function updateVolumeIcon(vol) {
    if (vol == 0) {
        volumeupicon.style.display = "none";
        volumedownicon.style.display = "none";
        volumeofficon.style.display = "";
    } else if (vol > 0 && vol <= 0.5) {
        volumeupicon.style.display = "none";
        volumedownicon.style.display = "";
        volumeofficon.style.display = "none";
    } else {
        volumeupicon.style.display = "";
        volumedownicon.style.display = "none";
        volumeofficon.style.display = "none";
    }
}

volumeSlider.addEventListener('input', function() {
    player.volume = parseFloat(this.value);
    updateVolumeIcon(player.volume);
    if (player.volume > 0) lastVolume = player.volume;
});

volumeLabel.addEventListener('click', function(e) {
    e.preventDefault();
    if (player.volume > 0) {
        lastVolume = player.volume;
        player.volume = 0;
        volumeSlider.value = 0;
    } else {
        player.volume = lastVolume || 1;
        volumeSlider.value = player.volume;
    }
    updateVolumeIcon(player.volume);
});

updateVolumeIcon(player.volume);

// Update voortgang en tijd
function updateCurrentTime() {
    let played = 0;
    for (let i = 0; i < currentTrack; i++) played += durations[i] || 0;
    let totalPlayed = played + player.currentTime;
    currentTimeSpan.textContent = formatTime(totalPlayed);
    // ProgressBar over alle tracks
    progressBar.value = ((totalPlayed / (totalDuration || 1)) * 500) || 0;
    progressBar.style.background = 'linear-gradient(to right, var(--clr-logodegrijzejager) ' + (progressBar.value / 500 * 100) + '%, var(--clr-text) ' + (progressBar.value / 500 * 100) + '%)';
}

// Bij tijdsupdate
player.addEventListener('timeupdate', updateCurrentTime);

// Voortgangsbalk aanpassen
progressBar.addEventListener('input', function() {
    if (totalDuration > 0) {
        const seekTo = (progressBar.value / 500) * totalDuration;
        // Zoek uit in welke track dit valt
        let acc = 0;
        let idx = 0;
        while (idx < durations.length && acc + durations[idx] < seekTo) {
            acc += durations[idx];
            idx++;
        }
        if (idx >= durations.length) idx = durations.length - 1;
        currentTrack = idx;
        loadTrack(currentTrack);
        player.currentTime = seekTo - acc;
        if (isPlaying) player.play();
        updateCurrentTime();
        highlightCurrentTrack(); // <-- voeg deze regel toe
    }
});

// Volgende track na einde
player.addEventListener('ended', function() {
    if (currentTrack < audioFiles.length - 1) {
        currentTrack++;
        playTrack(currentTrack);
    } else {
        isPlaying = false;
        playPauseBtn.textContent = "Play";
    }
});

// Start: laad alle durations
getDurations(audioFiles, audioDir);