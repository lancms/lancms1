import 'whatwg-fetch';

export default function(url, opts = {}) {
  return new Promise((resolve, reject) => {
    fetch(url, opts)
      .then(checkStatus)
      .then(response => response.json())
      .then(resolve)
      .catch(reject);
  });
}

function checkStatus(response) {
  if (response.status >= 200 && response.status < 300) {
    return response
  } else {
    var error = new Error(response.statusText)
    error.response = response
    throw error
  }
}
