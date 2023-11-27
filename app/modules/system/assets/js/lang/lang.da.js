/*
 * This file has been compiled from: /modules/system/lang/da/client.php
 */
if (!window.oc) {
    window.oc = {};
}

if (!window.oc.langMessages) {
    window.oc.langMessages = {};
}

window.oc.langMessages['da'] = $.extend(
    window.oc.langMessages['da'] || {},
    {"markdowneditor":{"formatting":"Formatering","quote":"Citat","code":"Kode","header1":"Overskrift 1","header2":"Overskrift 2","header3":"Overskrift 3","header4":"Overskrift 4","header5":"Overskrift 5","header6":"Overskrift 6","bold":"Fed","italic":"Skr\u00e5","unorderedlist":"Usorteret Liste","orderedlist":"Nummereret Liste","snippet":"Snippet","video":"Video","image":"Billede","link":"Link","horizontalrule":"Inds\u00e6t horisontal streg","fullscreen":"Fuld sk\u00e6rm","preview":"Forh\u00e5ndsvisning","strikethrough":"Strikethrough","cleanblock":"Clean Block","table":"Table","sidebyside":"Side by Side"},"mediamanager":{"insert_link":"Inds\u00e6t Link","insert_image":"Inds\u00e6t Billede","insert_video":"Inds\u00e6t Video","insert_audio":"Inds\u00e6t Lyd","invalid_file_empty_insert":"V\u00e6lg venligst en fil, at inds\u00e6tte et link til.","invalid_file_single_insert":"V\u00e6lg venligst en enkel fil.","invalid_image_empty_insert":"V\u00e6lg venligst et eller flere billeder, at inds\u00e6tte.","invalid_video_empty_insert":"V\u00e6lg venligst en videofil, at inds\u00e6tte.","invalid_audio_empty_insert":"V\u00e6lg venligst en lydfil, at inds\u00e6tte."},"alert":{"error":"Error","confirm":"Confirm","dismiss":"Dismiss","confirm_button_text":"OK","cancel_button_text":"Fortryd","widget_remove_confirm":"Remove this widget?"},"datepicker":{"previousMonth":"Sidste M\u00e5ned","nextMonth":"N\u00e6ste M\u00e5ned","months":["Januar","Februar","Marts","April","Maj","Juni","Juli","August","September","Oktober","November","December"],"weekdays":["S\u00f8ndag","Mandag","Tirsdag","Onsdag","Torsdag","Fredag","L\u00f8rdag"],"weekdaysShort":["S\u00f8n","Man","Tir","Ons","Tor","Fre","L\u00f8r"]},"colorpicker":{"choose":"OK"},"filter":{"group":{"all":"Alle"},"scopes":{"apply_button_text":"Apply","clear_button_text":"Clear"},"dates":{"all":"alle","filter_button_text":"Filter","reset_button_text":"Nulstil","date_placeholder":"Dato","after_placeholder":"Efter","before_placeholder":"F\u00f8r"},"numbers":{"all":"all","filter_button_text":"Filter","reset_button_text":"Reset","min_placeholder":"Min","max_placeholder":"Max"}},"eventlog":{"show_stacktrace":"Vis stacktracen","hide_stacktrace":"Skjul stacktracen","tabs":{"formatted":"Formateret","raw":"R\u00e5"},"editor":{"title":"Kildekode redigeringsv\u00e6rkt\u00f8j","description":"Dit operativsystem b\u00f8r konfigureres til at lytte til et af disse URL-skemaer.","openWith":"\u00c5ben med","remember_choice":"Husk valgte mulighed for denne session","open":"\u00c5ben","cancel":"Fortryd"}},"upload":{"max_files":"You can not upload any more files.","invalid_file_type":"You can't upload files of this type.","file_too_big":"File is too big ({{filesize}}MB). Max filesize: {{maxFilesize}}MB.","response_error":"Server responded with {{statusCode}} code.","remove_file":"Remove file"},"inspector":{"add":"Add","remove":"Remove","key":"Key","value":"Value","ok":"OK","cancel":"Cancel","items":"Items"}}
);


//! moment.js locale configuration v2.22.2

