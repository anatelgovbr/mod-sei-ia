table#tabela_ordenada tr th {
    text-align: left;
}
table#tabela_ordenada {
    width: 100%;
}
table#tabela_ordenada td {
    border: 1px solid #ccc;
}
table#tabela_ordenada td input {
    width: 100%
}
.gg-arrows-v {
    box-sizing: border-box;
    position: relative;
    display: block;
    transform: scale(var(--ggs,1));
    width: 10px;
    height: 20px;
    float:left;
    color: #28a745;
    width: 30% !important;
    background:
    linear-gradient(
        to left,
    currentColor 15px,transparent 0)
    no-repeat 4px 2px/2px 6px,
    linear-gradient(
    to left,
    currentColor 15px,transparent 0)
    no-repeat 4px 12px/2px 6px

}
.gg-arrows-v::after,
.gg-arrows-v::before {
    content: "";
    display: block;
    box-sizing: border-box;
    position: absolute;
    width: 6px;
    height: 6px;
    transform: rotate(-45deg);
    left: 2px
}

.gg-arrows-v::after {
    border-bottom: 2px solid;
    border-left: 2px solid;
    bottom: 1px
}

.gg-arrows-v::before {
    border-top: 2px solid;
    border-right: 2px solid;
    top: 1px
}
.btn_thumbs.down {
    rotate: 180deg;
}
.btn_thumbs {
    background-image: url(modulos/ia/imagens/like.svg);
    width: 20px;
    height: 20px;
    background-repeat: no-repeat;
    background-size: cover;
    transition: all 0.3s;
    cursor: pointer;
}
.btn_thumbs:hover, .btn_thumbs.active {
    background-image: url(modulos/ia/imagens/like_preto.svg);
}
tbody td:nth-child(-n+1):hover {
    cursor: move;
}
.bubbly-button {
    -webkit-appearance: none;
    appearance: none;
    position: relative;
    transition: transform ease-in 0.1s, box-shadow ease-in 0.25s;
}
.bubbly-button:focus {
    outline: 0;
}
.bubbly-button:before, .bubbly-button:after {
    position: absolute;
    content: "";
    display: block;
    width: 300%;
    height: 300%;
    left: -10px;
    z-index: 1000;
    transition: all ease-in-out 0.5s;
    background-repeat: no-repeat;
}
.bubbly-button:before {
    display: none;
    top: -75%;
    background-size: 10% 10%, 20% 20%, 15% 15%, 20% 20%, 18% 18%, 10% 10%, 15% 15%, 10% 10%, 18% 18%;
}
.down:before {
    background-image: radial-gradient(circle, #dc3545 20%, transparent 20%), radial-gradient(circle, transparent 20%, #dc3545 20%, transparent 30%), radial-gradient(circle, #dc3545 20%, transparent 20%), radial-gradient(circle, #dc3545 20%, transparent 20%), radial-gradient(circle, transparent 10%, #dc3545 15%, transparent 20%), radial-gradient(circle, #dc3545 20%, transparent 20%), radial-gradient(circle, #dc3545 20%, transparent 20%), radial-gradient(circle, #dc3545 20%, transparent 20%), radial-gradient(circle, #dc3545 20%, transparent 20%);
}
.down:after {
    background-image: radial-gradient(circle, #dc3545 20%, transparent 20%), radial-gradient(circle, #dc3545 20%, transparent 20%), radial-gradient(circle, transparent 10%, #dc3545 15%, transparent 20%), radial-gradient(circle, #dc3545 20%, transparent 20%), radial-gradient(circle, #dc3545 20%, transparent 20%), radial-gradient(circle, #dc3545 20%, transparent 20%), radial-gradient(circle, #dc3545 20%, transparent 20%);
}
.up:before {
    background-image: radial-gradient(circle, #1ac0fb 20%, transparent 20%), radial-gradient(circle, transparent 20%, #1ac0fb 20%, transparent 30%), radial-gradient(circle, #1ac0fb 20%, transparent 20%), radial-gradient(circle, #1ac0fb 20%, transparent 20%), radial-gradient(circle, transparent 10%, #1ac0fb 15%, transparent 20%), radial-gradient(circle, #1ac0fb 20%, transparent 20%), radial-gradient(circle, #1ac0fb 20%, transparent 20%), radial-gradient(circle, #1ac0fb 20%, transparent 20%), radial-gradient(circle, #1ac0fb 20%, transparent 20%);
}
.up:after {
    background-image: radial-gradient(circle, #1ac0fb 20%, transparent 20%), radial-gradient(circle, #1ac0fb 20%, transparent 20%), radial-gradient(circle, transparent 10%, #1ac0fb 15%, transparent 20%), radial-gradient(circle, #1ac0fb 20%, transparent 20%), radial-gradient(circle, #1ac0fb 20%, transparent 20%), radial-gradient(circle, #1ac0fb 20%, transparent 20%), radial-gradient(circle, #1ac0fb 20%, transparent 20%);
}
.bubbly-button:after {
    display: none;
    bottom: -75%;
    background-size: 15% 15%, 20% 20%, 18% 18%, 20% 20%, 15% 15%, 10% 10%, 20% 20%;
}
.bubbly-button:active {
    transform: scale(0.9);
}
.bubbly-button.animate:before {
    display: block;
    animation: topBubbles ease-in-out 0.75s forwards;
}
.bubbly-button.animate:after {
    display: block;
    animation: bottomBubbles ease-in-out 0.75s forwards;
}
@keyframes topBubbles {
    0% {
        background-position: 5% 90%, 10% 90%, 10% 90%, 15% 90%, 25% 90%, 25% 90%, 40% 90%, 55% 90%, 70% 90%;
    }
    50% {
        background-position: 0% 80%, 0% 20%, 10% 40%, 20% 0%, 30% 30%, 22% 50%, 50% 50%, 65% 20%, 90% 30%;
    }
    100% {
        background-position: 0% 70%, 0% 10%, 10% 30%, 20% -10%, 30% 20%, 22% 40%, 50% 40%, 65% 10%, 90% 20%;
        background-size: 0% 0%, 0% 0%, 0% 0%, 0% 0%, 0% 0%, 0% 0%;
    }
}
@keyframes bottomBubbles {
    0% {
        background-position: 10% -10%, 30% 10%, 55% -10%, 70% -10%, 85% -10%, 70% -10%, 70% 0%;
    }
    50% {
        background-position: 0% 80%, 20% 80%, 45% 60%, 60% 100%, 75% 70%, 95% 60%, 105% 0%;
    }
    100% {
        background-position: 0% 90%, 20% 90%, 45% 70%, 60% 110%, 75% 80%, 95% 70%, 110% 10%;
        background-size: 0% 0%, 0% 0%, 0% 0%, 0% 0%, 0% 0%, 0% 0%;
    }
}
.ui-sortable-helper {
    background: #f8f9fa;
}
#divMsg {
    display: none;
}
#objetivosOds {
    padding-left: 5%;
    padding-right: 5%;
}
#objetivosOds .imagem {
    border-radius: 5px;
    width: 160px;
    height: 160px;
}
#objetivosOds img.desfoque, #objetivosOds img.sugestaoIa, #objetivosOds img.historico, #objetivosOds img.sugestaoUsuExt {
    opacity: 0.25;
}
#objetivosOds img.historico {
    position: relative;
}
div.imagem > div.historico {
    background:url('<?= Icone::HISTORICO ?>') 0 0 no-repeat;
}
div.imagem > div.sugestaoIa {
    background:url('modulos/ia/imagens/md_ia_icone_alerta.svg?<?= Icone::VERSAO ?>') 0 0 no-repeat;
}
div.imagem > div.sugestaoUsuExt {
    background:url('modulos/ia/imagens/md_ia_usu_externo_sm.svg?<?= Icone::VERSAO ?>') 0 0 no-repeat;
}
div.imagem > div.historico, div.imagem > div.sugestaoIa, div.imagem > div.sugestaoUsuExt {
    display: block !important;
    position: absolute;
    left: 21px;
    bottom: 3px;
    width: 24px;
    height: 24px;
}
@media (max-width: 576px) {
    #objetivosOds .imagem {
        width: 95%;
        height: auto;
    }
    div.imagem > div.historico, div.imagem > div.sugestaoIa, div.imagem > div.sugestaoUsuExt {
        left: 25px;
        bottom: 15px;
    }
}
.colorido {
    opacity: 100 !important;
}