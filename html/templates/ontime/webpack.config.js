const path = require('path');
const ExtractTextPlugin = require('extract-text-webpack-plugin');

function resolve(p) {
  return path.join(__dirname, p);
}

const extractTextPlugin = new ExtractTextPlugin({
  filename: 'css/[name].css',
  disable: false,
});

module.exports = {
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
        use: ExtractTextPlugin.extract({
          fallback: 'style-loader',
          //resolve-url-loader may be chained before sass-loader if necessary
          use: ['css-loader', 'sass-loader']
        }),
      },
    ],
  },

  plugins: [
    extractTextPlugin,
  ],
};
