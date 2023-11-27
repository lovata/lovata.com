/*
 * This file has been compiled from: /modules/system/lang/hu/client.php
 */
if (!window.oc) {
    window.oc = {};
}

if (!window.oc.langMessages) {
    window.oc.langMessages = {};
}

window.oc.langMessages['hu'] = $.extend(
    window.oc.langMessages['hu'] || {},
    {"markdowneditor":{"formatting":"Forr\u00e1sk\u00f3d","quote":"Id\u00e9zet","code":"K\u00f3d","header1":"C\u00edmsor 1","header2":"C\u00edmsor 2","header3":"C\u00edmsor 3","header4":"C\u00edmsor 4","header5":"C\u00edmsor 5","header6":"C\u00edmsor 6","bold":"F\u00e9lk\u00f6v\u00e9r","italic":"D\u00f6lt","unorderedlist":"Rendezett lista","orderedlist":"Sz\u00e1mozott lista","snippet":"Snippet","video":"Vide\u00f3","image":"K\u00e9p","link":"Hivatkoz\u00e1s","horizontalrule":"Vonal besz\u00far\u00e1sa","fullscreen":"Teljes k\u00e9perny\u0151","preview":"El\u0151n\u00e9zet","strikethrough":"Strikethrough","cleanblock":"Clean Block","table":"Table","sidebyside":"Side by Side"},"mediamanager":{"insert_link":"Hivatkoz\u00e1s besz\u00far\u00e1sa","insert_image":"K\u00e9p besz\u00far\u00e1sa","insert_video":"Vide\u00f3 besz\u00far\u00e1sa","insert_audio":"Audi\u00f3 besz\u00far\u00e1sa","invalid_file_empty_insert":"Hivatkoz\u00e1s k\u00e9sz\u00edt\u00e9s\u00e9hez jel\u00f6lj\u00f6n ki egy sz\u00f6vegr\u00e9szt.","invalid_file_single_insert":"K\u00e9rj\u00fck jel\u00f6lj\u00f6n ki egy f\u00e1jlt.","invalid_image_empty_insert":"V\u00e1lasszon ki legal\u00e1bb egy k\u00e9pet a besz\u00far\u00e1shoz.","invalid_video_empty_insert":"V\u00e1lasszon ki legal\u00e1bb egy vide\u00f3t a besz\u00far\u00e1shoz.","invalid_audio_empty_insert":"V\u00e1lasszon ki legal\u00e1bb egy audi\u00f3t a besz\u00far\u00e1shoz."},"alert":{"error":"Hiba","confirm":"Elfogad\u00e1s","dismiss":"Elutas\u00edt\u00e1s","confirm_button_text":"Igen","cancel_button_text":"M\u00e9gsem","widget_remove_confirm":"Val\u00f3ban t\u00f6r\u00f6lni akarja?"},"datepicker":{"previousMonth":"El\u0151z\u0151 h\u00f3nap","nextMonth":"K\u00f6vetkez\u0151 h\u00f3nap","months":["janu\u00e1r","febru\u00e1r","m\u00e1rcius","\u00e1prilis","m\u00e1jus","j\u00fanius","j\u00falius","augusztus","szeptember","okt\u00f3ber","november","december"],"weekdays":["vas\u00e1rnap","h\u00e9tf\u0151","kedd","szerda","cs\u00fct\u00f6rt\u00f6k","p\u00e9ntek","szombat"],"weekdaysShort":["va","h\u00e9","ke","sze","cs","p\u00e9","szo"]},"colorpicker":{"choose":"Ment\u00e9s"},"filter":{"group":{"all":"\u00f6sszes"},"scopes":{"apply_button_text":"Sz\u0171r\u00e9s","clear_button_text":"Alaphelyzet"},"dates":{"all":"\u00f6sszes","filter_button_text":"Sz\u0171r\u00e9s","reset_button_text":"Alaphelyzet","date_placeholder":"D\u00e1tum","after_placeholder":"Kezdete","before_placeholder":"V\u00e9ge"},"numbers":{"all":"\u00f6sszes","filter_button_text":"Sz\u0171r\u00e9s","reset_button_text":"Alaphelyzet","min_placeholder":"Minimum","max_placeholder":"Maximum"}},"eventlog":{"show_stacktrace":"R\u00e9szletek","hide_stacktrace":"Rejt\u00e9s","tabs":{"formatted":"Form\u00e1zott","raw":"T\u00f6m\u00f6r\u00edtett"},"editor":{"title":"Forr\u00e1sk\u00f3d szerkeszt\u0151","description":"Az oper\u00e1ci\u00f3s rendszert \u00fagy kell be\u00e1ll\u00edtani, hogy figyelembe vegye az URL s\u00e9m\u00e1t.","openWith":"Megnyit\u00e1s mint","remember_choice":"Kiv\u00e1lasztott be\u00e1ll\u00edt\u00e1sok megjegyz\u00e9se ebben a munkamenetben","open":"Megnyit\u00e1s","cancel":"M\u00e9gsem"}},"upload":{"max_files":"Nem t\u00f6lthet fel t\u00f6bb f\u00e1jlt.","invalid_file_type":"Ilyen t\u00edpus\u00fa f\u00e1jl nem t\u00f6lthet\u0151 fel.","file_too_big":"A f\u00e1jl m\u00e9rete t\u00fal nagy ({{filesize}}MB). Maximum ennyi lehet: {{maxFilesize}}MB.","response_error":"A szerver {{statusCode}} k\u00f3ddal t\u00e9rt vissza.","remove_file":"F\u00e1jl elt\u00e1vol\u00edt\u00e1sa"},"inspector":{"add":"Add","remove":"Remove","key":"Key","value":"Value","ok":"OK","cancel":"Cancel","items":"Items"}}
);


