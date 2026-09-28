import Plyr from "plyr";

const lang = document.documentElement.lang;
let i18n = {
  rewind: "Rewind {seektime}s",
  play: "Play",
  pause: "Pause",
  fastForward: "Forward {seektime}s",
  seek: "Seek",
  buffered: "Buffered",
  currentTime: "Current time",
  duration: "Duration",
  volumeToggle: "Volume",
  volume: "Volume"
};

if (lang === "de") {
  i18n = {
    rewind: "{seektime}s zurückspulen",
    play: "Abspielen",
    pause: "Pause",
    fastForward: "{seektime}s vorspulen",
    seek: "Suche",
    buffered: "Buffered",
    currentTime: "aktuelle Zeit",
    duration: "Dauer",
    volumeToggle: "Lautstärke",
    volume: "Lautstärke"
  };
}

export default function Audio() {
  const audios = document.querySelectorAll("audio");

  audios.forEach(audio => {
    const iconPath = audio.dataset.iconPath;
    // Setup the player
    new Plyr(audio, {
      controls: [
        "play-large", // The large play button in the center
        "rewind",
        "play", // Play/pause playback
        "fast-forward",
        "progress", // The progress bar and scrubber for playback and buffering
        "current-time", // The current time of playback
        "duration", // The full duration of the media
        "mute", // Mute control
        "volume" // Volume control
      ],

      tooltips: { controls: false },

      i18n: i18n,

      iconUrl: iconPath ?? "",

      iconPrefix: "player"
    });
  });
}