;(function (global, factory) {
   typeof exports === 'object' && typeof module !== 'undefined'
       && typeof require === 'function' ? factory(require('../moment')) :
   typeof define === 'function' && define.amd ? define(['../moment'], factory) :
   factory(global.moment)
}(this, (function (moment) { 'use strict';


    var da = moment.defineLocale('da', {
        months : 'januar_februar_marts_april_maj_juni_juli_august_september_oktober_november_december'.split('_'),
        monthsShort : 'jan_feb_mar_apr_maj_jun_jul_aug_sep_okt_nov_dec'.split('_'),
        weekdays : 'søndag_mandag_tirsdag_onsdag_torsdag_fredag_lørdag'.split('_'),
        weekdaysShort : 'søn_man_tir_ons_tor_fre_lør'.split('_'),
        weekdaysMin : 'sø_ma_ti_on_to_fr_lø'.split('_'),
        longDateFormat : {
            LT : 'HH:mm',
            LTS : 'HH:mm:ss',
            L : 'DD.MM.YYYY',
            LL : 'D. MMMM YYYY',
            LLL : 'D. MMMM YYYY HH:mm',
            LLLL : 'dddd [d.] D. MMMM YYYY [kl.] HH:mm'
        },
        calendar : {
            sameDay : '[i dag kl.] LT',
            nextDay : '[i morgen kl.] LT',
            nextWeek : 'på dddd [kl.] LT',
            lastDay : '[i går kl.] LT',
            lastWeek : '[i] dddd[s kl.] LT',
            sameElse : 'L'
        },
        relativeTime : {
            future : 'om %s',
            past : '%s siden',
            s : 'få sekunder',
            ss : '%d sekunder',
            m : 'et minut',
            mm : '%d minutter',
            h : 'en time',
            hh : '%d timer',
            d : 'en dag',
            dd : '%d dage',
            M : 'en måned',
            MM : '%d måneder',
            y : 'et år',
            yy : '%d år'
        },
        dayOfMonthOrdinalParse: /\d{1,2}\./,
        ordinal : '%d.',
        week : {
            dow : 1, // Monday is the first day of the week.
            doy : 4  // The week that contains Jan 4th is the first week of the year.
        }
    });

    return da;

})));


/*! Select2 4.1.0-rc.0 | https://github.com/select2/select2/blob/master/LICENSE.md */

!function(){if(jQuery&&jQuery.fn&&jQuery.fn.select2&&jQuery.fn.select2.amd)var e=jQuery.fn.select2.amd;e.define("select2/i18n/da",[],function(){return{errorLoading:function(){return"Resultaterne kunne ikke indlæses."},inputTooLong:function(e){return"Angiv venligst "+(e.input.length-e.maximum)+" tegn mindre"},inputTooShort:function(e){return"Angiv venligst "+(e.minimum-e.input.length)+" tegn mere"},loadingMore:function(){return"Indlæser flere resultater…"},maximumSelected:function(e){var n="Du kan kun vælge "+e.maximum+" emne";return 1!=e.maximum&&(n+="r"),n},noResults:function(){return"Ingen resultater fundet"},searching:function(){return"Søger…"},removeAllItems:function(){return"Fjern alle elementer"}}}),e.define,e.require}();

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
 * Danish
 */

if (!$.FE_LANGUAGE) {
    $.FE_LANGUAGE = {};
}