//! moment.js locale configuration v2.22.2

;(function (global, factory) {
   typeof exports === 'object' && typeof module !== 'undefined'
       && typeof require === 'function' ? factory(require('../moment')) :
   typeof define === 'function' && define.amd ? define(['../moment'], factory) :
   factory(global.moment)
}(this, (function (moment) { 'use strict';


    var weekEndings = 'vasárnap hétfőn kedden szerdán csütörtökön pénteken szombaton'.split(' ');
    function translate(number, withoutSuffix, key, isFuture) {
        var num = number;
        switch (key) {
            case 's':
                return (isFuture || withoutSuffix) ? 'néhány másodperc' : 'néhány másodperce';
            case 'ss':
                return num + (isFuture || withoutSuffix) ? ' másodperc' : ' másodperce';
            case 'm':
                return 'egy' + (isFuture || withoutSuffix ? ' perc' : ' perce');
            case 'mm':
                return num + (isFuture || withoutSuffix ? ' perc' : ' perce');
            case 'h':
                return 'egy' + (isFuture || withoutSuffix ? ' óra' : ' órája');
            case 'hh':
                return num + (isFuture || withoutSuffix ? ' óra' : ' órája');
            case 'd':
                return 'egy' + (isFuture || withoutSuffix ? ' nap' : ' napja');
            case 'dd':
                return num + (isFuture || withoutSuffix ? ' nap' : ' napja');
            case 'M':
                return 'egy' + (isFuture || withoutSuffix ? ' hónap' : ' hónapja');
            case 'MM':
                return num + (isFuture || withoutSuffix ? ' hónap' : ' hónapja');
            case 'y':
                return 'egy' + (isFuture || withoutSuffix ? ' év' : ' éve');
            case 'yy':
                return num + (isFuture || withoutSuffix ? ' év' : ' éve');
        }
        return '';
    }
    function week(isFuture) {
        return (isFuture ? '' : '[múlt] ') + '[' + weekEndings[this.day()] + '] LT[-kor]';
    }

    var hu = moment.defineLocale('hu', {
        months : 'január_február_március_április_május_június_július_augusztus_szeptember_október_november_december'.split('_'),
        monthsShort : 'jan_feb_márc_ápr_máj_jún_júl_aug_szept_okt_nov_dec'.split('_'),
        weekdays : 'vasárnap_hétfő_kedd_szerda_csütörtök_péntek_szombat'.split('_'),
        weekdaysShort : 'vas_hét_kedd_sze_csüt_pén_szo'.split('_'),
        weekdaysMin : 'v_h_k_sze_cs_p_szo'.split('_'),
        longDateFormat : {
            LT : 'H:mm',
            LTS : 'H:mm:ss',
            L : 'YYYY.MM.DD.',
            LL : 'YYYY. MMMM D.',
            LLL : 'YYYY. MMMM D. H:mm',
            LLLL : 'YYYY. MMMM D., dddd H:mm'
        },
        meridiemParse: /de|du/i,
        isPM: function (input) {
            return input.charAt(1).toLowerCase() === 'u';
        },
        meridiem : function (hours, minutes, isLower) {
            if (hours < 12) {
                return isLower === true ? 'de' : 'DE';
            } else {
                return isLower === true ? 'du' : 'DU';
            }
        },
        calendar : {
            sameDay : '[ma] LT[-kor]',
            nextDay : '[holnap] LT[-kor]',
            nextWeek : function () {
                return week.call(this, true);
            },
            lastDay : '[tegnap] LT[-kor]',
            lastWeek : function () {
                return week.call(this, false);
            },
            sameElse : 'L'
        },
        relativeTime : {
            future : '%s múlva',
            past : '%s',
            s : translate,
            ss : translate,
            m : translate,
            mm : translate,
            h : translate,
            hh : translate,
            d : translate,
            dd : translate,
            M : translate,
            MM : translate,
            y : translate,
            yy : translate
        },
        dayOfMonthOrdinalParse: /\d{1,2}\./,
        ordinal : '%d.',
        week : {
            dow : 1, // Monday is the first day of the week.
            doy : 4  // The week that contains Jan 4th is the first week of the year.
        }
    });

    return hu;

})));


