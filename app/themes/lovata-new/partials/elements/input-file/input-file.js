export default new class inputFile {

  constructor () {
    this.handler();
    this.sizeMb = 10;
    this.allowFormatted = [
      "image/png",
      "image/jpeg",
      "application/vnd.rar",
      "application/vnd.ms-excel",
      "application/zip",
      "text/plain",
      "application/pdf"
    ];
    this.errors = {
      format: "неверный формат",
      size: `размер файла не может превышать ${this.sizeMb}Mb`
    };
  }

  handler () {
    this.eventChangeInput();
    this.eventDropInput();
    this.eventCloseLoadedFile();
  }

  eventChangeInput () {
    const inputsFile = document.querySelectorAll(".js-input-file-input");
    if (!inputsFile) return false;
    inputsFile.forEach(input => input.addEventListener("change", () => this.changeFile(input)));

  }

  eventDropInput () {
    const dropBoxes = document.querySelectorAll(".js-input-dropbox");
    if (!dropBoxes) return false;
    dropBoxes.forEach(dropBox => dropBox.addEventListener("ondrop", (e) => this.dropFile(dropBox, e)));

  }

  eventCloseLoadedFile () {
    const buttonCloseUploader = document.querySelectorAll(".js-file-close");
    buttonCloseUploader.forEach(button => {
      button.addEventListener("click", () => this.clearLoaderFile(button));
    });
  }

  clearLoaderFile (button) {
    const parentWrap = button.closest(".js-input-file");
    const attachmentWrap = parentWrap.querySelector(".js-input-attachment");
    parentWrap.querySelector(".js-input-file-input").value = "";
    parentWrap.querySelector(".js-file-wrap ").classList.remove("hidden");
    this.hideFileName(attachmentWrap);
  }

  dropFile (dropBox, event) {
    event.preventDefault();
    const fileInput = dropBox.parentNode.querySelector("input");
    fileInput.files = e.dataTransfer.files;
  }

  changeFile (element) {
    const file = element.files[0];
    console.log("file")
    console.log(file)
    this.setError(element);
    if (this.checkFileValidete(file, element)) return false;
    this.setAttachment(element, file);
  }

  checkFileValidete (file, element) {
    const errors = [];
    const format = this.checkFormat(file);
    const size = this.checkSize(file);
    if (format) errors.push(format);
    if (size) errors.push(size);
    if (errors.length > 0) {
      this.setError(element, errors);
      return true;
    } else {
      return false;
    }
  }

  setError (element, errors) {
    const errorWrap = element.closest(".js-input-file").querySelector(".js-error-text");
    errorWrap.innerText = errors ? errors.join(", ") : "";
    if (errors) {
      errorWrap.classList.remove("hidden");
    } else {
      errorWrap.classList.add("hidden");
    }
  }

  checkFormat (file) {
    return !!this.allowFormatted.find(el => el == file.type) ? false : this.errors.format;
  }

  checkSize (file) {
    return (file.size / 1024 / 1024) < this.sizeMb ? false : this.errors.size;
  }

  hideWrapButton (element) {
    element.closest(".js-input-file").querySelector(".js-file-wrap").classList.add("hidden");
  }

  showFileName (inputAttachment, file) {
    inputAttachment.querySelector(".js-name").innerText = file.name;
    inputAttachment.classList.remove("hidden");
  }

  hideFileName (inputAttachment) {
    inputAttachment.querySelector(".js-name").innerText = "";
    inputAttachment.classList.add("hidden");
  }

  setAttachment (element, file) {
    const attachmentElem = element.closest(".input-file").querySelector(".js-input-attachment");
    this.showFileName(attachmentElem, file);
    this.hideWrapButton(attachmentElem);
  }
};
