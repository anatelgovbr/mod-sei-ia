window.onload = function() {

  var host = window.location.hostname;
  var protocolo = window.location.protocol;
  var porta = window.location.port;
  var urlCompleta = protocolo + '//' + host + (porta ? ':' + porta : '');

  window.ui = SwaggerUIBundle({
    url: urlCompleta + "/sei/modulos/ia/ws/swagger-ui/swagger.json",
    dom_id: '#swagger-ui',
    deepLinking: true,
    validatorUrl: null,
    presets: [
      SwaggerUIBundle.presets.apis,
      SwaggerUIStandalonePreset
    ],
    plugins: [
      SwaggerUIBundle.plugins.DownloadUrl
    ],
    layout: "StandaloneLayout"
  });
};
