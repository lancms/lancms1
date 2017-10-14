const webpack = require('webpack');
const path = require('path');
const ExtractTextPlugin = require('extract-text-webpack-plugin');
const ExtraneousFileCleanupPlugin = require('webpack-extraneous-file-cleanup-plugin');

function resolve(p) {
  return path.join(__dirname, p);
}

const extractTextPlugin = new ExtractTextPlugin({
  filename: (getPath) => getPath('[name].css').replace('css/js', 'css'),
  disable: false,
});

module.exports = {
  entry: {
    lancms: [
      'font-awesome-webpack',
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
        use: ExtractTextPlugin.extract({
          use: [{
            loader: 'css-loader',
          }, {
            loader: 'sass-loader',
          }],
          fallback: 'style-loader',
        }),
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

      // the url-loader uses DataUrls.
      // the file-loader emits files.
      { test: /\.woff(2)?(\?v=[0-9]\.[0-9]\.[0-9])?$/, loader: "url-loader?limit=10000&mimetype=application/font-woff" },
      { test: /\.(ttf|eot|svg)(\?v=[0-9]\.[0-9]\.[0-9])?$/, loader: "file-loader" },
    ],
  },

  plugins: [
    extractTextPlugin,
    new webpack.DefinePlugin({
      'process.env.NODE_ENV': '"' + process.env.NODE_ENV + '"',
    }),
  ],
};
