export default {
  input: "web/vendor.js",
  output: {
    file: "dist/js/composant.min.js",
    format: "iife",
    sourcemap: true,
  },
  context: "window",
  plugins: [],
};