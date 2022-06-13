import typescript from "@rollup/plugin-typescript";
import { nodeResolve } from "@rollup/plugin-node-resolve";

export default {
  input: "web/index.ts",
  output: {
    file: "dist/js/controller.min.js",
    format: "iife",
    sourcemap: true,
  },
  context: "window",
  plugins: [nodeResolve(), typescript()],
};