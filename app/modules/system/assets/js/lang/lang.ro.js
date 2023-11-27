/*
 * This file has been compiled from: /modules/system/lang/ro/client.php
 */
if (!window.oc) {
    window.oc = {};
}

if (!window.oc.langMessages) {
    window.oc.langMessages = {};
}

window.oc.langMessages['ro'] = $.extend(
    window.oc.langMessages['ro'] || {},
    {"markdowneditor":{"formatting":"Formatare","quote":"Citat","code":"Cod","header1":"Antet 1","header2":"Antet 2","header3":"Antet 3","header4":"Antet 4","header5":"Antet 5","header6":"Antet 6","bold":"\u00cengro\u0219at","italic":"Italic","unorderedlist":"List\u0103 neordonat\u0103","orderedlist":"List\u0103 ordonat\u0103","snippet":"Snippet","video":"Video","image":"Imagine","link":"Link","horizontalrule":"Introdu linie orizontal\u0103","fullscreen":"Umple ecranul","preview":"Previzualizeaz\u0103","strikethrough":"Strikethrough","cleanblock":"Clean Block","table":"Table","sidebyside":"Side by Side"},"mediamanager":{"insert_link":"Introdu link","insert_image":"Introdu image","insert_video":"Introdu video","insert_audio":"Introdu audio","invalid_file_empty_insert":"Selecteaz\u0103 un fi\u0219ier c\u0103tre care s\u0103 se fac\u0103 leg\u0103tura.","invalid_file_single_insert":"Selecteaz\u0103 un singur fi\u0219ier.","invalid_image_empty_insert":"Alege imaginile pentru a fi introduse.","invalid_video_empty_insert":"Alege un fi\u0219ier video pentru a fi introdus.","invalid_audio_empty_insert":"Alege un fi\u0219ier video pentru a fi introdus."},"alert":{"error":"Error","confirm":"Confirm","dismiss":"Dismiss","confirm_button_text":"OK","cancel_button_text":"Anuleaz\u0103","widget_remove_confirm":"Remove this widget?"},"datepicker":{"previousMonth":"Previous Month","nextMonth":"Next Month","months":["January","February","March","April","May","June","July","August","September","October","November","December"],"weekdays":["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"],"weekdaysShort":["Sun","Mon","Tue","Wed","Thu","Fri","Sat"]},"colorpicker":{"choose":"OK"},"filter":{"group":{"all":"all"},"scopes":{"apply_button_text":"Apply","clear_button_text":"Clear"},"dates":{"all":"all","filter_button_text":"Filter","reset_button_text":"Reset","date_placeholder":"Date","after_placeholder":"After","before_placeholder":"Before"},"numbers":{"all":"all","filter_button_text":"Filter","reset_button_text":"Reset","min_placeholder":"Min","max_placeholder":"Max"}},"eventlog":{"show_stacktrace":"Show the stacktrace","hide_stacktrace":"Hide the stacktrace","tabs":{"formatted":"Formatted","raw":"Raw"},"editor":{"title":"Source Code Editor","description":"Your operating system should be configured to listen to one of these URL schemes.","openWith":"Open with","remember_choice":"Remember selected option for this session","open":"Open","cancel":"Cancel"}},"upload":{"max_files":"You can not upload any more files.","invalid_file_type":"You can't upload files of this type.","file_too_big":"File is too big ({{filesize}}MB). Max filesize: {{maxFilesize}}MB.","response_error":"Server responded with {{statusCode}} code.","remove_file":"Remove file"},"inspector":{"add":"Add","remove":"Remove","key":"Key","value":"Value","ok":"OK","cancel":"Cancel","items":"Items"}}
);


//! moment.js locale configuration v2.22.2

