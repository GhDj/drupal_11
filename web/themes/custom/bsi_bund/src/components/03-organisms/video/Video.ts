import Plyr from "plyr";

const lang = document.documentElement.lang;
let i18n = {
  restart: "Restart",
  rewind: "Rewind {seektime}s",
  play: "Play",
  pause: "Pause",
  fastForward: "Forward {seektime}s",
  seek: "Seek",
  seekLabel: "{currentTime} of {duration}",
  played: "Played",
  buffered: "Buffered",
  currentTime: "Current time",
  duration: "Duration",
  volume: "Volume",
  mute: "Mute",
  unmute: "Unmute",
  enableCaptions: "Enable captions",
  disableCaptions: "Disable captions",
  download: "Download",
  enterFullscreen: "Enter fullscreen",
  exitFullscreen: "Exit fullscreen",
  frameTitle: "Player for {title}",
  captions: "Captions",
  settings: "Settings",
  pip: "PIP",
  menuBack: "Go back to previous menu",
  speed: "Speed",
  normal: "Normal",
  quality: "Quality",
  loop: "Loop",
  start: "Start",
  end: "End",
  all: "All",
  reset: "Reset",
  disabled: "Disabled",
  enabled: "Enabled"
};

if (lang === "de") {
  i18n = {
    restart: "Neu starten",
    rewind: "{seektime}s zurückspulen",
    play: "Wiedergabe",
    pause: "Pause",
    fastForward: "{seektime}s vorspulen",
    seek: "Springen",
    seekLabel: "{currentTime} von {duration}",
    played: "Abgespielt",
    buffered: "Gepuffert",
    currentTime: "Aktuelle Zeit",
    duration: "Dauer",
    volume: "Lautstärke",
    mute: "Stummschalten",
    unmute: "Ton einschalten",
    enableCaptions: "Untertitel aktivieren",
    disableCaptions: "Untertitel deaktivieren",
    download: "Herunterladen",
    enterFullscreen: "Vollbildmodus aktivieren",
    exitFullscreen: "Vollbildmodus beenden",
    frameTitle: "Player für {title}",
    captions: "Untertitel",
    settings: "Einstellungen",
    pip: "Bild-in-Bild",
    menuBack: "Zum vorherigen Menü zurückkehren",
    speed: "Wiedergabegeschwindigkeit",
    normal: "Normal",
    quality: "Qualität",
    loop: "Wiederholen",
    start: "Anfang",
    end: "Ende",
    all: "Alle",
    reset: "Zurücksetzen",
    disabled: "Deaktiviert",
    enabled: "Aktiviert"
  };
}

export default function Video() {
  const videos = document.querySelectorAll("video");

  videos.forEach(video => {
    const iconPath = video.dataset.iconPath;
    // Setup the player
    new Plyr(video, {
      controls: [
        "play-large", // The large play button in the center
        "play", // Play/pause playback
        "progress", // The progress bar and scrubber for playback and buffering
        "current-time", // The current time of playback
        "duration", // The full duration of the media
        "mute", // Mute control
        "volume", // Volume control
        "settings", // Settings menu
        "fullscreen" // Toggle fullscreen
      ],

      tooltips: { controls: false },

      i18n: i18n,

      iconUrl: iconPath ?? "",

      iconPrefix: "player"
    });
  });
}