$.FE_LANGUAGE['da'] = {
  translation: {
    // Place holder
    "Type something": "Skriv her",

    // Basic formatting
    "Bold": "Fed",
    "Italic": "Kursiv",
    "Underline": "Understreget",
    "Strikethrough": "Gennemstreget",

    // Main buttons
    "Insert": "Indsæt",
    "Delete": "Slet",
    "Cancel": "Fortryd",
    "OK": "Ok",
    "Back": "Tilbage",
    "Remove": "Fjern",
    "More": "Mere",
    "Update": "Opdater",
    "Style": "Udseende",

    // Font
    "Font Family": "Skrifttype",
    "Font Size": "Skriftstørrelse",

    // Colors
    "Colors": "Farver",
    "Background": "Baggrund",
    "Text": "Tekst",
    "HEX Color": "Hex farve",

    // Paragraphs
    "Paragraph Format": "Typografi",
    "Normal": "Normal",
    "Code": "Kode",
    "Heading 1": "Overskrift 1",
    "Heading 2": "Overskrift 2",
    "Heading 3": "Overskrift 3",
    "Heading 4": "Overskrift 4",

    // Style
    "Paragraph Style": "Afsnit",
    "Inline Style": "På linje",

    // Alignment
    "Align": "Tilpasning",
    "Align Left": "Venstrejusteret",
    "Align Center": "Centreret",
    "Align Right": "Højrejusteret",
    "Align Justify": "Justeret",
    "None": "Ingen",

    // Lists
    "Ordered List": "Punktopstilling",
    "Default": "Standard",
    "Lower Alpha": "Lavere alfa",
    "Lower Greek": "Lavere græsk",
    "Lower Roman": "Lavere romersk",
    "Upper Alpha": "Øvre alfa",
    "Upper Roman": "Øvre romersk",

    "Unordered List": "Punktopstilling med tal",
    "Circle": "Cirkel",
    "Disc": "Disk",
    "Square": "Firkant",

    // Line height
    "Line Height": "Linjehøjde",
    "Single": "Enkelt",
    "Double": "Dobbelt",

    // Indent
    "Decrease Indent": "Formindsk indrykning",
    "Increase Indent": "Forøg indrykning",

    // Links
    "Insert Link": "Indsæt link",
    "Open in new tab": "Åbn i ny fane",
    "Open Link": "Åbn link",
    "Edit Link": "Rediger link",
    "Unlink": "Fjern link",
    "Choose Link": "Vælg link",

    // Images
    "Insert Image": "Indsæt billede",
    "Upload Image": "Upload billede",
    "By URL": "Fra URL",
    "Browse": "Gennemse",
    "Drop image": "Træk billedet herind",
    "or click": "eller klik",
    "Manage Images": "Administrer billeder",
    "Loading": "Henter",
    "Deleting": "Sletter",
    "Tags": "Tags",
    "Are you sure? Image will be deleted.": "Er du sikker? Billedet vil blive slettet.",
    "Replace": "Udskift",
    "Uploading": "Uploader",
    "Loading image": "Henter billede",
    "Display": "Layout",
    "Inline": "På linje",
    "Break Text": "Ombryd tekst",
    "Alternative Text": "Supplerende tekst",
    "Change Size": "Tilpas størrelse",
    "Width": "Bredde",
    "Height": "Højde",
    "Something went wrong. Please try again.": "Noget gik galt. Prøv igen.",
    "Image Caption": "Billedtekst",
    "Advanced Edit": "Avanceret redigering",

    // Video
    "Insert Video": "Indsæt video",
    "Embedded Code": "Indlejret kode",
    "Paste in a video URL": "Indsæt en video via URL",
    "Drop video": "Træk videoen herind",
    "Your browser does not support HTML5 video.": "Din browser understøtter ikke HTML5 video.",
    "Upload Video": "Upload video",

    // Tables
    "Insert Table": "Indsæt tabel",
    "Table Header": "Tabeloverskrift",
    "Remove Table": "Fjern tabel",
    "Table Style": "Tabeludseende",
    "Horizontal Align": "Vandret tilpasning",
    "Row": "Række",
    "Insert row above": "Indsæt række over",
    "Insert row below": "Indsæt række under",
    "Delete row": "Slet række",
    "Column": "Kolonne",
    "Insert column before": "Indsæt kolonne før",
    "Insert column after": "Indsæt kolonne efter",
    "Delete column": "Slet kolonne",
    "Cell": "Celle",
    "Merge cells": "Flet celler",
    "Horizontal split": "Vandret split",
    "Vertical split": "Lodret split",
    "Cell Background": "Cellebaggrund",
    "Vertical Align": "Lodret tilpasning",
    "Top": "Top",
    "Middle": "Midte",
    "Bottom": "Bund",
    "Align Top": "Tilpas i top",
    "Align Middle": "Tilpas i midte",
    "Align Bottom": "Tilpas i bund",
    "Cell Style": "Celleudseende",

    // Files
    "Upload File": "Upload fil",
    "Drop file": "Træk filen herind",

    // Emoticons
    "Emoticons": "Humørikoner",
    "Grinning face": "Grinende ansigt",
    "Grinning face with smiling eyes": "Grinende ansigt med smilende øjne",
    "Face with tears of joy": "Ansigt med glædestårer",
    "Smiling face with open mouth": "Smilende ansigt med åben mund",
    "Smiling face with open mouth and smiling eyes": "Smilende ansigt med åben mund og smilende øjne",
    "Smiling face with open mouth and cold sweat": "Smilende ansigt med åben mund og koldsved",
    "Smiling face with open mouth and tightly-closed eyes": "Smilende ansigt med åben mund og stramtlukkede øjne",
    "Smiling face with halo": "Smilende ansigt med glorie",
    "Smiling face with horns": "Smilende ansigt med horn",
    "Winking face": "Blinkede ansigt",
    "Smiling face with smiling eyes": "Smilende ansigt med smilende øjne",
    "Face savoring delicious food": "Ansigt der savler over lækker mad",
    "Relieved face": "Lettet ansigt",
    "Smiling face with heart-shaped eyes": "Smilende ansigt med hjerteformede øjne",
    "Smiling face with sunglasses": "Smilende ansigt med solbriller",
    "Smirking face": "Smilende ansigt",
    "Neutral face": "Neutralt ansigt",
    "Expressionless face": "Udtryksløst ansigt",
    "Unamused face": "Utilfredst ansigt",
    "Face with cold sweat": "Ansigt med koldsved",
    "Pensive face": "Eftertænksomt ansigt",
    "Confused face": "Forvirret ansigt",
    "Confounded face": "Irriteret ansigt",
    "Kissing face": "Kyssende ansigt",
    "Face throwing a kiss": "Ansigt der luftkysser",
    "Kissing face with smiling eyes": "Kyssende ansigt med smilende øjne",
    "Kissing face with closed eyes": "Kyssende ansigt med lukkede øjne",
    "Face with stuck out tongue": "Ansigt med tungen ud af munden",
    "Face with stuck out tongue and winking eye": "Ansigt med tungen ud af munden og blinkede øje",
    "Face with stuck out tongue and tightly-closed eyes": "Ansigt med tungen ud af munden og stramt lukkede øjne",
    "Disappointed face": "Skuffet ansigt",
    "Worried face": "Bekymret ansigt",
    "Angry face": "Vredt ansigt",
    "Pouting face": "Surmulende ansigt",
    "Crying face": "Grædende ansigt",
    "Persevering face": "Vedholdende ansigt",
    "Face with look of triumph": "Hoverende ansigt",
    "Disappointed but relieved face": "Skuffet, men lettet ansigt",
    "Frowning face with open mouth": "Ansigt med åben mund og rynket pande",
    "Anguished face": "Forpintt ansigt",
    "Fearful face": "Angst ansigt",
    "Weary face": "Udmattet ansigt",
    "Sleepy face": "Søvnigt ansigt",
    "Tired face": "Træt ansigt",
    "Grimacing face": "Ansigt der laver en grimasse",
    "Loudly crying face": "Vrælende ansigt",
    "Face with open mouth": "Ansigt med åben mund",
    "Hushed face": "Tyst ansigt",
    "Face with open mouth and cold sweat": "Ansigt med åben mund og koldsved",
    "Face screaming in fear": "Ansigt der skriger i frygt",
    "Astonished face": "Forbløffet ansigt",
    "Flushed face": "Blussende ansigt",
    "Sleeping face": "Sovende ansigt",
    "Dizzy face": "Svimmelt ansigt",
    "Face without mouth": "Ansigt uden mund",
    "Face with medical mask": "Ansigt med mundbind",

    // Line breaker
    "Break": "Linjeskift",

    // Math
    "Subscript": "Sænket skrift",
    "Superscript": "Hævet skrift",

    // Full screen
    "Fullscreen": "Fuldskærm",

    // Horizontal line
    "Insert Horizontal Line": "Indsæt vandret linie",

    // Clear formatting
    "Clear Formatting": "Fjern formatering",

    // Undo, redo
    "Undo": "Fortryd",
    "Redo": "Annuller fortryd",

    // Select all
    "Select All": "Vælg alt",

    // Code view
    "Code View": "Kodevisning",

    // Quote
    "Quote": "Citat",
    "Increase": "Forøg",
    "Decrease": "Formindsk",

    // Quick Insert
    "Quick Insert": "Kvik-indsæt",

    // Spcial Characters
    "Special Characters": "Specialtegn",
    "Latin": "Latin",
    "Greek": "Græsk",
    "Cyrillic": "Kyrillisk",
    "Punctuation": "Tegnsætning",
    "Currency": "Valuta",
    "Arrows": "Pile",
    "Math": "Matematik",
    "Misc": "Diverse",

    // Print.
    "Print": "Print",

    // Spell Checker.
    "Spell Checker": "Stavekontrol",

    // Help
    "Help": "Hjælp",
    "Shortcuts": "Genveje",
    "Inline Editor": "Indlejret editor",
    "Show the editor": "Vis editor",
    "Common actions": "Almindelige handlinger",
    "Copy": "Kopier",
    "Cut": "Klip",
    "Paste": "Sæt ind",
    "Basic Formatting": "Grundlæggende formatering",
    "Increase quote level": "Hæv citatniveau",
    "Decrease quote level": "Sænk citatniveau",
    "Image / Video": "Billede / video",
    "Resize larger": "Ændre til større",
    "Resize smaller": "Ændre til mindre",
    "Table": "Tabel",
    "Select table cell": "Vælg tabelcelle",
    "Extend selection one cell": "Udvid markeringen med én celle",
    "Extend selection one row": "Udvid markeringen med én række",
    "Navigation": "Navigation",
    "Focus popup / toolbar": "Fokuser popup / værktøjslinje",
    "Return focus to previous position": "Skift fokus tilbage til tidligere position",

    // Embed.ly
    "Embed URL": "Integrer URL",
    "Paste in a URL to embed": "Indsæt en URL for at indlejre",

    // Word Paste.
    "The pasted content is coming from a Microsoft Word document. Do you want to keep the format or clean it up?": "Det indsatte indhold kommer fra et Microsoft Word-dokument. Vil du beholde formateringen eller fjerne den?",
    "Keep": "Behold",
    "Clean": "Fjern",
    "Word Paste Detected": "Indsættelse fra Word opdaget"
  },
  direction: "ltr"
};

}));