;(function (global, factory) {
   typeof exports === 'object' && typeof module !== 'undefined'
       && typeof require === 'function' ? factory(require('../moment')) :
   typeof define === 'function' && define.amd ? define(['../moment'], factory) :
   factory(global.moment)
}(this, (function (moment) { 'use strict';


    function relativeTimeWithPlural(number, withoutSuffix, key) {
        var format = {
                'ss': 'secunde',
                'mm': 'minute',
                'hh': 'ore',
                'dd': 'zile',
                'MM': 'luni',
                'yy': 'ani'
            },
            separator = ' ';
        if (number % 100 >= 20 || (number >= 100 && number % 100 === 0)) {
            separator = ' de ';
        }
        return number + separator + format[key];
    }

    var ro = moment.defineLocale('ro', {
        months : 'ianuarie_februarie_martie_aprilie_mai_iunie_iulie_august_septembrie_octombrie_noiembrie_decembrie'.split('_'),
        monthsShort : 'ian._febr._mart._apr._mai_iun._iul._aug._sept._oct._nov._dec.'.split('_'),
        monthsParseExact: true,
        weekdays : 'duminică_luni_marți_miercuri_joi_vineri_sâmbătă'.split('_'),
        weekdaysShort : 'Dum_Lun_Mar_Mie_Joi_Vin_Sâm'.split('_'),
        weekdaysMin : 'Du_Lu_Ma_Mi_Jo_Vi_Sâ'.split('_'),
        longDateFormat : {
            LT : 'H:mm',
            LTS : 'H:mm:ss',
            L : 'DD.MM.YYYY',
            LL : 'D MMMM YYYY',
            LLL : 'D MMMM YYYY H:mm',
            LLLL : 'dddd, D MMMM YYYY H:mm'
        },
        calendar : {
            sameDay: '[azi la] LT',
            nextDay: '[mâine la] LT',
            nextWeek: 'dddd [la] LT',
            lastDay: '[ieri la] LT',
            lastWeek: '[fosta] dddd [la] LT',
            sameElse: 'L'
        },
        relativeTime : {
            future : 'peste %s',
            past : '%s în urmă',
            s : 'câteva secunde',
            ss : relativeTimeWithPlural,
            m : 'un minut',
            mm : relativeTimeWithPlural,
            h : 'o oră',
            hh : relativeTimeWithPlural,
            d : 'o zi',
            dd : relativeTimeWithPlural,
            M : 'o lună',
            MM : relativeTimeWithPlural,
            y : 'un an',
            yy : relativeTimeWithPlural
        },
        week : {
            dow : 1, // Monday is the first day of the week.
            doy : 7  // The week that contains Jan 1st is the first week of the year.
        }
    });

    return ro;

})));


/*! Select2 4.1.0-rc.0 | https://github.com/select2/select2/blob/master/LICENSE.md */

!function(){if(jQuery&&jQuery.fn&&jQuery.fn.select2&&jQuery.fn.select2.amd)var e=jQuery.fn.select2.amd;e.define("select2/i18n/ro",[],function(){return{errorLoading:function(){return"Rezultatele nu au putut fi incărcate."},inputTooLong:function(e){var t=e.input.length-e.maximum,n="Vă rugăm să ștergeți"+t+" caracter";return 1!==t&&(n+="e"),n},inputTooShort:function(e){return"Vă rugăm să introduceți "+(e.minimum-e.input.length)+" sau mai multe caractere"},loadingMore:function(){return"Se încarcă mai multe rezultate…"},maximumSelected:function(e){var t="Aveți voie să selectați cel mult "+e.maximum;return t+=" element",1!==e.maximum&&(t+="e"),t},noResults:function(){return"Nu au fost găsite rezultate"},searching:function(){return"Căutare…"},removeAllItems:function(){return"Eliminați toate elementele"}}}),e.define,e.require}();

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
 * Romanian
 */

if (!$.FE_LANGUAGE) {
    $.FE_LANGUAGE = {};
}

