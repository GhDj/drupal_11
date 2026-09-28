import autoprefixer from "autoprefixer";
import pxtorem from "postcss-pxtorem";

export default {
  plugins: [
    pxtorem({
      rootValue: 16, // base pixel size for 1rem
      propList: ["*"], // list props to convert; use e.g. ["font", "margin", "padding"] if you need finer control
      selectorBlackList: [], // add selectors here to skip conversion
      replace: true // set false to keep the px fallback alongside rem
    }),
    autoprefixer
  ]
};
