/*
 * This file has been compiled from: /modules/system/lang/pt-br/client.php
 */
if (!window.oc) {
    window.oc = {};
}

if (!window.oc.langMessages) {
    window.oc.langMessages = {};
}

window.oc.langMessages['pt-br'] = $.extend(
    window.oc.langMessages['pt-br'] || {},
    {"markdowneditor":{"formatting":"Formatando","quote":"Cita\u00e7\u00e3o","code":"C\u00f3digo","header1":"Cabe\u00e7alho 1","header2":"Cabe\u00e7alho 2","header3":"Cabe\u00e7alho 3","header4":"Cabe\u00e7alho 4","header5":"Cabe\u00e7alho 5","header6":"Cabe\u00e7alho 6","bold":"Negrito","italic":"It\u00e1lico","unorderedlist":"Lista n\u00e3o ordenada","orderedlist":"Lista ordenada","snippet":"Snippet","video":"V\u00eddeo","image":"Imagem","link":"Link","horizontalrule":"Inserir linha horizontal","fullscreen":"Tela cheia","preview":"Visualizar","strikethrough":"Strikethrough","cleanblock":"Clean Block","table":"Table","sidebyside":"Side by Side"},"mediamanager":{"insert_link":"Inserir link","insert_image":"Inserir imagem","insert_video":"Inserir v\u00eddeo","insert_audio":"Inserir \u00e1udio","invalid_file_empty_insert":"Por favor, selecione o arquivo para criar o link.","invalid_file_single_insert":"Por favor, selecione apenas um arquivo.","invalid_image_empty_insert":"Por favor, selecione as imagens que deseja inserir.","invalid_video_empty_insert":"Por favor, selecione os v\u00eddeos que deseja inserir.","invalid_audio_empty_insert":"Por favor, selecione os \u00e1udios que deseja inserir."},"alert":{"error":"Erro","confirm":"Confirmar","dismiss":"Dispensar","confirm_button_text":"OK","cancel_button_text":"Cancelar","widget_remove_confirm":"Remover este widget?"},"datepicker":{"previousMonth":"M\u00eas anterior","nextMonth":"Pr\u00f3ximo m\u00eas","months":["Janeiro","Fevereiro","Mar\u00e7o","Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro"],"weekdays":["Domingo","Segunda-feira","Ter\u00e7a-feira","Quarta-feira","Quinta-feira","Sexta-feira","S\u00e1bado"],"weekdaysShort":["Dom","Seg","Ter","Qua","Qui","Sex","Sab"]},"colorpicker":{"choose":"OK"},"filter":{"group":{"all":"todos"},"scopes":{"apply_button_text":"Aplicar","clear_button_text":"Limpar"},"dates":{"all":"todas","filter_button_text":"Filtro","reset_button_text":"Reiniciar","date_placeholder":"Data","after_placeholder":"Ap\u00f3s","before_placeholder":"Antes"},"numbers":{"all":"todas","filter_button_text":"Filtar","reset_button_text":"Reiniciar","min_placeholder":"Min","max_placeholder":"Max"}},"eventlog":{"show_stacktrace":"Exibir o rastreamento","hide_stacktrace":"Ocultar o rastreamento","tabs":{"formatted":"Formatado","raw":"Bruto"},"editor":{"title":"Editor de c\u00f3digo fonte","description":"Seu sistema operacional deve ser configurado para ouvir um desses esquemas de URL.","openWith":"Abrir com","remember_choice":"Lembrar a op\u00e7\u00e3o selecionada nesta sess\u00e3o","open":"Abrir","cancel":"Cancelar"}},"upload":{"max_files":"Voc\u00ea n\u00e3o pode enviar mais arquivos.","invalid_file_type":"Voc\u00ea n\u00e3o pode carregar arquivos deste tipo.","file_too_big":"O arquivo \u00e9 muito grande ({{filesize}}MB). Tamanho m\u00e1ximo do arquivo: {{maxFilesize}}MB.","response_error":"O servidor respondeu com o c\u00f3digo {{statusCode}}.","remove_file":"Remover arquivo"},"inspector":{"add":"Add","remove":"Remove","key":"Key","value":"Value","ok":"OK","cancel":"Cancel","items":"Items"}}
);


