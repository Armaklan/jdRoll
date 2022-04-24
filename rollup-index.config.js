import typescript from "@rollup/plugin-typescript";

export default {
  input: "web/index.ts",
  output: {
    file: "dist/js/controller.min.js",
    format: "iife",
    sourcemap: true
  },
  context: "window",
  plugins: [typescript()],
};