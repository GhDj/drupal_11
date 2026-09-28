import "@molecules/facts/facts";
import "@molecules/60-sec/60-sec";
import "@molecules/dropdown/Dropdown";
import "@molecules/sidemenu/SideMenu";
import Audio from "@organisms/audio/Audio";
import "@organisms/hero-slider/hero-slider";
import "@organisms/image-slider/ImageSlider";
import "@organisms/opener/Opener";
import "@organisms/card-slider/CardSlider.ts";
import "@organisms/accordion/Accordion";
import "@organisms/anchors/Anchors";
import Header from "@organisms/header/Header";
import FormValidation from "@organisms/form/FormValidation";
import Video from "@organisms/video/Video";
import AutocompletePopover from "@molecules/searchmenu/AutocompletePopover";
import Timeline from "@organisms/timeline/Timeline";

document.addEventListener("DOMContentLoaded", () => {
  AutocompletePopover();
  Audio();
  Header();
  FormValidation();
  Video();
  Timeline();
});
