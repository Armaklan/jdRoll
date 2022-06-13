const path = require('path');

module.exports = {
  stories: ["../web/**/*.stories.mdx", "../web/**/*.stories.@(js|jsx|ts|tsx)"],
  addons: ["@storybook/addon-links", "@storybook/addon-essentials"],
  framework: "@storybook/web-components",
  webpackFinal: async (config) => {
    config.module.rules.push({
      test: /\.(ts|tsx)$/,
      include: path.resolve(__dirname, "../web"),
      loader: require.resolve("ts-loader"),
    });
    return config;
  },
};