//! moment.js locale configuration v2.22.2

;(function (global, factory) {
   typeof exports === 'object' && typeof module !== 'undefined'
       && typeof require === 'function' ? factory(require('../moment')) :
   typeof define === 'function' && define.amd ? define(['../moment'], factory) :
   factory(global.moment)
}(this, (function (moment) { 'use strict';


    var ptBr = moment.defineLocale('pt-br', {
        months : 'janeiro_fevereiro_março_abril_maio_junho_julho_agosto_setembro_outubro_novembro_dezembro'.split('_'),
        monthsShort : 'jan_fev_mar_abr_mai_jun_jul_ago_set_out_nov_dez'.split('_'),
        weekdays : 'Domingo_Segunda-feira_Terça-feira_Quarta-feira_Quinta-feira_Sexta-feira_Sábado'.split('_'),
        weekdaysShort : 'Dom_Seg_Ter_Qua_Qui_Sex_Sáb'.split('_'),
        weekdaysMin : 'Do_2ª_3ª_4ª_5ª_6ª_Sá'.split('_'),
        weekdaysParseExact : true,
        longDateFormat : {
            LT : 'HH:mm',
            LTS : 'HH:mm:ss',
            L : 'DD/MM/YYYY',
            LL : 'D [de] MMMM [de] YYYY',
            LLL : 'D [de] MMMM [de] YYYY [às] HH:mm',
            LLLL : 'dddd, D [de] MMMM [de] YYYY [às] HH:mm'
        },
        calendar : {
            sameDay: '[Hoje às] LT',
            nextDay: '[Amanhã às] LT',
            nextWeek: 'dddd [às] LT',
            lastDay: '[Ontem às] LT',
            lastWeek: function () {
                return (this.day() === 0 || this.day() === 6) ?
                    '[Último] dddd [às] LT' : // Saturday + Sunday
                    '[Última] dddd [às] LT'; // Monday - Friday
            },
            sameElse: 'L'
        },
        relativeTime : {
            future : 'em %s',
            past : 'há %s',
            s : 'poucos segundos',
            ss : '%d segundos',
            m : 'um minuto',
            mm : '%d minutos',
            h : 'uma hora',
            hh : '%d horas',
            d : 'um dia',
            dd : '%d dias',
            M : 'um mês',
            MM : '%d meses',
            y : 'um ano',
            yy : '%d anos'
        },
        dayOfMonthOrdinalParse: /\d{1,2}º/,
        ordinal : '%dº'
    });

    return ptBr;

})));


/*! Select2 4.1.0-rc.0 | https://github.com/select2/select2/blob/master/LICENSE.md */

!function(){if(jQuery&&jQuery.fn&&jQuery.fn.select2&&jQuery.fn.select2.amd)var e=jQuery.fn.select2.amd;e.define("select2/i18n/pt-br",[],function(){return{errorLoading:function(){return"Os resultados não puderam ser carregados."},inputTooLong:function(e){var n=e.input.length-e.maximum,r="Apague "+n+" caracter";return 1!=n&&(r+="es"),r},inputTooShort:function(e){return"Digite "+(e.minimum-e.input.length)+" ou mais caracteres"},loadingMore:function(){return"Carregando mais resultados…"},maximumSelected:function(e){var n="Você só pode selecionar "+e.maximum+" ite";return 1==e.maximum?n+="m":n+="ns",n},noResults:function(){return"Nenhum resultado encontrado"},searching:function(){return"Buscando…"},removeAllItems:function(){return"Remover todos os itens"}}}),e.define,e.require}();

/*!
 * Froala Editor for October CMS
 */

