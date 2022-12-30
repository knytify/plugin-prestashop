function dictToURI(dict, remove_null = true) {
    var str = [];
    for (var p in dict) {
      if (remove_null && (dict[p] === null || dict[p] === undefined)) {
        continue;
      }
      str.push(encodeURIComponent(p) + "=" + encodeURIComponent(dict[p]));
    }
    return str.join("&");
  }

  function updateQueryStringParameter(uri, key, value) {
    var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
    var separator = uri.indexOf("?") !== -1 ? "&" : "?";
    if (uri.match(re)) {
      return uri.replace(re, "$1" + key + "=" + value + "$2");
    } else {
      return uri + separator + key + "=" + value;
    }
  }

  function updateQueryStringParameters(subject, args = {}) {
    let subject_copy = (" " + subject).slice(1);
    Object.keys(args).forEach((param_name) => {
      const param_value = args[param_name];
      subject_copy = updateQueryStringParameter(
        subject_copy,
        param_name,
        param_value
      );
    });
    return subject_copy;
  }

  function updateURLParams(params_to_update = null, clear = false) {
    if (params_to_update === null) {
      params_to_update = {};
    }
    let path = window.location.pathname;
    if (!clear) {
      path += window.location.search;
    }
    const new_path = updateQueryStringParameters(path, params_to_update);
    window.history.pushState({}, null, new_path);
  }

  const dictToQueryString = (data) => Object.keys(data).map((key) => {
    return encodeURIComponent(key) + '=' + encodeURIComponent(data[key])
  }).join('&');


  export { updateQueryStringParameters,  dictToQueryString };
