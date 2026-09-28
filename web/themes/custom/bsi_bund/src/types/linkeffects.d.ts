declare module "@init/linkeffects" {
  interface LinkEffectsOptions {
    moduleElementSelector: string;
    styledElementSelector: string;
    linkedElementSelector: string;
    targetLinkSelector: string;
  }

  export default class LinkEffects {
    constructor(options: LinkEffectsOptions);
  }
}