(function (factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module.
        define(['jquery'], factory);
    } else if (typeof module === 'object' && module.exports) {
        // Node/CommonJS
        module.exports = function( root, jQuery ) {
            if ( jQuery === undefined ) {
                // require('jQuery') returns a factory that requires window to
                // build a jQuery instance, we normalize how we use modules
                // that require this pattern but the window provided is a noop
                // if it's defined (how jquery works)
                if ( typeof window !== 'undefined' ) {
                    jQuery = require('jquery');
                }
                else {
                    jQuery = require('jquery')(root);
                }
            }
            return factory(jQuery);
        };
    } else {
        // Browser globals
        factory(window.jQuery);
    }
}(function ($) {
/**
 * Portuguese spoken in Brazil
 */

if (!$.FE_LANGUAGE) {
    $.FE_LANGUAGE = {};
}

$.FE_LANGUAGE['pt_br'] = {
  translation: {
    // Place holder
    "Type something": "Digite algo",

    // Basic formatting
    "Bold": "Negrito",
    "Italic": "Itálito",
    "Underline": "Sublinhar",
    "Strikethrough": "Tachado",

    // Main buttons
    "Insert": "Inserir",
    "Delete": "Apagar",
    "Cancel": "Cancelar",
    "OK": "Ok",
    "Back": "Voltar",
    "Remove": "Remover",
    "More": "Mais",
    "Update": "Atualizar",
    "Style": "Estilo",

    // Font
    "Font Family": "Fonte",
    "Font Size": "Tamanho",

    // Colors
    "Colors": "Cores",
    "Background": "Fundo",
    "Text": "Texto",
    "HEX Color": "Cor hexadecimal",

    // Paragraphs
    "Paragraph Format": "Formatos",
    "Normal": "Normal",
    "Code": "Código",
    "Heading 1": "Cabeçalho 1",
    "Heading 2": "Cabeçalho 2",
    "Heading 3": "Cabeçalho 3",
    "Heading 4": "Cabeçalho 4",

    // Style
    "Paragraph Style": "Estilo de parágrafo",
    "Inline Style": "Estilo embutido",

    // Alignment
    "Align": "Alinhar",
    "Align Left": "Alinhar à esquerda",
    "Align Center": "Centralizar",
    "Align Right": "Alinhar à direita",
    "Align Justify": "Justificar",
    "None": "Nenhum",

    // Lists
    "Ordered List": "Lista ordenada",
    "Default": "Padrão",
    "Lower Alpha": "Alpha inferior",
    "Lower Greek": "Grego inferior",
    "Lower Roman": "Baixa romana",
    "Upper Alpha": "Alfa superior",
    "Upper Roman": "Romana superior",

    "Unordered List": "Lista não ordenada",
    "Circle": "Círculo",
    "Disc": "Disco",
    "Square": "Quadrado",

    // Line height
    "Line Height": "Altura da linha",
    "Single": "Solteiro",
    "Double": "Em dobro",

    // Indent
    "Decrease Indent": "Diminuir recuo",
    "Increase Indent": "Aumentar recuo",

    // Links
    "Insert Link": "Inserir link",
    "Open in new tab": "Abrir em uma nova aba",
    "Open Link": "Abrir link",
    "Edit Link": "Editar link",
    "Unlink": "Remover link",
    "Choose Link": "Escolha o link",

    // Images
    "Insert Image": "Inserir imagem",
    "Upload Image": "Carregar imagem",
    "By URL": "Por um endereço URL",
    "Browse": "Procurar",
    "Drop image": "Arraste sua imagem aqui",
    "or click": "ou clique aqui",
    "Manage Images": "Gerenciar imagens",
    "Loading": "Carregando",
    "Deleting": "Excluindo",
    "Tags": "Etiquetas",
    "Are you sure? Image will be deleted.": "Você tem certeza? A imagem será apagada.",
    "Replace": "Substituir",
    "Uploading": "Carregando imagem",
    "Loading image": "Carregando imagem",
    "Display": "Exibir",
    "Inline": "Em linha",
    "Break Text": "Texto de quebra",
    "Alternate Text": "Texto alternativo",
    "Change Size": "Alterar tamanho",
    "Width": "Largura",
    "Height": "Altura",
    "Something went wrong. Please try again.": "Algo deu errado. Por favor, tente novamente.",
    "Image Caption": "Legenda da imagem",
    "Advanced Edit": "Edição avançada",

    // Video
    "Insert Video": "Inserir vídeo",
    "Embedded Code": "Código embutido",
    "Paste in a video URL": "Colar um endereço de vídeo",
    "Drop video": "Solte o vídeo",
    "Your browser does not support HTML5 vídeo.": "Seu navegador não suporta vídeo em HTML5.",
    "Upload Video": "Carregar vídeo",

    // Tables
    "Insert Table": "Inserir tabela",
    "Table Header": "Cabeçalho da tabela",
    "Remove Table": "Remover tabela",
    "Table Style": "Estilo de tabela",
    "Horizontal Align": "Alinhamento horizontal",
    "Row": "Linha",
    "Insert row above": "Inserir linha antes",
    "Insert row below": "Inserir linha depois",
    "Delete row": "Excluir linha",
    "Column": "Coluna",
    "Insert column before": "Inserir coluna antes",
    "Insert column after": "Inserir coluna depois",
    "Delete column": "Excluir coluna",
    "Cell": "Célula",
    "Merge cells": "Agrupar células",
    "Horizontal split": "Divisão horizontal",
    "Vertical split": "Divisão vertical",
    "Cell Background": "Fundo da célula",
    "Vertical Align": "Alinhamento vertical",
    "Top": "Topo",
    "Middle": "Meio",
    "Bottom": "Fundo",
    "Align Top": "Alinhar topo",
    "Align Middle": "Alinhar meio",
    "Align Bottom": "Alinhar fundo",
    "Cell Style": "Estilo de célula",

    // Files
    "Upload File": "Carregar arquivo",
    "Drop file": "Arraste seu arquivo aqui",

    // Emoticons
    "Emoticons": "Emoticons",
    "Grinning face": "Rosto sorrindo",
    "Grinning face with smiling eyes": "Rosto sorrindo rosto com olhos sorridentes",
    "Face with tears of joy": "Rosto com lágrimas de alegria",
    "Smiling face with open mouth": "Rosto sorrindo com a boca aberta",
    "Smiling face with open mouth and smiling eyes": "Rosto sorrindo com a boca aberta e olhos sorridentes",
    "Smiling face with open mouth and cold sweat": "Rosto sorrindo com a boca aberta e suor frio",
    "Smiling face with open mouth and tightly-closed eyes": "Rosto sorrindo com a boca aberta e os olhos bem fechados",
    "Smiling face with halo": "Rosto sorrindo com aréola",
    "Smiling face with horns": "Rosto sorrindo com chifres",
    "Winking face": "Rosto piscando",
    "Smiling face with smiling eyes": "Rosto sorrindo com olhos sorridentes",
    "Face savoring delicious food": "Rosto saboreando uma deliciosa comida",
    "Relieved face": "Rosto aliviado",
    "Smiling face with heart-shaped eyes": "Rosto sorrindo com os olhos em forma de coração",
    "Smiling face with sunglasses": "Rosto sorrindo com óculos de sol",
    "Smirking face": "Rosto sorridente",
    "Neutral face": "Rosto neutro",
    "Expressionless face": "Rosto inexpressivo",
    "Unamused face": "Rosto sem expressão",
    "Face with cold sweat": "Rosto com suor frio",
    "Pensive face": "Rosto pensativo",
    "Confused face": "Rosto confuso",
    "Confounded face": "Rosto atônito",
    "Kissing face": "Rosto beijando",
    "Face throwing a kiss": "Rosto jogando um beijo",
    "Kissing face with smiling eyes": "Rosto beijando com olhos sorridentes",
    "Kissing face with closed eyes": "Rosto beijando com os olhos fechados",
    "Face with stuck out tongue": "Rosto com a língua para fora",
    "Face with stuck out tongue and winking eye": "Rosto com a língua para fora e um olho piscando",
    "Face with stuck out tongue and tightly-closed eyes": "Rosto com a língua para fora e os olhos bem fechados",
    "Disappointed face": "Rosto decepcionado",
    "Worried face": "Rosto preocupado",
    "Angry face": "Rosto irritado",
    "Pouting face": "Rosto com beicinho",
    "Crying face": "Rosto chorando",
    "Persevering face": "Rosto perseverante",
    "Face with look of triumph": "Rosto com olhar de triunfo",
    "Disappointed but relieved face": "Rosto decepcionado mas aliviado",
    "Frowning face with open mouth": "Rosto franzido com a boca aberta",
    "Anguished face": "Rosto angustiado",
    "Fearful face": "Rosto com medo",
    "Weary face": "Rosto cansado",
    "Sleepy face": "Rosto com sono",
    "Tired face": "Rosto cansado",
    "Grimacing face": "Rosto fazendo careta",
    "Loudly crying face": "Rosto chorando alto",
    "Face with open mouth": "Rosto com a boca aberta",
    "Hushed face": "Rosto silencioso",
    "Face with open mouth and cold sweat": "Rosto com a boca aferta e suando frio",
    "Face screaming in fear": "Rosto gritando de medo",
    "Astonished face": "Rosto surpreso",
    "Flushed face": "Rosto envergonhado",
    "Sleeping face": "Rosto dormindo",
    "Dizzy face": "Rosto tonto",
    "Face without mouth": "Rosto sem boca",
    "Face with medical mask": "Rosto com máscara médica",

    // Line breaker
    "Break": "Quebrar linha",

    // Math
    "Subscript": "Subscrito",
    "Superscript": "Sobrescrito",

    // Full screen
    "Fullscreen": "Tela cheia",

    // Horizontal line
    "Insert Horizontal Line": "Inserir linha horizontal",

    // Clear formatting
    "Clear Formatting": "Remover formatação",

    // Save
    "Save": "\u0053\u0061\u006c\u0076\u0065",

    // Undo, redo
    "Undo": "Desfazer",
    "Redo": "Refazer",

    // Select all
    "Select All": "Selecionar tudo",

    // Code view
    "Code View": "Exibir de código",

    // Quote
    "Quote": "Citação",
    "Increase": "Aumentar",
    "Decrease": "Diminuir",

    // Quick Insert
    "Quick Insert": "Inserção rápida",

    // Spcial Characters
    "Special Characters": "Caracteres especiais",
    "Latin": "Latino",
    "Greek": "Grego",
    "Cyrillic": "Cirílico",
    "Punctuation": "Pontuação",
    "Currency": "Moeda",
    "Arrows": "Setas",
    "Math": "Matemática",
    "Misc": "Misc",

    // Print.
    "Print": "Impressão",

    // Spell Checker.
    "Spell Checker": "Corretor ortográfico",

    // Help
    "Help": "Ajuda",
    "Shortcuts": "Atalhos",
    "Inline Editor": "Editor em linha",
    "Show the editor": "Mostre o editor",
    "Common actions": "Ações comuns",
    "Copy": "Cópia de",
    "Cut": "Cortar",
    "Paste": "Colar",
    "Basic Formatting": "Formatação básica",
    "Increase quote level": "Aumentar o nível de cotação",
    "Decrease quote level": "Diminuir o nível de cotação",
    "Image / Video": "Imagem / Vídeo",
    "Resize larger": "Redimensionar maior",
    "Resize smaller": "Redimensionar menor",
    "Table": "Tabela",
    "Select table cell": "Selecione a célula da tabela",
    "Extend selection one cell": "Ampliar a seleção de uma célula",
    "Extend selection one row": "Ampliar a seleção de uma linha",
    "Navigation": "Navegação",
    "Focus popup / toolbar": "Pop-up de foco / Barra de ferramentas",
    "Return focus to previous position": "Retornar o foco para a posição anterior",

    // Embed.ly
    "Embed URL": "URL de inserção",
    "Paste in a URL to embed": "Colar um endereço URL para incorporar",

    // Word Paste.
    "The pasted content is coming from a Microsoft Word document. Do you want to keep the format or clean it up?": "O conteúdo colado vem de um documento Microsoft Word. Você quer manter o formato ou limpá-lo?",
    "Keep": "Manter formatação",
    "Clean": "Limpar formatação",
    "Word Paste Detected": "Texto do Word detectado"
  },
  direction: "ltr"
};

}));

