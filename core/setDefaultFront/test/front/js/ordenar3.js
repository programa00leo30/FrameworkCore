// Seleciona as divs que queremos ordenar
var divs = document.querySelectorAll('#pai3 .datos');

// Converte a NodeList de divs para array
// https://developer.mozilla.org/en/docs/Web/API/NodeList#How_can_I_convert_NodeList_to_Array.3F
var ordem = [].map.call(divs, function(element) {
    return element;
});

// Ordena a array pelo atributo 'contagem'
ordem.sort(function(a,b) {
    var ca = parseFloat(a.getAttribute('contagem'), 10);
    var cb = parseFloat(b.getAttribute('contagem'), 10);
    return cb - ca;
});

// Reinsere os filhos no pai, resultando na ordem desejada
var container = document.querySelector('#pai3');
for(var i=0; i<ordem.length; i++) {
    container.appendChild(ordem[i]);
}
