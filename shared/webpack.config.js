const webpack = require('webpack');
const path = require('path');
const VueLoaderPlugin = require('vue-loader/lib/plugin');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");

function resolve(p) {
  return path.join(__dirname, p);
}

module.exports = {
  mode: 'production',

  entry: {
    lancms: [
      'font-awesome/css/font-awesome.css',
      resolve('src/scss/lancms.scss'),
      resolve('src/js/lancms.js'),
    ],
  },

  output: {
    path: resolve('/../html/templates/shared'),
    publicPath: 'templates/shared/',
    filename: '[name].js',
  },

  resolve: {
    alias: {
      '@': resolve('src/js'),
      '%': resolve('src/scss'),
      'vue$': 'vue/dist/vue.esm.js',
    },
  },

  module: {
    rules: [
      {
        test: /\.(c|sa|sc)ss$/,
        use: [
          {
            loader: MiniCssExtractPlugin.loader,
          },
          "css-loader",
          "sass-loader"
        ],
      },

      {
        test: /\.js$/,
        exclude: /node_modules/,
        use: 'babel-loader',
      },

      {
        test: /\.vue$/,
        use: 'vue-loader',
      },

      {
        test: /\.(woff(2)?|ttf|eot|svg)(\?v=\d+\.\d+\.\d+)?$/,
        use: [{
          loader: 'file-loader',
          options: {
            name: '[name].[ext]',
            outputPath: 'fonts/',
            publicPath: '/templates/shared/fonts/',
          },
        }],
      },
    ],
  },

  plugins: [
    new VueLoaderPlugin(),
    new MiniCssExtractPlugin({
      // Options similar to the same options in webpackOptions.output
      // both options are optional
      filename: "[name].css",
      chunkFilename: "[id].css"
    }),
    new webpack.DefinePlugin({
      'process.env.NODE_ENV': '"' + process.env.NODE_ENV + '"',
    }),
  ],
};