$.FE_LANGUAGE['ro'] = {
  translation: {
    // Place holder
    "Type something": "Tasteaz\u0103 ceva",

    // Basic formatting
    "Bold": "\u00cengro\u015fat",
    "Italic": "Cursiv",
    "Underline": "Subliniat",
    "Strikethrough": "T\u0103iat",

    // Main buttons
    "Insert": "Insereaz\u0103",
    "Delete": "\u015eterge",
    "Cancel": "Anuleaz\u0103",
    "OK": "Ok",
    "Back": "\u00cenapoi",
    "Remove": "\u0218terge",
    "More": "Mai mult",
    "Update": "Actualizeaz\u0103",
    "Style": "Stil",

    // Font
    "Font Family": "Font",
    "Font Size": "Dimensiune font",

    // Colors
    "Colors": "Culoare",
    "Background": "Fundal",
    "Text": "Text",
    "HEX Color": "Culoare Hexa",

    // Paragraphs
    "Paragraph Format": "Format paragraf",
    "Normal": "Normal",
    "Code": "Cod",
    "Heading 1": "Antet 1",
    "Heading 2": "Antet 2",
    "Heading 3": "Antet 3",
    "Heading 4": "Antet 4",

    // Style
    "Paragraph Style": "Stil paragraf",
    "Inline Style": "Stil \u00een linie",

    // Alignment
    "Align": "Aliniere",
    "Align Left": "Aliniere la st\u00e2nga",
    "Align Center": "Aliniere la centru",
    "Align Right": "Aliniere la dreapta",
    "Align Justify": "Aliniere pe toat\u0103 l\u0103\u021bimea",
    "None": "Niciunul",

    // Lists
    "Ordered List": "List\u0103 ordonat\u0103",
    "Default": "Mod implicit",
    "Lower Alpha": "Inferior alfa",
    "Lower Greek": "Inferior grecesc",
    "Lower Roman": "Inferior roman",
    "Upper Alpha": "Alfa superioară",
    "Upper Roman": "Superior roman",

    "Unordered List": "List\u0103 neordonat\u0103",
    "Circle": "Cerc",
    "Disc": "Disc",
    "Square": "Pătrat",

    // Line height
    "Line Height": "Inaltimea liniei",
    "Single": "Singur",
    "Double": "Dubla",

    // Indent
    "Decrease Indent": "De-indenteaz\u0103",
    "Increase Indent": "Indenteaz\u0103",

    // Links
    "Insert Link": "Inserare link",
    "Open in new tab": "Deschide \u00EEn tab nou",
    "Open Link": "Deschide link",
    "Edit Link": "Editare link",
    "Unlink": "\u0218terge link-ul",
    "Choose Link": "Alege link",

    // Images
    "Insert Image": "Inserare imagine",
    "Upload Image": "\u00cencarc\u0103 imagine",
    "By URL": "Dup\u0103 URL",
    "Browse": "R\u0103sfoie\u0219te",
    "Drop image": "Trage imagine",
    "or click": "sau f\u0103 click",
    "Manage Images": "Gestionare imagini",
    "Loading": "Se \u00eencarc\u0103",
    "Deleting": "Se \u0219terge",
    "Tags": "Etichete",
    "Are you sure? Image will be deleted.": "Sunte\u021bi sigur? Imaginea va fi \u015ftears\u0103.",
    "Replace": "\u00cenlocuire",
    "Uploading": "Imaginea se \u00eencarc\u0103",
    "Loading image": "Imaginea se \u00eencarc\u0103",
    "Display": "Afi\u0219are",
    "Inline": "\u00cen linie",
    "Break Text": "Sparge text",
    "Alternative Text": "Text alternativ",
    "Change Size": "Modificare dimensiuni",
    "Width": "L\u0103\u021bime",
    "Height": "\u00cen\u0103l\u021bime",
    "Something went wrong. Please try again.": "Ceva n-a mers bine. V\u0103 rug\u0103m s\u0103 \u00eencerca\u021bi din nou.",
    "Image Caption": "Captura imaginii",
    "Advanced Edit": "Editare avansată",

    // Video
    "Insert Video": "Inserare video",
    "Embedded Code": "Cod embedded",
    "Paste in a video URL": "Lipiți o adresă URL pentru video",
    "Drop video": "Trage video",
    "Your browser does not support HTML5 video.": "Browserul dvs. nu acceptă videoclipul html5.",
    "Upload Video": "Încărcați videoclipul",

    // Tables
    "Insert Table": "Inserare tabel",
    "Table Header": "Antet tabel",
    "Remove Table": "\u0218terge tabel",
    "Table Style": "Stil tabel",
    "Horizontal Align": "Aliniere orizontal\u0103",
    "Row": "Linie",
    "Insert row above": "Insereaz\u0103 linie \u00eenainte",
    "Insert row below": "Insereaz\u0103 linie dup\u0103",
    "Delete row": "\u015eterge linia",
    "Column": "Coloan\u0103",
    "Insert column before": "Insereaz\u0103 coloan\u0103 \u00eenainte",
    "Insert column after": "Insereaz\u0103 coloan\u0103 dup\u0103",
    "Delete column": "\u015eterge coloana",
    "Cell": "Celula",
    "Merge cells": "Une\u015fte celulele",
    "Horizontal split": "\u00cemparte orizontal",
    "Vertical split": "\u00cemparte vertical",
    "Cell Background": "Fundal celul\u0103",
    "Vertical Align": "Aliniere vertical\u0103",
    "Top": "Sus",
    "Middle": "Mijloc",
    "Bottom": "Jos",
    "Align Top": "Aliniere sus",
    "Align Middle": "Aliniere la mijloc",
    "Align Bottom": "Aliniere jos",
    "Cell Style": "Stil celul\u0103",

    // Files
    "Upload File": "\u00cenc\u0103rca\u021bi fi\u0219ier",
    "Drop file": "Trage fi\u0219ier",

    // Emoticons
    "Emoticons": "Emoticoane",
    "Grinning face": "Fa\u021b\u0103 r\u00e2njind",
    "Grinning face with smiling eyes": "Fa\u021b\u0103 r\u00e2njind cu ochi z\u00e2mbitori",
    "Face with tears of joy": "Fa\u021b\u0103 cu lacrimi de bucurie",
    "Smiling face with open mouth": "Fa\u021b\u0103 z\u00e2mbitoare cu gura deschis\u0103",
    "Smiling face with open mouth and smiling eyes": "Fa\u021b\u0103 z\u00e2mbitoare cu gura deschis\u0103 \u0219i ochi z\u00e2mbitori",
    "Smiling face with open mouth and cold sweat": "Fa\u021b\u0103 z\u00e2mbitoare cu gura deschis\u0103 şi sudoare rece",
    "Smiling face with open mouth and tightly-closed eyes": "Fa\u021b\u0103 z\u00e2mbitoare cu gura deschis\u0103 şi ochii ferm \u00eenchi\u0219i",
    "Smiling face with halo": "Fa\u021b\u0103 z\u00e2mbitoare cu aur\u0103",
    "Smiling face with horns": "Fa\u021b\u0103 z\u00e2mbitoare cu coarne",
    "Winking face": "Fa\u021b\u0103 clipind",
    "Smiling face with smiling eyes": "Fa\u021b\u0103 z\u00e2mbitoare cu ochi z\u00e2mbitori",
    "Face savoring delicious food": "Fa\u021b\u0103 savur\u00e2nd preparate delicioase",
    "Relieved face": "Fa\u021b\u0103 u\u0219urat\u0103",
    "Smiling face with heart-shaped eyes": "Fa\u021b\u0103 z\u00e2mbitoare cu ochi in forma de inim\u0103",
    "Smiling face with sunglasses": "Fa\u021b\u0103 z\u00e2mbitoare cu ochelari de soare",
    "Smirking face": "Fa\u021b\u0103 cu sur\u00e2s afectat",
    "Neutral face": "Fa\u021b\u0103 neutr\u0103",
    "Expressionless face": "Fa\u021b\u0103 f\u0103r\u0103 expresie",
    "Unamused face": "Fa\u021b\u0103 neamuzat\u0103",
    "Face with cold sweat": "Fa\u021b\u0103 cu sudoare rece",
    "Pensive face": "Fa\u021b\u0103 medit\u00e2nd",
    "Confused face": "Fa\u021b\u0103 confuz\u0103",
    "Confounded face": "Fa\u021b\u0103 z\u0103p\u0103cit\u0103",
    "Kissing face": "Fa\u021b\u0103 s\u0103rut\u00e2nd",
    "Face throwing a kiss": "Fa\u021b\u0103 arunc\u00e2nd un s\u0103rut",
    "Kissing face with smiling eyes": "Fa\u021b\u0103 s\u0103rut\u00e2nd cu ochi z\u00e2mbitori",
    "Kissing face with closed eyes": "Fa\u021b\u0103 s\u0103rut\u00e2nd cu ochii \u00eenchi\u0219i",
    "Face with stuck out tongue": "Fa\u021b\u0103 cu limba afar\u0103",
    "Face with stuck out tongue and winking eye": "Fa\u021b\u0103 cu limba scoas\u0103 clipind",
    "Face with stuck out tongue and tightly-closed eyes": "Fa\u021b\u0103 cu limba scoas\u0103 \u0219i ochii ferm \u00eenchi\u0219i",
    "Disappointed face": "Fa\u021b\u0103 dezam\u0103git\u0103",
    "Worried face": "Fa\u021b\u0103 \u00eengrijorat\u0103",
    "Angry face": "Fa\u021b\u0103 nervoas\u0103",
    "Pouting face": "Fa\u021b\u0103 fierb\u00e2nd",
    "Crying face": "Fa\u021b\u0103 pl\u00e2ng\u00e2nd",
    "Persevering face": "Fa\u021b\u0103 perseverent\u0103",
    "Face with look of triumph": "Fa\u021b\u0103 triumf\u0103toare",
    "Disappointed but relieved face": "Fa\u021b\u0103 dezam\u0103git\u0103 dar u\u0219urat\u0103",
    "Frowning face with open mouth": "Fa\u021b\u0103 \u00eencruntat\u0103 cu gura deschis\u0103",
    "Anguished face": "Fa\u021b\u0103 \u00eendurerat\u0103",
    "Fearful face": "Fa\u021b\u0103 tem\u0103toare",
    "Weary face": "Fa\u021b\u0103 \u00eengrijorat\u0103",
    "Sleepy face": "Fa\u021b\u0103 adormit\u0103",
    "Tired face": "Fa\u021b\u0103 obosit\u0103",
    "Grimacing face": "Fa\u021b\u0103 cu grimas\u0103",
    "Loudly crying face": "Fa\u021b\u0103 pl\u00e2ng\u00e2nd zgomotos",
    "Face with open mouth": "Fa\u021b\u0103 cu gura deschis\u0103",
    "Hushed face": "Fa\u021b\u0103 discret\u0103",
    "Face with open mouth and cold sweat": "Fa\u021b\u0103 cu gura deschis\u0103 si sudoare rece",
    "Face screaming in fear": "Fa\u021b\u0103 \u021bip\u00e2nd de fric\u0103",
    "Astonished face": "Fa\u021b\u0103 uimit\u0103",
    "Flushed face": "Fa\u021b\u0103 sp\u0103lat\u0103",
    "Sleeping face": "Fa\u021b\u0103 adormit\u0103",
    "Dizzy face": "Fa\u021b\u0103 ame\u021bit\u0103",
    "Face without mouth": "Fa\u021b\u0103 f\u0103r\u0103 gur\u0103",
    "Face with medical mask": "Fa\u021b\u0103 cu masc\u0103 medical\u0103",

    // Line breaker
    "Break": "Desparte",

    // Horizontal line
    "Insert Horizontal Line": "Inserare linie orizontal\u0103",

    // Math
    "Subscript": "Indice",
    "Superscript": "Exponent",

    // Full screen
    "Fullscreen": "Ecran complet",

    // Clear formatting
    "Clear Formatting": "Elimina\u021bi formatarea",

    // Save
    "Save": "\u0053\u0061\u006c\u0076\u0061\u021b\u0069",

    // Undo, redo
    "Undo": "Reexecut\u0103",
    "Redo": "Dezexecut\u0103",

    // Select all
    "Select All": "Selecteaz\u0103 tot",

    // Code view
    "Code View": "Vizualizare cod",

    // Quote
    "Quote": "Citat",
    "Increase": "Indenteaz\u0103",
    "Decrease": "De-indenteaz\u0103",

    // Quick Insert
    "Quick Insert": "Inserare rapid\u0103",

    // Spcial Characters
    "Special Characters": "Caracterele speciale",
    "Latin": "Latină",
    "Greek": "Greacă",
    "Cyrillic": "Chirilic",
    "Punctuation": "Punctuaţie",
    "Currency": "Valută",
    "Arrows": "Săgeți",
    "Math": "Matematică",
    "Misc": "Diverse",

    // Print.
    "Print": "Imprimare",

    // Spell Checker.
    "Spell Checker": "Ortografie",

    // Help
    "Help": "Ajutor",
    "Shortcuts": "Comenzi rapide",
    "Inline Editor": "Editor inline",
    "Show the editor": "Arătați editorul",
    "Common actions": "Acțiuni comune",
    "Copy": "Copie",
    "Cut": "A taia",
    "Paste": "Lipire",
    "Basic Formatting": "Formatul de bază",
    "Increase quote level": "Creșteți nivelul cotației",
    "Decrease quote level": "Micșorați nivelul cotației",
    "Image / Video": "Imagine / video",
    "Resize larger": "Redimensionați mai mare",
    "Resize smaller": "Redimensionați mai puțin",
    "Table": "Tabel",
    "Select table cell": "Selectați celula tabelă",
    "Extend selection one cell": "Extindeți selecția la o celulă",
    "Extend selection one row": "Extindeți selecția cu un rând",
    "Navigation": "Navigare",
    "Focus popup / toolbar": "Focus popup / bara de instrumente",
    "Return focus to previous position": "Reveniți la poziția anterioară",

    // Embed.ly
    "Embed URL": "Încorporați url",
    "Paste in a URL to embed": "Lipiți un URL pentru a-l încorpora",

    // Word Paste.
    "The pasted content is coming from a Microsoft Word document. Do you want to keep the format or clean it up?": "Conținutul lipit vine dintr-un document word Microsoft. Doriți să păstrați formatul sau să îl curățați?",
    "Keep": "A pastra",
    "Clean": "Curat",
    "Word Paste Detected": "A fost detectată lipire din Word"
  },
  direction: "ltr"
};

}));

