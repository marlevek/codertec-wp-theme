// Mostra ou oculta o botão ao rolar a página
window.onscroll = function() {
    const btn = document.getElementById("btnVoltarAoTopo");
    if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
        btn.style.display = "block"; // Mostra o botão
    } else {
        btn.style.display = "none"; // Oculta o botão
    }
};

// Volta ao topo ao clicar no botão
document.getElementById("btnVoltarAoTopo").onclick = function() {
    document.body.scrollTop = 0; // Para navegadores Safari
    document.documentElement.scrollTop = 0; // Para outros navegadores
};