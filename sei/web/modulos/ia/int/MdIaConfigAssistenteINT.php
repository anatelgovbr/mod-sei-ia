<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 23/11/2022 - criado por michaelr.colab
 *
 * Versão do Gerador de Código: 1.43.1
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdIaConfigAssistenteINT extends InfraINT
{

    public static function montarCssChat()
    {
        $css = '
            <style>
                #chat_ia {
                    z-index: 9999;
                    position: fixed;
                    right: 17px;
                    bottom: 17px;
                }
                #chat_ia .widget-button {
                    width: 52px;
                    height: 52px;
                    background-color: #2494da;
                    border-radius: 30px;
                    position: absolute;
                    right: 0;
                    bottom: 0;
                    cursor: pointer;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    margin-right: 19px;
                    box-shadow: 0 0.125rem 0.5rem rgba(0, 0, 0, .3);
                }
                #chat_ia .chat-open {
                    fill: #fff;
                    width: 35px;
                    height: 35px;
                }
                #chat_ia .close-chat {
                    fill: #fff;
                    width: 35px;
                    height: 35px;
                    display: none;
                    -webkit-animation: close-chat 1.9s ease-in-out both;
                    animation: close-chat 1.9s ease-in-out both;
                }
                #chat_ia .widget-content {
                    visibility: hidden;
                    opacity: 0;
                    transition: .4s;
                    width: 360px;
                    height: 44.1rem;
                    overflow: hidden;
                    border-radius: 5px;
                    box-shadow:0 7px 30px -10px rgba(150,170,180,0.5);
                    position: absolute;
                    bottom: 65px;
                    right: 0;
                    margin-right: 1.2px;
                    background-color: white;
                    display: none;
                }
                #chat_ia .widget-title {
                    display: flex;
                    align-items: center;
                    padding: 15px;
                    background-color: #2494da;
                    color: #FFF;
                    font-size: 14px;
                }
                #chat_ia .widget-container-dialog {
                    display: flex;
                    flex-direction: row;
                    justify-content: flex-end;
                    height: 100%;
                }
                #chat_ia .interaction-container {
                    padding: 0 15px;
                    height: 70%;
                    display: flex;
                    flex-direction: column;
                    justify-content: flex-end;
                    overflow: hidden;
                    scrollbar-width: thin;
                }
                #chat_ia .interaction-container::-webkit-scrollbar {
                    width: 12px;
                }
                .wrapperMensagens {
                    height: 50px;
                }
                #validacaoMensagem {
                    overflow-x: hidden;
		            overflow-y: auto;
                    scrollbar-width: thin;
                    height: 25px;
                }
                .wrapperMensagens::-webkit-scrollbar {
                    width: 12px;
                }
                #chat_ia .interaction.client div.output {
                    background: #ccc;
                    color: #000;
                    border-radius: 10px;
                    border-top-left-radius: 0;
                    padding: 10px;
                    margin: 0;
                    max-width: 100%;
                }
                #chat_ia .mensagemIdentificador {
                    display: flex;
                }
                #chat_ia .interaction.agent div.input {
                    background-color: #2494da;
                    color: #fff;
                    border-radius: 10px;
                    border-top-left-radius: 0;
                    padding: 10px;
                    margin: 0;
                }
                #chat_ia .send-message {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    padding-top: 2px;
                }
                #chat_ia  #botaoEnviarMensagem {
                    fill: #555;
                    opacity: 0.4;
                }
                #chat_ia .habilitadoEnvio {
                    fill: #2494da !important;
                    opacity: 1 !important;
                }
                #chat_ia .send-message-input {
                    width: 300px;
                    padding: 10px 5px;
                    border-radius: 5px;
                    border: 1px solid #ccc;
                    margin: 0 5px;
                    min-height: 42px;
                    max-height: 120px;
                }
                #chat_ia .send-message-input:focus {
                    outline: none;
                    background: #f9f9f9;
                    border-bottom: 2px solid #2494da;
                }
                #chat_ia .widget-input-open {
                    display: none;
                }
                #chat_ia .widget-input-open:checked ~ .widget-content {
                    visibility: visible;
                    opacity: 1;
                }
                #chat_ia .widget-input-open:checked + .widget-button .chat-open {
                    display: none;
                }
                #chat_ia .widget-input-open:checked + .widget-button .close-chat {
                    display: block;
                }
                /* animacao close */
                @-webkit-keyframes close-chat {
                0% {
                    -webkit-transform: rotate(0);
                        transform: rotate(0);
                    }
                    100% {
                    -webkit-transform: rotate(360deg);
                        transform: rotate(360deg);
                    }
                }
                
                @keyframes close-chat {
                0% {
                    -webkit-transform: rotate(0);
                        transform: rotate(0);
                    }
                    100% {
                    -webkit-transform: rotate(360deg);
                        transform: rotate(360deg);
                    }
                }
                
                #chat_ia #botaoTransferir {
                  display: none;
                  position: fixed;
                  padding: 10px;
                  color: white;
                  border: none;
                  border-radius: 5px;
                  cursor: pointer;
                  background-color: #2494da;
                }
                #chat_ia #reduzirAssistente {
                    display: none;
                    float: right;
                    margin: 3px;
                }
                #chat_ia #expandirAssistente {
                    float: right;
                    margin: 3px;
                }
                #chat_ia #iconesExpandir {
                    width: 30%;
                    z-index: 99;
                }
                #chat_ia #tituloChat {
                    width: 70%;
                    z-index: 99;
                }
                #chat_ia #iconesExpandir svg, #chat_ia #tituloChat svg {
                    cursor: pointer;
                }
                @keyframes dots-1 { from { opacity: 0; } 25% { opacity: 1; } }
                @keyframes dots-2 { from { opacity: 0; } 50% { opacity: 1; } }
                @keyframes dots-3 { from { opacity: 0; } 75% { opacity: 1; } }
                @-webkit-keyframes dots-1 { from { opacity: 0; } 25% { opacity: 1; } }
                @-webkit-keyframes dots-2 { from { opacity: 0; } 50% { opacity: 1; } }
                @-webkit-keyframes dots-3 { from { opacity: 0; } 75% { opacity: 1; } }
                
                #chat_ia .dots span {
                    animation: dots-1 1s infinite steps(1);
                    -webkit-animation: dots-1 1s infinite steps(1);
                    font-size: 17px;
                    font-weight: bold;
                }
                
                #chat_ia .dots span:first-child + span {
                    animation-name: dots-2;
                    -webkit-animation-name: dots-2;
                }
                
                #chat_ia .dots span:first-child + span + span {
                    animation-name: dots-3;
                    -webkit-animation-name: dots-3;
                }
                #chat_ia .acoes_assistente {
                    border-radius: 0 0 10px 10px;
                    min-height: 17px;
                    margin: 5px 30px 0 30px;
                }
                #chat_ia .acoes_assistente .estrelinha {
                      background-image: url("data:image/svg+xml,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20576%20512%22%20%20width%3D%2216%22%20height%3D%2216%22%20fill%3D%22currentColor%22%3E%3Cpath%20d%3D%22M287.9%200c9.2%200%2017.6%205.2%2021.6%2013.5l68.6%20141.3%20153.2%2022.6c9%201.3%2016.5%207.6%2019.3%2016.3s.5%2018.1-5.9%2024.5L433.6%20328.4l26.2%20155.6c1.5%209-2.2%2018.1-9.7%2023.5s-17.3%206-25.3%201.7l-137-73.2L151%20509.1c-8.1%204.3-17.9%203.7-25.3-1.7s-11.2-14.5-9.7-23.5l26.2-155.6L31.1%20218.2c-6.5-6.4-8.7-15.9-5.9-24.5s10.3-14.9%2019.3-16.3l153.2-22.6L266.3%2013.5C270.4%205.2%20278.7%200%20287.9%200zm0%2079L235.4%20187.2c-3.5%207.1-10.2%2012.1-18.1%2013.3L99%20217.9%20184.9%20303c5.5%205.5%208.1%2013.3%206.8%2021L171.4%20443.7l105.2-56.2c7.1-3.8%2015.6-3.8%2022.6%200l105.2%2056.2L384.2%20324.1c-1.3-7.7%201.2-15.5%206.8-21l85.9-85.1L358.6%20200.5c-7.8-1.2-14.6-6.1-18.1-13.3L287.9%2079z%22%2F%3E%3C%2Fsvg%3E");
                      background-repeat: no-repeat;
                      margin: 0 4px;
                      width: 16px;
                      height: 16px;
                      float: left;
                }
                #chat_ia .acoes_assistente .copiar {
                      background-image: url("data:image/svg+xml,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20448%20512%22%3E%3Cpath%20d%3D%22M208%200H332.1c12.7%200%2024.9%205.1%2033.9%2014.1l67.9%2067.9c9%209%2014.1%2021.2%2014.1%2033.9V336c0%2026.5-21.5%2048-48%2048H208c-26.5%200-48-21.5-48-48V48c0-26.5%2021.5-48%2048-48zM48%20128h80v64H64V448H256V416h64v48c0%2026.5-21.5%2048-48%2048H48c-26.5%200-48-21.5-48-48V176c0-26.5%2021.5-48%2048-48z%22%2F%3E%3C%2Fsvg%3E");
                      background-repeat: no-repeat;
                      width: 16px;
                      height: 16px;
                      float: left;
                }
                #chat_ia .acoes_assistente .estrelinha:hover, .pontuado {
                    content: url("data:image/svg+xml,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20576%20512%22%3E%3C!--!Font%20Awesome%20Free%206.5.1%20by%20%40fontawesome%20-%20https%3A%2F%2Ffontawesome.com%20License%20-%20https%3A%2F%2Ffontawesome.com%2Flicense%2Ffree%20Copyright%202024%20Fonticons%2C%20Inc.--%3E%3Cpath%20fill%3D%22%23FFD43B%22%20d%3D%22M316.9%2018C311.6%207%20300.4%200%20288.1%200s-23.4%207-28.8%2018L195%20150.3%2051.4%20171.5c-12%201.8-22%2010.2-25.7%2021.7s-.7%2024.2%207.9%2032.7L137.8%20329%20113.2%20474.7c-2%2012%203%2024.2%2012.9%2031.3s23%208%2033.8%202.3l128.3-68.5%20128.3%2068.5c10.8%205.7%2023.9%204.9%2033.8-2.3s14.9-19.3%2012.9-31.3L438.5%20329%20542.7%20225.9c8.6-8.5%2011.7-21.2%207.9-32.7s-13.7-19.9-25.7-21.7L381.2%20150.3%20316.9%2018z%22%2F%3E%3C%2Fsvg%3E") !important;
                    text-shadow: 0 4px 5px rgba(0, 0, 0, .5);
                }
                #chat_ia .acoes_assistente .copiar:hover {
                    background-image: url("data:image/svg+xml,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20448%20512%22%3E%3Cpath%20fill%3D%22%230d69af%22%20d%3D%22M208%200H332.1c12.7%200%2024.9%205.1%2033.9%2014.1l67.9%2067.9c9%209%2014.1%2021.2%2014.1%2033.9V336c0%2026.5-21.5%2048-48%2048H208c-26.5%200-48-21.5-48-48V48c0-26.5%2021.5-48%2048-48zM48%20128h80v64H64V448H256V416h64v48c0%2026.5-21.5%2048-48%2048H48c-26.5%200-48-21.5-48-48V176c0-26.5%2021.5-48%2048-48z%22%2F%3E%3C%2Fsvg%3E");
                }
                #chat_ia .rating {
                    display: flex;
                    transform: rotateY(180deg);
                }
                #chat_ia .rating label {
                    display: block;
                    cursor: pointer;
                    width: 16px;
                    height: 16px;
                }
                #chat_ia .avaliacao_estrelinhas {
                    float: left;
                }
                #chat_ia .rating label:before {
                    content: url("data:image/svg+xml,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20576%20512%22%20%20width%3D%2216%22%20height%3D%2216%22%20fill%3D%22currentColor%22%3E%3Cpath%20d%3D%22M287.9%200c9.2%200%2017.6%205.2%2021.6%2013.5l68.6%20141.3%20153.2%2022.6c9%201.3%2016.5%207.6%2019.3%2016.3s.5%2018.1-5.9%2024.5L433.6%20328.4l26.2%20155.6c1.5%209-2.2%2018.1-9.7%2023.5s-17.3%206-25.3%201.7l-137-73.2L151%20509.1c-8.1%204.3-17.9%203.7-25.3-1.7s-11.2-14.5-9.7-23.5l26.2-155.6L31.1%20218.2c-6.5-6.4-8.7-15.9-5.9-24.5s10.3-14.9%2019.3-16.3l153.2-22.6L266.3%2013.5C270.4%205.2%20278.7%200%20287.9%200zm0%2079L235.4%20187.2c-3.5%207.1-10.2%2012.1-18.1%2013.3L99%20217.9%20184.9%20303c5.5%205.5%208.1%2013.3%206.8%2021L171.4%20443.7l105.2-56.2c7.1-3.8%2015.6-3.8%2022.6%200l105.2%2056.2L384.2%20324.1c-1.3-7.7%201.2-15.5%206.8-21l85.9-85.1L358.6%20200.5c-7.8-1.2-14.6-6.1-18.1-13.3L287.9%2079z%22%2F%3E%3C%2Fsvg%3E");
                    font-family: fontAwesome;
                    position: relative;
                    display: block;
                    font-size: 16px;
                    color: #0e1316;
                }
                
                #chat_ia .rating label:after {
                    content: url("data:image/svg+xml,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20576%20512%22%3E%3C!--!Font%20Awesome%20Free%206.5.1%20by%20%40fontawesome%20-%20https%3A%2F%2Ffontawesome.com%20License%20-%20https%3A%2F%2Ffontawesome.com%2Flicense%2Ffree%20Copyright%202024%20Fonticons%2C%20Inc.--%3E%3Cpath%20fill%3D%22%23FFD43B%22%20d%3D%22M316.9%2018C311.6%207%20300.4%200%20288.1%200s-23.4%207-28.8%2018L195%20150.3%2051.4%20171.5c-12%201.8-22%2010.2-25.7%2021.7s-.7%2024.2%207.9%2032.7L137.8%20329%20113.2%20474.7c-2%2012%203%2024.2%2012.9%2031.3s23%208%2033.8%202.3l128.3-68.5%20128.3%2068.5c10.8%205.7%2023.9%204.9%2033.8-2.3s14.9-19.3%2012.9-31.3L438.5%20329%20542.7%20225.9c8.6-8.5%2011.7-21.2%207.9-32.7s-13.7-19.9-25.7-21.7L381.2%20150.3%20316.9%2018z%22%2F%3E%3C%2Fsvg%3E");
                    font-family: fontAwesome;
                    display: block;
                    font-size: 16px;
                    color: #ffff00;
                    top: -25px;
                    opacity: 0;
                    transition: .5;
                    text-shadow: 0 4px 5px rgba(0, 0, 0, .5);
                    position: relative;
                }
                #chat_ia .rating label:hover:after,
                #chat_ia .rating label:hover ~ label:after, #chat_ia .marcado {
                    opacity: 1;
                }
                #chat_ia .marcado {
                    content: url("data:image/svg+xml,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20576%20512%22%3E%3C!--!Font%20Awesome%20Free%206.5.1%20by%20%40fontawesome%20-%20https%3A%2F%2Ffontawesome.com%20License%20-%20https%3A%2F%2Ffontawesome.com%2Flicense%2Ffree%20Copyright%202024%20Fonticons%2C%20Inc.--%3E%3Cpath%20fill%3D%22%23FFD43B%22%20d%3D%22M316.9%2018C311.6%207%20300.4%200%20288.1%200s-23.4%207-28.8%2018L195%20150.3%2051.4%20171.5c-12%201.8-22%2010.2-25.7%2021.7s-.7%2024.2%207.9%2032.7L137.8%20329%20113.2%20474.7c-2%2012%203%2024.2%2012.9%2031.3s23%208%2033.8%202.3l128.3-68.5%20128.3%2068.5c10.8%205.7%2023.9%204.9%2033.8-2.3s14.9-19.3%2012.9-31.3L438.5%20329%20542.7%20225.9c8.6-8.5%2011.7-21.2%207.9-32.7s-13.7-19.9-25.7-21.7L381.2%20150.3%20316.9%2018z%22%2F%3E%3C%2Fsvg%3E");
                    text-shadow: 0 4px 5px rgba(0, 0, 0, .5);
                }
                #chat_ia .pontuadoMeiaEstrela {
                    content: url("data:image/svg+xml,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20576%20512%22%3E%3C%21--%21Font%20Awesome%20Free%206.5.1%20by%20%40fontawesome%20-%20https%3A%2F%2Ffontawesome.com%20License%20-%20https%3A%2F%2Ffontawesome.com%2Flicense%2Ffree%20Copyright%202024%20Fonticons%2C%20Inc.--%3E%3Cpath%20fill%3D%22%23FFD43B%22%20d%3D%22M288%20376.4l.1-.1%2026.4%2014.1%2085.2%2045.5-16.5-97.6-4.8-28.7%2020.7-20.5%2070.1-69.3-96.1-14.2-29.3-4.3-12.9-26.6L288.1%2086.9l-.1%20.3V376.4zm175.1%2098.3c2%2012-3%2024.2-12.9%2031.3s-23%208-33.8%202.3L288.1%20439.8%20159.8%20508.3C149%20514%20135.9%20513.1%20126%20506s-14.9-19.3-12.9-31.3L137.8%20329%2033.6%20225.9c-8.6-8.5-11.7-21.2-7.9-32.7s13.7-19.9%2025.7-21.7L195%20150.3%20259.4%2018c5.4-11%2016.5-18%2028.8-18s23.4%207%2028.8%2018l64.3%20132.3%20143.6%2021.2c12%201.8%2022%2010.2%2025.7%2021.7s.7%2024.2-7.9%2032.7L438.5%20329l24.6%20145.7z%22%2F%3E%3C%2Fsvg%3E");
                    text-shadow: 0 4px 5px rgba(0, 0, 0, .5);
                }
                #chat_ia #rodapeChat {
                    font-size: 12px;
                    text-align: center;
                    color: #5f5f5f;
                    margin-top: 3px;
                }
                #chat_ia .acoes_assistente .transportarEditor {
                      background-image: url("data:image/svg+xml,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20512%20512%22%3E%3Cpath%20d%3D%22M498.1%205.6c10.1%207%2015.4%2019.1%2013.5%2031.2l-64%20416c-1.5%209.7-7.4%2018.2-16%2023s-18.9%205.4-28%201.6L284%20427.7l-68.5%2074.1c-8.9%209.7-22.9%2012.9-35.2%208.1S160%20493.2%20160%20480V396.4c0-4%201.5-7.8%204.2-10.7L331.8%20202.8c5.8-6.3%205.6-16-.4-22s-15.7-6.4-22-.7L106%20360.8%2017.7%20316.6C7.1%20311.3%20.3%20300.7%200%20288.9s5.9-22.8%2016.1-28.7l448-256c10.7-6.1%2023.9-5.5%2034%201.4z%22%2F%3E%3C%2Fsvg%3E");
                      background-repeat: no-repeat;
                      width: 16px;
                      height: 16px;
                      float: left;
                }
                #chat_ia .acoes_assistente .transportarEditor:hover {
                    background-image: url("data:image/svg+xml,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20512%20512%22%3E%3Cpath%20fill%3D%22%23308241%22%20d%3D%22M498.1%205.6c10.1%207%2015.4%2019.1%2013.5%2031.2l-64%20416c-1.5%209.7-7.4%2018.2-16%2023s-18.9%205.4-28%201.6L284%20427.7l-68.5%2074.1c-8.9%209.7-22.9%2012.9-35.2%208.1S160%20493.2%20160%20480V396.4c0-4%201.5-7.8%204.2-10.7L331.8%20202.8c5.8-6.3%205.6-16-.4-22s-15.7-6.4-22-.7L106%20360.8%2017.7%20316.6C7.1%20311.3%20.3%20300.7%200%20288.9s5.9-22.8%2016.1-28.7l448-256c10.7-6.1%2023.9-5.5%2034%201.4z%22%2F%3E%3C%2Fsvg%3E");
                }
                #chat_ia #imgArvoreAguarde {
                    display: none;
                }
                #chat_ia #validacaoMensagem {
                    padding: 0px 20px;
                    min-height: 13px;
                    display: block;
                }
                #chat_ia em {
                    font-style: italic;
                }
                #chat_ia .code-container {
                    position: relative;
                }
                
                #chat_ia .code .code-body {
                    background-color: #EEE;
                    padding: 10px;
                    font-family: \'Courier New\', monospace;
                    white-space: pre-wrap;
                    background: #FFF;
                    color: #000;
                }
                
                #chat_ia .code .code-body code {
                    background-color: unset;
                }
                #chat_ia .code-container .copyButton {
                    position: absolute;
                    top: 5px;
                    right: 5px;
                    padding: 5px 10px;
                    cursor: pointer;
                    background-color: #4caf50;
                    color: #fff;
                    border: none;
                    border-radius: 3px;
                }
                #chat_ia .expandido .code-container {
                    width: 70%;
                    margin-left: 15%;
                }
                #chat_ia .code-header {
                    display: flex;
                    padding: 4px 8px 4px 12px;
                    align-items: center;
                    background: #efefef;
                    border-radius: var(--cib-border-radius-large) var(--cib-border-radius-large) 0 0;
                }
                #chat_ia .highlighted {
                    flex: 1 1 0%;
                    display: block;
                    font-size: 16px;
                    font-weight: 600;
                    line-height: 22px;
                }
                #chat_ia .code {
                    border-radius: 5px;                  
                }
                #chat_ia .code-actions .copiar {
                    background-image: url("data:image/svg+xml,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20448%20512%22%3E%3Cpath%20d%3D%22M208%200H332.1c12.7%200%2024.9%205.1%2033.9%2014.1l67.9%2067.9c9%209%2014.1%2021.2%2014.1%2033.9V336c0%2026.5-21.5%2048-48%2048H208c-26.5%200-48-21.5-48-48V48c0-26.5%2021.5-48%2048-48zM48%20128h80v64H64V448H256V416h64v48c0%2026.5-21.5%2048-48%2048H48c-26.5%200-48-21.5-48-48V176c0-26.5%2021.5-48%2048-48z%22%2F%3E%3C%2Fsvg%3E");
                    background-repeat: no-repeat;
                    width: 16px;
                    height: 16px;
                    float: left;
                }
                #chat_ia strong * {
                    font-weight: bolder;
                }
                #chat_ia blockquote {
                    --tw-border-opacity: 1;
                    border-color: rgba(155, 155, 155, var(--tw-border-opacity));
                    border-left-width: 2px;
                    line-height: 1.5rem;
                    margin: 0;
                    padding-bottom: .5rem;
                    padding-left: 1rem;
                    padding-top: .5rem;
                }
                #chat_ia table {
                    border-collapse: collapse;
                    width: 100%;
                }
                
                #chat_ia table, #chat_ia th, #chat_ia td {
                    border: 1px solid black;
                }
                
                #chat_ia th, #chat_ia td {
                    padding: 8px;
                    text-align: left;
                }
                #chat_ia .tooltip {
                    position: absolute;
                    background-color: black;
                    color: white;
                    padding: 5px;
                    border-radius: 5px;
                    display: none;
                }
                
                #chat_ia .icon:hover + .tooltip {
                    display: block;
                }

                #chat_ia .agent svg {
                    width: 24px;
                }
                #chat_ia .iconeIdentificacao {
                    width: 30px;
                    height: 30px;
                    min-width: 30px;    
                }
                #chat_ia .interaction {
                    margin: 15px 0;
                }
                #chat_ia .textoPuro {
                    display: none;
                }
                #chat_ia .highlighted {
                    font-size: 14px;
                }
                #chat_ia p {
                    margin-bottom: 0.5rem;
                }
                #chat_ia a {
                    color: #FFF;
                    text-decoration: underline;
                    font-size: inherit;
                }
                #chat_ia a:hover {
                    text-decoration: none;
                }
                #chat_ia code {
                    font-size: 100%;
                }
                #chat_ia .iconeOrientacoesGerais, #chat_ia .iconeConfiguracoes, #chat_ia .iconeAdicionarTopico {
                    margin: 0 3px 0 0;
                }
                #chat_ia #painelTopicos {
                    display: none;
                    background: #f9f9f9;
                    margin: 0;
                    padding: 15px 20px;
                    width: 15%;
                    height: 100%;
                    min-width: 195px;
                }
                #chat_ia #conteudoChat {
                    width: 100%;
                }
                #chat_ia .nav-link {
                    width: 100%;
                }
                #chat_ia .nav-pills .nav-link.active, .nav-pills .show>.nav-link, .topico.active, #chat_ia .topico:hover {
                    background: #ececec !important;
                    color: #000;
                }
                #chat_ia #adicionarTopico {
                    color: #fff;
                    background-color: #007bff !important;
                    margin-bottom: 20px;
                }
                .topico a {
                    float: right;
                }
                #chat_ia .rename {
                    line-height: 30px;
                }
                #chat_ia .arquivo {
                    line-height: 30px;
                    margin-left: 10px;
                }
                #chat_ia .selecionaTopico {
                    width: 100%;
                }
                #chat_ia .topico {
                    height: 35px;
                    position: relative;
                    border-radius: 0.5rem;
                }
                
                #chat_ia #tituloChat a, #chat_ia .topico a  {
                    text-decoration: none;
                }
                #chat_ia .selecionaTopico button {
                    padding-right: 10px;
                    padding-left: 10px;
                }
                #chat_ia .selecionaTopico button svg {
                    margin-right: 5px;
                }
                #chat_ia .acoesTopico {
                    display: none;
                    position: absolute;
                    right: 0;
                    top: 0;
                    background: linear-gradient(to left, rgba(236, 236, 236, 1) 70%, rgba(236, 236, 236, 0) 90%);
                    width: 90px;
                    height: 34px;
                    padding: 0 20px;
                }
                #chat_ia .acoesVisiveis {
                    display: block;
                }
                #chat_ia .topicoEmEdicao {
                    display: none !important;
                }
                #chat_ia .erro_ia {
                    color: #f90000 !important;
                }
                #chat_ia .topico, #chat_ia .topico input {
                    position: relative;
                    display: inline-block;
                    max-width: 100%;
                    width: 100%;
                }
            
                #chat_ia .topico .nav-link {
                    overflow: hidden;
                    text-overflow: ellipsis;
                    white-space: nowrap;
                }
                #chat_ia #listagemTopicos {
                    max-width: 100%;
                }
                #chat_ia .iconeIdentificacao img {
                    cursor: default;
                }
                #btnInfraTopo {
                    right: 6.2rem;
                }   
                #chat_ia #listagemTopicos {
                    overflow-y: auto;
                    scrollbar-width: thin;
                }
                #chat_ia #conteudoChat h1 {
                    font-weight: 600;
                    font-size: 1.8em;
                }
                #chat_ia #conteudoChat h2 {
                    font-weight: 600;
                    font-size: 1.6em;
                }
                #chat_ia #conteudoChat h3 {
                    font-weight: 600;
                    font-size: 1.4em;
                }
                #chat_ia #conteudoChat h4 {
                    font-weight: 600;
                    font-size: 1.2em;
                }
                #chat_ia #conteudoChat h5 {
                    font-weight: 600;
                    font-size: 1em;
                }
                #chat_ia #conteudoChat h6 {
                    font-weight: 300;
                    font-size: 1em;
                }
                #chat_ia #conteudoChat th {
                    background-color: rgba(0, 0, 0, .1);
                    border-bottom-width: 1px;
                    border-color: rgba(0, 0, 0, .15);
                    border-top-width: 1px;
                    padding: .25rem .75rem;
                }
                #chat_ia #conteudoChat td {
                    border-bottom-width: 1px;
                    border-color: rgba(0, 0, 0, .15);
                    padding: .25rem .75rem;
                }
                #chat_ia #conteudoChat table {
                    --tw-border-spacing-x: 0px;
                    --tw-border-spacing-y: 0px;
                    border-spacing: var(--tw-border-spacing-x) var(--tw-border-spacing-y);
                    margin-bottom: .25rem;
                    margin-top: .25rem;
                    width: 100%;
                    border-top-left-radius: .375rem;
                }
                #chat_ia #conteudoChat hr {
                    border-color: rgba(0,0,0,.25);
                    border-top: 0;
                }
                @media (max-width: 576px) {
                    #chat_ia .expandido .iconeIdentificacao {
                        display:none;
                    }
                    #chat_ia .expandido .interaction-container {
                        padding: 0 5px;
                    }
                    #chat_ia .expandido .acoes_assistente {
                        margin: 5px 30px 0 10px;
                    }
                    #chat_ia .expandido .acoes_assistente .estrelinha {
                        margin-left: 0;
                    }
                }
                #chat_ia .section-title {
                    font-size: 0.8rem;
                    font-weight: bold;
                    color: #6c757d;
                    margin-bottom: 8px;
                }
            </style>

        ';

        return $css;
    }

    public static function montarChat()
    {
        $retorno = self::montarHmlChat();
        $retorno .= '<link rel="stylesheet" type="text/css" href="modulos/ia/lib/highlight.js/atom-one-light.css" />';
        $retorno .= self::montarCssChat();

        return $retorno;
    }

    public static function montarHmlChat()
    {
        $strLinkOrientacoesGerais = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_ia_modal_orientacoes_gerais');
        $strLinkOrientacoesGerais = "'$strLinkOrientacoesGerais'";
        $strLinkConfiguracoes = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_ia_modal_configuracoes_assistente_ia');
        $strLinkConfiguracoes = "'$strLinkConfiguracoes'";
        $botao = '
           
            <div class="widget-chat" id="chat_ia" style="display: none">
                <button id="botaoTransferir" onclick="transferirTexto()">Transferir para Assistente de IA</button>    
                <input type="hidden" id="textoSelecionado">
                <input class="widget-input-open" id="widget-open" type="checkbox" name="widget-open-chat" />
                <div class="widget-button">
                    <svg onclick="fecharChat()"class="close-chat" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0z" fill="none"/><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
                    <img onclick="abrirChat()" class="ionicon chat-open" src="modulos/ia/imagens/md_ia_icone_branco.svg?11" alt="Inteligência Artificial">
                </div>
                <div class="widget-content">
                    <div class="widget-title">
                        <div id="tituloChat">
                            Assistente de IA
                        </div>
                        <div id="iconesExpandir" class="text-right">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" class="iconeAdicionarTopico" viewBox="0 0 512 512" onclick="adicionarTopico(this);">
                                <path fill="#ffffff" d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM232 344V280H168c-13.3 0-24-10.7-24-24s10.7-24 24-24h64V168c0-13.3 10.7-24 24-24s24 10.7 24 24v64h64c13.3 0 24 10.7 24 24s-10.7 24-24 24H280v64c0 13.3-10.7 24-24 24s-24-10.7-24-24z"/>
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg"  width="16" height="16" fill="currentColor" viewBox="0 0 512 512" onclick="openModal(' . $strLinkOrientacoesGerais . ')" class="iconeOrientacoesGerais">
                                <path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM216 336h24V272H216c-13.3 0-24-10.7-24-24s10.7-24 24-24h48c13.3 0 24 10.7 24 24v88h8c13.3 0 24 10.7 24 24s-10.7 24-24 24H216c-13.3 0-24-10.7-24-24s10.7-24 24-24zm40-208a32 32 0 1 1 0 64 32 32 0 1 1 0-64z"/>
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg"  width="16" height="16" fill="currentColor" viewBox="0 0 512 512" onclick="infraAbrirJanelaModal(' . $strLinkConfiguracoes . ', 800, 300)" class="iconeConfiguracoes">
                                <path d="M495.9 166.6c3.2 8.7 .5 18.4-6.4 24.6l-43.3 39.4c1.1 8.3 1.7 16.8 1.7 25.4s-.6 17.1-1.7 25.4l43.3 39.4c6.9 6.2 9.6 15.9 6.4 24.6c-4.4 11.9-9.7 23.3-15.8 34.3l-4.7 8.1c-6.6 11-14 21.4-22.1 31.2c-5.9 7.2-15.7 9.6-24.5 6.8l-55.7-17.7c-13.4 10.3-28.2 18.9-44 25.4l-12.5 57.1c-2 9.1-9 16.3-18.2 17.8c-13.8 2.3-28 3.5-42.5 3.5s-28.7-1.2-42.5-3.5c-9.2-1.5-16.2-8.7-18.2-17.8l-12.5-57.1c-15.8-6.5-30.6-15.1-44-25.4L83.1 425.9c-8.8 2.8-18.6 .3-24.5-6.8c-8.1-9.8-15.5-20.2-22.1-31.2l-4.7-8.1c-6.1-11-11.4-22.4-15.8-34.3c-3.2-8.7-.5-18.4 6.4-24.6l43.3-39.4C64.6 273.1 64 264.6 64 256s.6-17.1 1.7-25.4L22.4 191.2c-6.9-6.2-9.6-15.9-6.4-24.6c4.4-11.9 9.7-23.3 15.8-34.3l4.7-8.1c6.6-11 14-21.4 22.1-31.2c5.9-7.2 15.7-9.6 24.5-6.8l55.7 17.7c13.4-10.3 28.2-18.9 44-25.4l12.5-57.1c2-9.1 9-16.3 18.2-17.8C227.3 1.2 241.5 0 256 0s28.7 1.2 42.5 3.5c9.2 1.5 16.2 8.7 18.2 17.8l12.5 57.1c15.8 6.5 30.6 15.1 44 25.4l55.7-17.7c8.8-2.8 18.6-.3 24.5 6.8c8.1 9.8 15.5 20.2 22.1 31.2l4.7 8.1c6.1 11 11.4 22.4 15.8 34.3zM256 336a80 80 0 1 0 0-160 80 80 0 1 0 0 160z"/>
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 448 512"  onclick="expandirAssistente()" id="expandirAssistente">
                                <path d="M32 32C14.3 32 0 46.3 0 64v96c0 17.7 14.3 32 32 32s32-14.3 32-32V96h64c17.7 0 32-14.3 32-32s-14.3-32-32-32H32zM64 352c0-17.7-14.3-32-32-32s-32 14.3-32 32v96c0 17.7 14.3 32 32 32h96c17.7 0 32-14.3 32-32s-14.3-32-32-32H64V352zM320 32c-17.7 0-32 14.3-32 32s14.3 32 32 32h64v64c0 17.7 14.3 32 32 32s32-14.3 32-32V64c0-17.7-14.3-32-32-32H320zM448 352c0-17.7-14.3-32-32-32s-32 14.3-32 32v64H320c-17.7 0-32 14.3-32 32s14.3 32 32 32h96c17.7 0 32-14.3 32-32V352z"/>
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 448 512" onclick="reduzirAssistente()" id="reduzirAssistente">
                                <path d="M160 64c0-17.7-14.3-32-32-32s-32 14.3-32 32v64H32c-17.7 0-32 14.3-32 32s14.3 32 32 32h96c17.7 0 32-14.3 32-32V64zM32 320c-17.7 0-32 14.3-32 32s14.3 32 32 32H96v64c0 17.7 14.3 32 32 32s32-14.3 32-32V352c0-17.7-14.3-32-32-32H32zM352 64c0-17.7-14.3-32-32-32s-32 14.3-32 32v96c0 17.7 14.3 32 32 32h96c17.7 0 32-14.3 32-32s-14.3-32-32-32H352V64zM320 320c-17.7 0-32 14.3-32 32v96c0 17.7 14.3 32 32 32s32-14.3 32-32V384h64c17.7 0 32-14.3 32-32s-14.3-32-32-32H320z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="widget-container-dialog">
                        <div id="painelTopicos" class="row">
                        <div class="nav flex-column nav-pills" role="tablist" aria-orientation="vertical">
                            <a onclick="adicionarTopico(this);">
                                <button class="nav-link adicionarTopico" id="adicionarTopico" data-toggle="pill" data-target="#v-pills-home" type="button" role="tab" aria-controls="v-pills-home" aria-selected="true">Novo Tópico</button>
                            </a>
                            <div id="listagemTopicos"></div>
                        </div>
                        </div>
                        <div id="conteudoChat">
                            <div class="interaction-container" id="conversa"></div>
                            <div class="send-message">
                                <textarea class="send-message-input"  aria-describedby="validacaoMensagem" id="mensagem"></textarea>
                                <div class="botaoEnvioMensagem">
                                    <svg id="botaoEnviarMensagem" onclick="enviarMensagem(this);" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M4.01 6.03l7.51 3.22-7.52-1 .01-2.22m7.5 8.72L4 17.97v-2.22l7.51-1M2.01 3L2 10l15 2-15 2 .01 7L23 12 2.01 3z"/></svg>
                                    <img id="imgArvoreAguarde" src="' . PaginaSEI::getInstance()->getIconeAguardar() . '" width="24" height="24" />
                                </div>
                            </div>
                            <div class="wrapperMensagens">
	                            <div id="validacaoMensagem" class="invalid-feedback"></div>
	                            <div id="rodapeChat">Pode conter erros. Considere verificar informações importantes.</div>
							</div>
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" id="janelaContexto">
            <input type="hidden" id="topicoTemporario">
        ';

        return $botao;
    }

    public function enviarMensagemAssistenteIa($mensagem)
    {
        $objMdIaAdmConfigAssistIADTO = new MdIaAdmConfigAssistIADTO();
        $objMdIaAdmConfigAssistIADTO->retStrSystemPrompt();
        $objMdIaAdmConfigAssistIARN = new MdIaAdmConfigAssistIARN();
        $objMdIaAdmConfigAssistIADTO = $objMdIaAdmConfigAssistIARN->consultar($objMdIaAdmConfigAssistIADTO);

        $budget = MdIaConfigAssistenteINT::calcularConsumoDiarioToken();

        if ($budget["extrapolouLimiteTokens"]) {
            $retorno = [];
            $retorno["error"] = true;
            $retorno["mensagem"] = utf8_encode("O volume de conteúdo permitido nas interações diárias foi excedido. Tente novamente amanhã, quando o volume de conteúdo permitido para interação terá sido renovado.");
            return json_encode($retorno);
        }
        $systemPrompt = $objMdIaAdmConfigAssistIADTO->getStrSystemPrompt();

        $systemPrompt = str_replace('@descricao_orgao_origem@', SessaoSEI::getInstance()->getStrDescricaoOrgaoUsuario(), $systemPrompt);
        $systemPrompt = str_replace('@sigla_orgao_origem@', SessaoSEI::getInstance()->getStrSiglaOrgaoSistema(), $systemPrompt);

        $dadosMensagem = array();
        $dadosMensagem["text"] = addslashes(utf8_encode($mensagem["text"]));
        $dadosMensagem["id_usuario"] = SessaoSEI::getInstance()->getNumIdUsuario();
        $dadosMensagem["system_prompt"] = addslashes(utf8_encode($systemPrompt));
        $dadosMensagem["temperature"] = 0;

        if ($mensagem["relacaoProtocolos"] != "") {
            $procedimentos = array();
            $protocolos = json_decode($mensagem["relacaoProtocolos"]);

            $procedimentos["id_procedimento"] = $mensagem["idProcedimento"];
            $procedimentos["id_documentos"] = $protocolos;

            $dadosMensagem["id_procedimentos"] = array($procedimentos);
        } elseif ($mensagem["idDocumento"] != "") {
            $procedimentos = array();
            $procedimentos["id_procedimento"] = $mensagem["idProcedimento"];
            $procedimentos["id_documentos"] = array($mensagem["idDocumento"]);

            $dadosMensagem["id_procedimentos"] = array($procedimentos);
        }

        $objMdIaTopicoChatINT = new MdIaTopicoChatINT();

        if ($mensagem["topicoTemporario"] == "true") {
            $objMdIaTopicoChatINT->adicionarTopico();
        }

        if (!SessaoSEI::getInstance()->isSetAtributo('MD_IA_ID_TOPICO_CHAT_IA')) {
            $idTopico = $objMdIaTopicoChatINT->consultarUltimoTopico();
            $dadosMensagem["id_topico"] = intval($idTopico);
            SessaoSEI::getInstance()->setAtributo('MD_IA_ID_TOPICO_CHAT_IA', $idTopico);
        } else {
            $dadosMensagem["id_topico"] = intval(MdIaTopicoChatINT::verificaSessaoTopico());
        }

        $objMdIaInteracaoChatRN = new MdIaInteracaoChatRN();
        $objMdIaInteracaoChatDTO = new MdIaInteracaoChatDTO();
        $objMdIaInteracaoChatDTO->setNumIdMdIaTopicoChat($dadosMensagem["id_topico"]);
        $objMdIaInteracaoChatDTO->setStrPergunta($mensagem["text"]);
        $objMdIaInteracaoChatDTO->setStrInputPrompt(json_encode($dadosMensagem));
        $objMdIaInteracaoChatDTO->setStrLinkAcessoProcedimento($mensagem["linkAcesso"]);
        $objMdIaInteracaoChatDTO->setStrProcedimentoCitado($mensagem["procedimento"]);
        $objMdIaInteracaoChatDTO->setDthCadastro(InfraData::getStrDataHoraAtual());
        $interacao = $objMdIaInteracaoChatRN->cadastrar($objMdIaInteracaoChatDTO);

        self::executarConsultaWebService($interacao->getNumIdMdIaInteracaoChat());

        return $interacao->getNumIdMdIaInteracaoChat();
    }

    public function executarConsultaWebService($parametros)
    {
        $commandJob = 'php ' . dirname(__FILE__) . '/MdIaConsultaWebserviceINT.php ' . $parametros;
        $command = $commandJob . ' > /dev/null 2>&1 & echo $!';
        exec($command, $op);
    }

    public function consultarDisponibilidadeApi()
    {
        $objMdIaConfigAssistenteRN = new MdIaConfigAssistenteRN();
        $urlApi = $objMdIaConfigAssistenteRN->retornarUrlApi();
        $dadosMensagem = array();
        $dadosMensagem["url"] = $urlApi['urlBase'] . $urlApi["linkConsultaDisponibilidade"];
        $retornoMensagem = array();
        $retornoMensagem["retornoApi"] = $objMdIaConfigAssistenteRN->consultarDisponibilidadeApi($dadosMensagem["url"]);
        $retornoMensagem["janelaContexto"] = $urlApi['janelaContexto'];
        return $retornoMensagem;
    }

    public function calcularConsumoDiarioToken()
    {
        $retornoMensagem["extrapolouLimiteTokens"] = false;
        $retornoMensagem["quantidadeTokensUsados"] = 0;
        $retornoMensagem["tamanhoBudget"] = 0;

        $objMdIaAdmConfigAssistIARN = new MdIaAdmConfigAssistIARN();
        $objMdIaAdmConfigAssistIADTO = new MdIaAdmConfigAssistIADTO();
        $objMdIaAdmConfigAssistIADTO->retNumLimiteGeralTokens();
        $objMdIaAdmConfigAssistIADTO->retNumLimiteMaiorUsuariosTokens();
        $objMdIaAdmConfigAssistIADTO->setNumIdMdIaAdmConfigAssistIA(1);
        $configAssistIa = $objMdIaAdmConfigAssistIARN->consultar($objMdIaAdmConfigAssistIADTO);

        $objMdIaAdmCfgAssiIaUsuRN = new MdIaAdmCfgAssiIaUsuRN();
        $objMdIaAdmCfgAssiIaUsuDTO = new MdIaAdmCfgAssiIaUsuDTO();
        $objMdIaAdmCfgAssiIaUsuDTO->retNumIdMdIaAdmConfigAssistIA();
        $objMdIaAdmCfgAssiIaUsuDTO->setNumIdMdIaAdmCfgAssiIaUsu(1);
        $objMdIaAdmCfgAssiIaUsuDTO->setNumIdUsuario(SessaoSEI::getInstance()->getNumIdUsuario());
        $usuarioLimiteMaior = $objMdIaAdmCfgAssiIaUsuRN->consultar($objMdIaAdmCfgAssiIaUsuDTO);

        $objMdIaInteracaoChatBD = new MdIaInteracaoChatBD(BancoSEI::getInstance());
        $total_token_utilizado = $objMdIaInteracaoChatBD->calcularConsumoTokenDiario(SessaoSEI::getInstance()->getNumIdUsuario());

        if (!is_null($usuarioLimiteMaior)) {
            $retornoMensagem["tamanhoBudget"] = ($configAssistIa->getNumLimiteMaiorUsuariosTokens() * 1000000);
        } else {
            $retornoMensagem["tamanhoBudget"] = ($configAssistIa->getNumLimiteGeralTokens() * 1000000);
        }
        if ($total_token_utilizado >= $retornoMensagem["tamanhoBudget"]) {
            $retornoMensagem["extrapolouLimiteTokens"] = true;
        }
        $retornoMensagem["quantidadeTokensUsados"] = $total_token_utilizado;
        return $retornoMensagem;
    }

    public function enviarFeedbackProcessos($feedback)
    {
        $objMdIaConfigAssistenteRN = new MdIaConfigAssistenteRN();
        $urlApi = $objMdIaConfigAssistenteRN->retornarUrlApi();

        $urlEndpoint = $urlApi["urlBase"] . $urlApi["linkFeedback"];

        $dadosFeedback = array();
        $dadosFeedback["id_mensagem"] = $feedback["id_mensagem"];
        $dadosFeedback["stars"] = $feedback["stars"];
        $retornoMensagem = $objMdIaConfigAssistenteRN->enviarFeedbackProcessos(array(json_encode($dadosFeedback), $urlEndpoint));

        $objMdIaInteracaoChatRN = new MdIaInteracaoChatRN();
        $objMdIaInteracaoChatDTO = new MdIaInteracaoChatDTO();
        $objMdIaInteracaoChatDTO->retNumIdMdIaInteracaoChat();
        $objMdIaInteracaoChatDTO->setNumIdMessage($dadosFeedback["id_mensagem"]);
        $interacao = $objMdIaInteracaoChatRN->consultar($objMdIaInteracaoChatDTO);

        $interacao->setNumFeedback($dadosFeedback["stars"]);
        $objMdIaInteracaoChatRN->alterar($interacao);

        return $retornoMensagem[1];
    }

    public function consultaProtocoloDocumento($documento) {
        $objProtocoloDTO = new ProtocoloDTO();
        $objProtocoloDTO->retStrStaProtocolo();
        $objProtocoloDTO->retDblIdProtocolo();
        $objProtocoloDTO->retStrStaNivelAcessoGlobal();
        $objProtocoloDTO->retStrStaNivelAcessoLocal();
        $objProtocoloDTO->retDblIdProcedimentoDocumento();
        $objProtocoloDTO->setStrProtocoloFormatado($documento);
        $objProtocoloDTO = (new ProtocoloRN())->consultarRN0186($objProtocoloDTO);
        return $objProtocoloDTO;
    }

    public function consultaDocumento($documento) {
        $objDocumentoRN = new DocumentoRN();
        $objDocumentoDTO = new DocumentoDTO();
        $objDocumentoDTO->retDblIdDocumento();
        $objDocumentoDTO->retStrStaEstadoProtocolo();
        $objDocumentoDTO->retNumIdUnidadeGeradoraProtocolo();
        $objDocumentoDTO->retStrSinPublicado();
        $objDocumentoDTO->retStrSinAssinado();
        $objDocumentoDTO->retStrNomeArvore();
        $objDocumentoDTO->setStrProtocoloDocumentoFormatado($documento);
        $objDocumentoDTO = $objDocumentoRN->consultarRN0005($objDocumentoDTO);
        return $objDocumentoDTO;
    }
    public function consultaProtocolo($protocolo)
    {

        $documento = $paginas = null;

        // Verifica se quer pesquisar em um intervalo de paginas do documento
        if (preg_match('/#[0-9]+\[[0-9]+(:[0-9]+)?]/', $protocolo['documento'][0])) {
            $documento = substr(explode('[', $protocolo['documento'][0])[0], 1);
            $paginas = explode(':', substr(explode('[', $protocolo['documento'][0])[1], 0, -1));
        } else {
            $documento = substr($protocolo['documento'][0], 1);
        }
        if (!is_null($documento)) {
            try {
                $objProtocoloDTO = self::consultaProtocoloDocumento($documento);
                if (!is_null($objProtocoloDTO)) {
                    if ($objProtocoloDTO->getStrStaProtocolo() == ProtocoloRN::$TP_PROCEDIMENTO) {
                        return ["result" => "false", "mensagem" => utf8_encode("Provisoriamente, não é permitida a citação de protocolo de processo.")];
                        if ($objProtocoloDTO->getStrStaNivelAcessoGlobal() == ProtocoloRN::$NA_SIGILOSO) {
                            if (($protocolo["acao_origem"] != "usuario_validar_acesso" && $protocolo["acao_origem"] != "arvore_visualizar" && $protocolo["acao_origem"] != "procedimento_gerar")
                                || $protocolo["id_procedimento"] != $objProtocoloDTO->getDblIdProtocolo()) {
                                return ["result" => "false", "mensagem" => utf8_encode("Para interagir com o Assistente IA em documentos de Processos com o nível de acesso Sigiloso é necessário que você tenha o acesso e esteja dentro do processo desejado.")];
                            }
                        }

                        $arr = MdIaRecursoINT::listarDocumentosProcesso($objProtocoloDTO->getDblIdProtocolo());

                        $objProcedimentoDTO = $arr[0];

                        $arrObjRelProtocoloProtocoloDTO = $objProcedimentoDTO->getArrObjRelProtocoloProtocoloDTO();

                        $arrayProcessos = [];
                        $arrayProcessos[] = $arrObjRelProtocoloProtocoloDTO;
                        $objMdIaRecursoRN = new MdIaRecursoRN();

                        foreach ($arrObjRelProtocoloProtocoloDTO as $objRelProtocoloProtocoloDTO) {
                            if ($objRelProtocoloProtocoloDTO->getStrStaAssociacao() == RelProtocoloProtocoloRN::$TA_PROCEDIMENTO_ANEXADO) {

                                $objProcedimentoDTOAnexado = $objRelProtocoloProtocoloDTO->getObjProtocoloDTO2();

                                $arr = MdIaRecursoINT::listarDocumentosProcesso($objProcedimentoDTOAnexado->getDblIdProcedimento());

                                $objProcedimentoDTO = $arr[0];

                                $arrObjRelProtocoloProtocoloDTO = $objProcedimentoDTO->getArrObjRelProtocoloProtocoloDTO();
                                $arrayProcessos[] = $arrObjRelProtocoloProtocoloDTO;
                            }
                        }
                        foreach ($arrayProcessos as $processos) {
                            foreach ($processos as $documentoConsiderado) {
                                if ($documentoConsiderado->getStrStaAssociacao() == RelProtocoloProtocoloRN::$TA_DOCUMENTO_ASSOCIADO) {
                                    $objDocumentoDTO = $documentoConsiderado->getObjProtocoloDTO2();
                                    if ($objMdIaRecursoRN->verificarSelecaoDocumentoAlvo($objDocumentoDTO)) {
                                        $idProtocolo[] = $objDocumentoDTO->getDblIdDocumento();
                                        $protocolosConsiderados[] = $objDocumentoDTO->getStrProtocoloDocumentoFormatado();
                                    }
                                }
                            }
                        }
                        if ($objProtocoloDTO->getStrStaNivelAcessoGlobal() != ProtocoloRN::$NA_SIGILOSO) {
                            try {
                                $objEntradaConsultarProcedimentoAPI = new EntradaConsultarProcedimentoAPI();
                                $objEntradaConsultarProcedimentoAPI->setProtocoloProcedimento($documento);
                                $objSaidaConsultarProcedimentoAPI = (new SeiRN())->consultarProcedimento($objEntradaConsultarProcedimentoAPI);

                                $idProcesso = $objSaidaConsultarProcedimentoAPI->getIdProcedimento();
                                $linkAcesso = $objSaidaConsultarProcedimentoAPI->getLinkAcesso();
                            } catch (Exception $e) {

                            }
                        } else {
                            $idProcesso = $objProtocoloDTO->getDblIdProtocolo();
                            $linkAcesso = ConfiguracaoSEI::getInstance()->getValor('SEI', 'URL') . '/controlador.php?acao=procedimento_trabalhar&amp;id_procedimento=' . $objProtocoloDTO->getDblIdProtocolo();
                        }

                        if (!is_null($protocolosConsiderados)) {
                            return ["result" => "true", "idDocumento" => $idProcesso, "linkAcesso" => $linkAcesso, "relacaoProtocolos" => json_encode($idProtocolo), "idProcedimento" => $idProcesso];
                        } else {
                            return ["result" => "false", "mensagem" => utf8_encode("Unidade [" . SessaoSEI::getInstance()->getStrSiglaUnidadeAtual() . "] não possui acesso a nenhum documento do processo nº [" . $documento . "] ")];
                        }

                    } else {
                        if ($objProtocoloDTO->getStrStaNivelAcessoGlobal() != ProtocoloRN::$NA_SIGILOSO) {
                            $objEntradaConsultarDocumentoAPI = new EntradaConsultarDocumentoAPI();
                            $objEntradaConsultarDocumentoAPI->setProtocoloDocumento($documento);
                            $objSaidaConsultarDocumentoAPI = (new SeiRN())->consultarDocumento($objEntradaConsultarDocumentoAPI);

                            $idDocumento = $objSaidaConsultarDocumentoAPI->getIdDocumento();
                            $linkAcesso = $objSaidaConsultarDocumentoAPI->getLinkAcesso();
                            $idProcedimento = $objSaidaConsultarDocumentoAPI->getIdProcedimento();

                        } else {
                            if (($protocolo["acao_origem"] != "usuario_validar_acesso" && $protocolo["acao_origem"] != "arvore_visualizar" && $protocolo["acao_origem"] != "procedimento_gerar")
                                || $protocolo["id_procedimento"] != $objProtocoloDTO->getDblIdProcedimentoDocumento()) {
                                return ["result" => "false", "mensagem" => utf8_encode("Para interagir com o Assistente IA em documentos de Processos com o nível de acesso Sigiloso é necessário que você tenha o acesso e esteja dentro do processo desejado.")];
                            }

                            $objDocumentoDTO = self::consultaDocumento($documento);

                            $retornoPermissaoAcesso = MdIaRecursoRN::verificarSelecaoDocumentoAlvo($objDocumentoDTO);
                            if(!$retornoPermissaoAcesso) {
                                return ["result" => "false", "mensagem" => utf8_encode("Unidade [" . SessaoSEI::getInstance()->getStrSiglaUnidadeAtual() . "] não possui acesso ao documento [" . $documento . "].")];
                            }
                            $linkAcesso = ConfiguracaoSEI::getInstance()->getValor('SEI', 'URL') . '/controlador.php?acao=procedimento_trabalhar&amp;id_procedimento=' . $objProtocoloDTO->getDblIdProcedimentoDocumento() . '&amp;id_documento=' . $objProtocoloDTO->getDblIdProtocolo();
                            $idDocumento = $objProtocoloDTO->getDblIdProtocolo();
                            $idProcedimento = $objProtocoloDTO->getDblIdProcedimentoDocumento();

                        }
                        if ($objProtocoloDTO->getStrStaProtocolo() == ProtocoloRN::$TP_DOCUMENTO_GERADO) {
                            if (!is_null($paginas)) {
                                return ["result" => "false", "mensagem" => utf8_encode("O protocolo indicado se refere a Documento Gerado no SEI, que, por natureza, não aceita indicação de intervalo de páginas para interação sobre seu conteúdo com o Assistente de IA.")];
                            }
                        } elseif ($objProtocoloDTO->getStrStaProtocolo() == ProtocoloRN::$TP_DOCUMENTO_RECEBIDO) {

                            $arrExtensoesAceitas = ["pdf", "html", "htm", "txt", "ods", "xlsx", "csv", "xml", "odt", "odp", "doc", "docx", "json", "ppt", "pptx", "rtf", "xls", "xlsm"];

                            $objAnexoDTO = (new MdIaConfigAssistenteRN())->consultarAnexo($idDocumento);
                            $extensaoArquivo = pathinfo($objAnexoDTO->getStrNome(), PATHINFO_EXTENSION);
                            $extensaoArquivo = str_replace(' ', '', InfraString::transformarCaixaBaixa($extensaoArquivo));

                            if (in_array($extensaoArquivo, $arrExtensoesAceitas)) {
                                if (!is_null($paginas) && !in_array($extensaoArquivo, ['pdf'])) {
                                    return ["result" => "false", "mensagem" => utf8_encode("A indicação de intervalo de páginas sobre Documento Externo para interação com o Assistente de IA está restrita a documentos do tipo PDF.")];
                                }
                            } else {
                                return ["result" => "false", "mensagem" => utf8_encode("O protocolo indicado se refere a Documento Externo de arquivo com extensão não permitida para interação sobre seu conteúdo com o Assistente de IA.")];
                            }
                        }
                    }
                    return ["result" => "true", "idDocumento" => $idDocumento, "linkAcesso" => $linkAcesso, "idProcedimento" => $idProcedimento];
                } else {
                    return ["result" => "false", "mensagem" => utf8_encode("O protocolo citado #" . $documento . " não existe no SEI.")];
                }
            } catch (Exception $e) {
                if ($e->getArrObjInfraValidacao()) {
                    if (current($e->getArrObjInfraValidacao())->getStrDescricao()) {
                        return ["result" => "false", "mensagem" => utf8_encode(current($e->getArrObjInfraValidacao())->getStrDescricao())];
                    }
                } else {
                    return ["result" => "false", "mensagem" => utf8_encode($e->getMessage())];
                }
            }
        }
    }

    public function geraLogExcedeuJanelaContexto($dadosEnviados)
    {
        if ($dadosEnviados["protocolo"] == "false") {
            $protocoloIndicado = "";
        } else {
            $protocoloIndicado = $dadosEnviados["protocolo"];
        }

        $objMdIaConfigAssistenteRN = new MdIaConfigAssistenteRN();
        $urlApi = $objMdIaConfigAssistenteRN->retornarUrlApi();


        $log = "00001 - MENSAGEM AO ASSISTENTE DE IA BLOQUEADA POR ULTRAPASSAR JANELA DE CONTEXTO \n";
        $log .= "00002 - Usuario: " . SessaoSEI::getInstance()->getStrNomeUsuario() . " - Unidade: " . SessaoSEI::getInstance()->getStrSiglaUnidadeAtual() . " \n";
        $log .= "00003 - Endpoint do Recurso: " . $urlEndpoint = $urlApi["urlBase"] . $urlApi["linkEndpoint"] . " \n";
        $log .= "00004 - Tokens Enviados: " . $dadosEnviados["tokensEnviados"] . " \n";
        $log .= "00005 - Quantidade Máxima de Tokens Permitidos: " . $dadosEnviados["janelaContexto"] . " \n";
        $log .= "00006 - Protocolo Indicado: " . $protocoloIndicado . " \n";
        $log .= "00007 - FIM \n";
        LogSEI::getInstance()->gravar($log, InfraLog::$INFORMACAO);
        return array("result" => "true");
    }

    public function retornaMensagemAmigavelUsuario($statusRequisicao, $mensagemOriginal, $procedimentoCitado = "")
    {
        if ($statusRequisicao != "200" && $statusRequisicao != "406") {
            $mensagemErro = "Atenção: Ocorreu o erro abaixo na comunicação com a API da Inteligência Artificial.";
            switch ($statusRequisicao) {
                case '204':
                    $resposta = $mensagemErro . "<br> Erro: 204. RAG Não retornou nenhum documento similar.";
                    break;

                case '400':
                    $resposta = $mensagemErro . "<br> Erro: 400. Bad Request - Menção a mais de um documento não permitido para a intenção.";
                    break;

                case '401':
                    $resposta = $mensagemErro . "<br> Erro: 401. Erro de ao acessar o LLM remoto.";
                    break;

                case '404':
                    if($mensagemOriginal == '{"detail":"Documento nao encontrado"}') {
                        $resposta = $mensagemErro . "<br> Erro: 404. Documento não encontrado.";
                    } else{
                        $resposta = $mensagemErro . "<br> Erro: 404. Houve um problema de comunicação com o endpoint LLM.";
                    }
                    break;

                case '413':
                    $resposta = $mensagemErro . "<br> Erro: 413. Texto muito longo.";
                    break;

                case '422':
                    $resposta = $mensagemErro . "<br> Erro: 422. Requisição mal formatada.";
                    break;

                case '500':
                    $resposta = $mensagemErro . "<br> Erro: 500. Erro interno no servidor.";
                    break;

                case '501':
                    $resposta = $mensagemErro . "<br> Erro: 501. Erro interno no servidor.";
                    break;

                case '502':
                    if($procedimentoCitado == "") {
                        $resposta = $mensagemErro . "<br> Erro: 502. Limite de tempo excedido pelo servidor do LLM.";
                    } else {
                        $documento = $paginas = null;

                        // Verifica se quer pesquisar em um intervalo de paginas do documento
                        if (preg_match('/#[0-9]+\[[0-9]+(:[0-9]+)?]/', $procedimentoCitado)) {
                            $documento = substr(explode('[', $procedimentoCitado)[0], 1);
                            $paginas = explode(':', substr(explode('[', $procedimentoCitado)[1], 0, -1));
                        } else {
                            $documento = substr($procedimentoCitado, 1);
                        }

                        if (!is_null($documento)) {
                            try {
                                $objProtocoloDTO = self::consultaProtocoloDocumento($documento);

                                if ($objProtocoloDTO->getStrStaProtocolo() == ProtocoloRN::$TP_PROCEDIMENTO) {
                                    $resposta = $mensagemErro . "<br> Erro: 502. Limite de tempo excedido pelo servidor do LLM.";
                                } else {
                                    if ($objProtocoloDTO->getStrStaProtocolo() == ProtocoloRN::$TP_DOCUMENTO_RECEBIDO) {

                                        if ($objProtocoloDTO->getStrStaNivelAcessoGlobal() != ProtocoloRN::$NA_SIGILOSO) {
                                            $objEntradaConsultarDocumentoAPI = new EntradaConsultarDocumentoAPI();
                                            $objEntradaConsultarDocumentoAPI->setProtocoloDocumento($documento);
                                            $objSaidaConsultarDocumentoAPI = (new SeiRN())->consultarDocumento($objEntradaConsultarDocumentoAPI);

                                            $idDocumento = $objSaidaConsultarDocumentoAPI->getIdDocumento();

                                        } else {
                                            $idDocumento = $objProtocoloDTO->getDblIdProtocolo();
                                        }

                                        $objAnexoDTO = (new MdIaConfigAssistenteRN())->consultarAnexo($idDocumento);
                                        $extensaoArquivo = pathinfo($objAnexoDTO->getStrNome(), PATHINFO_EXTENSION);
                                        $extensaoArquivo = str_replace(' ', '', InfraString::transformarCaixaBaixa($extensaoArquivo));

                                        if ($extensaoArquivo == "pdf") {
                                            $resposta = $mensagemErro . "<br> Erro: 502. Limite de tempo excedido devido o alto volume do documento citado, recomendamos a utilização de paginação.";
                                        } else {
                                            $resposta = $mensagemErro . "<br> Erro: 502. Limite de tempo excedido devido o alto volume do documento citado.";
                                        }
                                    } else {
                                        $resposta = $mensagemErro . "<br> Erro: 502. Limite de tempo excedido devido o alto volume do documento citado.";
                                    }
                                }
                            } catch (Exception $e) {
                                var_dump($e);
                            }
                        }
                    }
                    break;

                case '503':
                    $resposta = $mensagemErro . "<br> Erro: 503. Error: Erro interno no servidor.";
                    break;

                case '504':
                    $resposta = $mensagemErro . "<br> Erro: 504. Erro interno no servidor.";
                    break;
            }
        } else {
            $resposta = $mensagemOriginal;
        }
        return $resposta;
    }

    public function consultarMensagem($dadosEnviados)
    {
        $objMdIaInteracaoChatRN = new MdIaInteracaoChatRN();
        $objMdIaInteracaoChatDTO = new MdIaInteracaoChatDTO();
        $objMdIaInteracaoChatDTO->setNumIdMdIaInteracaoChat($dadosEnviados["IdMdIaInteracaoChat"]);
        $objMdIaInteracaoChatDTO->retStrResposta();
        $objMdIaInteracaoChatDTO->retNumIdMessage();
        $objMdIaInteracaoChatDTO->retStrResposta();
        $objMdIaInteracaoChatDTO->retNumIdMessage();
        $objMdIaInteracaoChatDTO->retNumStatusRequisicao();
        $objMdIaInteracaoChatDTO->retStrProcedimentoCitado();
        $interacao = $objMdIaInteracaoChatRN->consultar($objMdIaInteracaoChatDTO);

        if (!is_null($interacao)) {
            if ($interacao->getNumStatusRequisicao() == "") {
                return array("result" => "false");
            } else {
                $resposta = self::retornaMensagemAmigavelUsuario($interacao->getNumStatusRequisicao(), $interacao->getStrResposta(), $interacao->getStrProcedimentoCitado());
                return array("result" => "true", "resposta" => utf8_encode($resposta), "id_mensagem" => $interacao->getNumIdMessage(), "status_requisicao" => $interacao->getNumStatusRequisicao());
            }
        } else {
            return array("result" => "false");
        }
    }
}