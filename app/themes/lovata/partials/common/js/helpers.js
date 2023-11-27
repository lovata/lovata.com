export default new class Helpers {
    /**
     * Detect Edge and Internet Explorer browsers
     */
    isMicrosoftBrowser() {
        let result = false;
        if (document.documentMode || /Edge/.test(navigator.userAgent)) {
            result = true;
        }
        return result;
    }
}