/*! Select2 4.1.0-rc.0 | https://github.com/select2/select2/blob/master/LICENSE.md */

!function(){if(jQuery&&jQuery.fn&&jQuery.fn.select2&&jQuery.fn.select2.amd)var e=jQuery.fn.select2.amd;e.define("select2/i18n/hu",[],function(){return{errorLoading:function(){return"Az eredmények betöltése nem sikerült."},inputTooLong:function(e){return"Túl hosszú. "+(e.input.length-e.maximum)+" karakterrel több, mint kellene."},inputTooShort:function(e){return"Túl rövid. Még "+(e.minimum-e.input.length)+" karakter hiányzik."},loadingMore:function(){return"Töltés…"},maximumSelected:function(e){return"Csak "+e.maximum+" elemet lehet kiválasztani."},noResults:function(){return"Nincs találat."},searching:function(){return"Keresés…"},removeAllItems:function(){return"Távolítson el minden elemet"},removeItem:function(){return"Elem eltávolítása"},search:function(){return"Keresés"}}}),e.define,e.require}();

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
 * Hungarian
 */

if (!$.FE_LANGUAGE) {
    $.FE_LANGUAGE = {};
}

$.FE_LANGUAGE['hu'] = {
  translation: {
    // Place holder
    "Type something": "Szöveg...",

    // Basic formatting
    "Bold": "Félkövér",
    "Italic": "Dőlt",
    "Underline": "Aláhúzott",
    "Strikethrough": "Áthúzott",

    // Main buttons
    "Insert": "Beillesztés",
    "Delete": "Törlés",
    "Cancel": "Mégse",
    "OK": "Rendben",
    "Back": "Vissza",
    "Remove": "Eltávolítás",
    "More": "Több",
    "Update": "Frissítés",
    "Style": "Stílus",

    // Font
    "Font Family": "Betűtípus",
    "Font Size": "Betűméret",

    // Colors
    "Colors": "Színek",
    "Background": "Háttér",
    "Text": "Szöveg",
    "HEX Color": "HEX színkód",

    // Paragraphs
    "Paragraph Format": "Formátumok",
    "Normal": "Normál",
    "Code": "Kód",
    "Heading 1": "Címsor 1",
    "Heading 2": "Címsor 2",
    "Heading 3": "Címsor 3",
    "Heading 4": "Címsor 4",

    // Style
    "Paragraph Style": "Bekezdés stílusa",
    "Inline Style": " Helyi stílus",

    // Alignment
    "Align": "Igazítás",
    "Align Left": "Balra igazít",
    "Align Center": "Középre zár",
    "Align Right": "Jobbra igazít",
    "Align Justify": "Sorkizárás",
    "None": "Egyik sem",

    // Lists
    "Ordered List": "Számozás",
    "Default": "Alapértelmezett",
    "Lower Alpha": "Csökkenő alfa",
    "Lower Greek": "Csökkenő görög",
    "Lower Roman": "Csökkenő római",
    "Upper Alpha": "Növekvő alfa",
    "Upper Roman": "Növekvő római",

    "Unordered List": "Felsorolás",
    "Circle": "Kör",
    "Disc": "Korong",
    "Square": "Négyzet",

    // Line height
    "Line Height": "Vonal magassága",
    "Single": "Szimpla",
    "Double": "Dupla",

    // Indent
    "Decrease Indent": "Behúzás csökkentése",
    "Increase Indent": "Behúzás növelése",

    // Links
    "Insert Link": "Hivatkozás beillesztése",
    "Open in new tab": "Megnyitás új lapon",
    "Open Link": "Hivatkozás megnyitása",
    "Edit Link": "Hivatkozás szerkesztése",
    "Unlink": "Hivatkozás törlése",
    "Choose Link": "Keresés a lapok között",

    // Images
    "Insert Image": "Kép beillesztése",
    "Upload Image": "Kép feltöltése",
    "By URL": "Webcím megadása",
    "Browse": "Böngészés a Médiában",
    "Drop image": "Húzza ide a képet",
    "or click": "vagy kattintson ide",
    "Manage Images": "Képek kezelése",
    "Loading": "Betöltés...",
    "Deleting": "Törlés...",
    "Tags": "Címkék",
    "Are you sure? Image will be deleted.": "Biztos benne? A kép törlésre kerül.",
    "Replace": "Csere",
    "Uploading": "Feltöltés",
    "Loading image": "Kép betöltése",
    "Display": "Kijelző",
    "Inline": "Sorban",
    "Break Text": "Szöveg törése",
    "Alternative Text": "Alternatív szöveg",
    "Change Size": "Méret módosítása",
    "Width": "Szélesség",
    "Height": "Magasság",
    "Something went wrong. Please try again.": "Valami elromlott. Kérjük próbálja újra.",
    "Image Caption": "Képaláírás",
    "Advanced Edit": "Fejlett szerkesztés",

    // Video
    "Insert Video": "Videó beillesztése",
    "Embedded Code": "Kód bemásolása",
    "Paste in a video URL": "Illessze be a videó webcímét",
    "Drop video": "Húzza ide a videót",
    "Your browser does not support HTML5 video.": "A böngészője nem támogatja a HTML5 videókat.",
    "Upload Video": "Videó feltöltése",

    // Tables
    "Insert Table": "Táblázat beillesztése",
    "Table Header": "Táblázat fejléce",
    "Remove Table": "Tábla eltávolítása",
    "Table Style": "Táblázat stílusa",
    "Horizontal Align": "Vízszintes igazítás",
    "Row": "Sor",
    "Insert row above": "Sor beszúrása elé",
    "Insert row below": "Sor beszúrása mögé",
    "Delete row": "Sor törlése",
    "Column": "Oszlop",
    "Insert column before": "Oszlop beszúrása elé",
    "Insert column after": "Oszlop beszúrása mögé",
    "Delete column": "Oszlop törlése",
    "Cell": "Cella",
    "Merge cells": "Cellák egyesítése",
    "Horizontal split": "Vízszintes osztott",
    "Vertical split": "Függőleges osztott",
    "Cell Background": "Cella háttere",
    "Vertical Align": "Függőleges igazítás",
    "Top": "Felső",
    "Middle": "Középső",
    "Bottom": "Alsó",
    "Align Top": "Igazítsa felülre",
    "Align Middle": "Igazítsa középre",
    "Align Bottom": "Igazítsa alúlra",
    "Cell Style": "Cella stílusa",

    // Files
    "Upload File": "Fájl feltöltése",
    "Drop file": "Húzza ide a fájlt",

    // Emoticons
    "Emoticons": "Hangulatjelek",
    "Grinning face": "Vigyorgó arc",
    "Grinning face with smiling eyes": "Vigyorgó arc mosolygó szemekkel",
    "Face with tears of joy": "Arcon az öröm könnyei",
    "Smiling face with open mouth": "Mosolygó arc tátott szájjal",
    "Smiling face with open mouth and smiling eyes": "Mosolygó arc tátott szájjal és mosolygó szemek",
    "Smiling face with open mouth and cold sweat": "Mosolygó arc tátott szájjal és hideg veríték",
    "Smiling face with open mouth and tightly-closed eyes": "Mosolygó arc tátott szájjal és lehunyt szemmel",
    "Smiling face with halo": "Mosolygó arc dicsfényben",
    "Smiling face with horns": "Mosolygó arc szarvakkal",
    "Winking face": "Kacsintós arc",
    "Smiling face with smiling eyes": "Mosolygó arc mosolygó szemekkel",
    "Face savoring delicious food": "Ízletes ételek kóstolása",
    "Relieved face": "Megkönnyebbült arc",
    "Smiling face with heart-shaped eyes": "Mosolygó arc szív alakú szemekkel",
    "Smilin g face with sunglasses": "Mosolygó arc napszemüvegben",
    "Smirking face": "Vigyorgó arc",
    "Neutral face": "Semleges arc",
    "Expressionless face": "Kifejezéstelen arc",
    "Unamused face": "Unott arc",
    "Face with cold sweat": "Arcán hideg verejtékkel",
    "Pensive face": "Töprengő arc",
    "Confused face": "Zavaros arc",
    "Confounded face": "Rácáfolt arc",
    "Kissing face": "Csókos arc",
    "Face throwing a kiss": "Arcra dobott egy csókot",
    "Kissing face with smiling eyes": "Csókos arcán mosolygó szemek",
    "Kissing face with closed eyes": "Csókos arcán csukott szemmel",
    "Face with stuck out tongue": "Kinyújototta a nyelvét",
    "Face with stuck out tongue and winking eye": "Kinyújtotta a nyelvét és kacsintó szem",
    "Face with stuck out tongue and tightly-closed eyes": "Kinyújtotta a nyelvét és szorosan lehunyt szemmel",
    "Disappointed face": "Csalódott arc",
    "Worried face": "Aggódó arc",
    "Angry face": "Dühös arc",
    "Pouting face": "Duzzogó arc",
    "Crying face": "Síró arc",
    "Persevering face": "Kitartó arc",
    "Face with look of triumph": "Arcát diadalmas pillantást",
    "Disappointed but relieved face": "Csalódott, de megkönnyebbült arc",
    "Frowning face with open mouth": "Komor arc tátott szájjal",
    "Anguished face": "Gyötrődő arc",
    "Fearful face": "Félelmetes arc",
    "Weary face": "Fáradt arc",
    "Sleepy face": "Álmos arc",
    "Tired face": "Fáradt arc",
    "Grimacing face": "Elfintorodott arc",
    "Loudly crying face": "Hangosan síró arc",
    "Face with open mouth": "Arc nyitott szájjal",
    "Hushed face": "Csitított arc",
    "Face with open mouth and cold sweat": "Arc tátott szájjal és hideg veríték",
    "Face screaming in fear": "Sikoltozó arc a félelemtől",
    "Astonished face": "Meglepett arc",
    "Flushed face": "Kipirult arc",
    "Sleeping face": "Alvó arc",
    "Dizzy face": " Szádülő arc",
    "Face without mouth": "Arc nélküli száj",
    "Face with medical mask": "Arcán orvosi maszk",

    // Line breaker
    "Break": "Törés",

    // Math
    "Subscript": "Alsó index",
    "Superscript": "Felső index",

    // Full screen
    "Fullscreen": "Teljes képernyő",

    // Horizontal line
    "Insert Horizontal Line": "Vízszintes vonal",

    // Clear formatting
    "Clear Formatting": "Formázás eltávolítása",

    // Save
    "Save": "Mentés",

    // Undo, redo
    "Undo": "Visszavonás",
    "Redo": "Ismét",

    // Select all
    "Select All": "Minden kijelölése",

    // Code view
    "Code View": "Forráskód",

    // Quote
    "Quote": "Idézet",
    "Increase": "Növelés",
    "Decrease": "Csökkentés",

    // Quick Insert
    "Quick Insert": "Beillesztés",

    // Spcial Characters
    "Special Characters": "Speciális karakterek",
    "Latin": "Latin",
    "Greek": "Görög",
    "Cyrillic": "Cirill",
    "Punctuation": "Központozás",
    "Currency": "Valuta",
    "Arrows": "Nyilak",
    "Math": "Matematikai",
    "Misc": "Egyéb",

    // Print
    "Print": "Nyomtatás",

    // Spell Checker
    "Spell Checker": "Helyesírás-ellenőrző",

    // Help
    "Help": "Segítség",
    "Shortcuts": "Hivatkozások",
    "Inline Editor": "Inline szerkesztő",
    "Show the editor": "Mutassa a szerkesztőt",
    "Common actions": "Közös cselekvések",
    "Copy": "Másolás",
    "Cut": "Kivágás",
    "Paste": "Beillesztés",
    "Basic Formatting": "Alap formázás",
    "Increase quote level": "Növeli az idézet behúzását",
    "Decrease quote level": "Csökkenti az idézet behúzását",
    "Image / Video": "Kép / videó",
    "Resize larger": "Méretezés nagyobbra",
    "Resize smaller": "Méretezés kisebbre",
    "Table": "Asztal",
    "Select table cell": "Válasszon táblázat cellát",
    "Extend selection one cell": "Növelje meg egy sorral",
    "Extend selection one row": "Csökkentse egy sorral",
    "Navigation": "Navigáció",
    "Focus popup / toolbar": "Felugró ablak / eszköztár",
    "Return focus to previous position": "Visszaáll az előző pozícióra",

    // Embed.ly
    "Embed URL": "Beágyazott webcím",
    "Paste in a URL to embed": "Beilleszteni egy webcímet a beágyazáshoz",

    // Word Paste
    "The pasted content is coming from a Microsoft Word document. Do you want to keep the format or clean it up?": "A beillesztett tartalom egy Microsoft Word dokumentumból származik. Szeretné megtartani a formázását vagy sem?",
    "Keep": "Megtartás",
    "Clean": "Tisztítás",
    "Word Paste Detected": "Word beillesztés észlelhető",

    // October CMS
    "Insert Audio": "Audió beillesztése",
    "Insert File": "Fájl beillesztése"
  },
  direction: "ltr"
};

}));

