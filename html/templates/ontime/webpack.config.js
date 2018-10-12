const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

function resolve(p) {
  return path.join(__dirname, p);
}

module.exports = {
  mode: 'production',

  entry: {
    style: resolve('src/scss/style.scss'),
  },

  output: {
    path: resolve('/'),
    filename: 'js/[name].js',
  },

  module: {
    rules: [
      {
        test: /\.(c|sa|sc)ss$/,
        use: [
          MiniCssExtractPlugin.loader,
          'css-loader',
          'sass-loader',
        ],
      },
    ],
  },

  plugins: [
    new MiniCssExtractPlugin({
      filename: 'css/[name].css',
    }),
  ],
};
