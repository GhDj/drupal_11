import js from "@eslint/js";
import tseslint from "typescript-eslint";
import storybook from "eslint-plugin-storybook";
import eslintPluginPrettier from "eslint-plugin-prettier/recommended";
import react from "eslint-plugin-react";
import reactHooks from "eslint-plugin-react-hooks";
import { flat, flatCodeBlocks } from "eslint-plugin-mdx";

export default tseslint.config(
  // Ignore patterns (must be first)
  {
    ignores: ["**/node_modules/**", "**/dist/**", "**/storybook-static/**"]
  },

  // Base configs for JS/TS files only
  {
    files: ["**/*.{js,ts,jsx,tsx}"],
    extends: [
      js.configs.recommended,
      ...tseslint.configs.recommendedTypeChecked,
      ...tseslint.configs.stylisticTypeChecked
    ],
    languageOptions: {
      ecmaVersion: "latest",
      sourceType: "module",
      parserOptions: {
        projectService: true,
        tsconfigRootDir: import.meta.dirname
      }
    }
  },

  // React and TSX config
  {
    files: ["**/*.tsx", "**/*.jsx"],
    plugins: {
      react,
      "react-hooks": reactHooks
    },
    settings: {
      react: {
        version: "detect"
      }
    },
    rules: {
      ...react.configs.recommended.rules,
      ...react.configs["jsx-runtime"].rules,
      ...reactHooks.configs.recommended.rules,
      "react/prop-types": "off" // Using TypeScript for prop validation
    }
  },

  // MDX config
  flat,
  flatCodeBlocks,

  // Storybook-specific config
  {
    files: ["**/*.stories.@(js|ts|tsx)", ".storybook/**/*.@(js|ts)"],
    plugins: {
      storybook
    },
    rules: {
      ...storybook.configs.recommended.rules
    }
  },

  // Project-specific rules for TS/JS files
  {
    files: ["**/*.{js,ts,jsx,tsx}"],
    rules: {
      // TypeScript rules
      "@typescript-eslint/consistent-type-imports": "error",
      "@typescript-eslint/no-explicit-any": "warn",
      "@typescript-eslint/no-unused-vars": [
        "warn",
        {
          argsIgnorePattern: "^_",
          varsIgnorePattern: "^_"
        }
      ],

      // Allow flexible template expressions for HTML strings
      "@typescript-eslint/restrict-template-expressions": [
        "error",
        {
          allowNumber: true,
          allowBoolean: true,
          allowAny: false,
          allowNullish: false
        }
      ],

      // General rules
      "prefer-const": "warn",
      "max-nested-callbacks": ["warn", 3],
      "no-plusplus": [
        "warn",
        {
          allowForLoopAfterthoughts: true
        }
      ]
    }
  },

  // Prettier integration (must be last)
  eslintPluginPrettier
);
